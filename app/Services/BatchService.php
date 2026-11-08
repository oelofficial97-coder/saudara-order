<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BatchService
{
    private function settings(): ?BusinessSetting
    {
        // All batch writers use the same lock order: settings, batch, then orders.
        return BusinessSetting::where('singleton_key', 1)->lockForUpdate()->first();
    }

    private function fields(array $data): array
    {
        return [
            'batch_name' => $data['batch_name'],
            'capacity_pouches' => (int) $data['capacity_pouches'],
            'estimated_ready_at' => empty($data['estimated_ready_at']) ? null
                : CarbonImmutable::createFromFormat('!Y-m-d\TH:i', $data['estimated_ready_at'], 'Asia/Jakarta')->utc(),
            'notes' => $data['notes'] ?? null,
        ];
    }

    private function audit(User $actor, ProductionBatch $batch, string $event, array $before = []): void
    {
        $log = new AuditLog;
        $log->forceFill([
            'user_id' => $actor->id, 'batch_id' => $batch->id, 'event' => $event,
            'description' => match ($event) {
                'batch.created' => 'Batch produksi dibuat dengan penerimaan tertutup.',
                'batch.updated' => 'Informasi batch produksi diperbarui.',
                'batch.opened' => 'Penerimaan pengajuan batch dibuka.',
                default => 'Penerimaan pengajuan batch ditutup.',
            },
            'old_values' => $before, 'new_values' => $batch->getAttributes(),
        ])->save();
    }

    public function create(User $actor, array $data): ProductionBatch
    {
        Gate::forUser($actor)->authorize('manage-batches');

        return DB::transaction(function () use ($actor, $data) {
            $this->settings();
            $product = Product::where('singleton_key', 1)->where('is_active', true)->first();
            if (! $product) {
                throw ValidationException::withMessages(['batch_name' => 'Produk aktif belum tersedia. Jalankan seeder produk terlebih dahulu.']);
            }
            $batch = new ProductionBatch($this->fields($data));
            $batch->product_id = $product->id;
            $batch->batch_code = 'BATCH-'.Str::ulid();
            $batch->is_open_for_orders = false;
            $batch->allocated_pouches = 0;
            $batch->save();
            $this->audit($actor, $batch, 'batch.created');

            return $batch;
        }, 3);
    }

    public function update(User $actor, ProductionBatch $batch, array $data): void
    {
        Gate::forUser($actor)->authorize('manage-batches');
        DB::transaction(function () use ($actor, $batch, $data) {
            $this->settings();
            $batch = ProductionBatch::whereKey($batch->id)->lockForUpdate()->firstOrFail();
            if ($batch->completed_at) {
                throw ValidationException::withMessages(['batch_name' => 'Batch selesai tidak dapat diubah.']);
            }
            $fields = $this->fields($data);
            if ($batch->is_open_for_orders && (! $fields['estimated_ready_at'] || $fields['estimated_ready_at']->isPast())) {
                throw ValidationException::withMessages(['estimated_ready_at' => 'Batch terbuka harus memiliki estimasi siap di masa mendatang.']);
            }
            $allocation = max($batch->allocated_pouches, (int) $batch->orders()
                ->whereIn('status', ['dikonfirmasi', 'diproduksi', 'siap_diambil', 'selesai'])->sum('quantity_pouches'));
            if ($fields['capacity_pouches'] < $allocation) {
                throw ValidationException::withMessages(['capacity_pouches' => 'Kapasitas tidak boleh lebih kecil dari alokasi pesanan.']);
            }
            $started = $batch->started_at || $batch->orders()->whereIn('status', ['diproduksi', 'siap_diambil', 'selesai'])->exists();
            if ($started && $fields['capacity_pouches'] !== $batch->capacity_pouches) {
                throw ValidationException::withMessages(['capacity_pouches' => 'Kapasitas terkunci karena produksi sudah dimulai.']);
            }
            $before = $batch->getAttributes();
            $batch->fill($fields)->save();
            $this->audit($actor, $batch, 'batch.updated', $before);
        }, 3);
    }

    public function setOpen(User $actor, ProductionBatch $batch, bool $open): void
    {
        Gate::forUser($actor)->authorize('manage-batches');
        DB::transaction(function () use ($actor, $batch, $open) {
            $settings = $this->settings();
            $batch = ProductionBatch::whereKey($batch->id)->lockForUpdate()->firstOrFail();
            if ($batch->completed_at) {
                throw ValidationException::withMessages(['batch' => 'Batch selesai tidak dapat dibuka atau ditutup kembali.']);
            }
            if ($open) {
                if (! $settings) {
                    throw ValidationException::withMessages(['batch' => 'Lengkapi Pengaturan Usaha terlebih dahulu sebelum membuka penerimaan.']);
                }
                if (($settings->active_batch_id && $settings->active_batch_id !== $batch->id)
                    || ProductionBatch::where('is_open_for_orders', true)->where('id', '!=', $batch->id)->exists()) {
                    throw ValidationException::withMessages(['batch' => 'Tutup batch penerimaan yang lain terlebih dahulu. Hanya satu batch boleh terbuka.']);
                }
                if (! $batch->product->is_active || ! $batch->estimated_ready_at || $batch->estimated_ready_at->isPast()) {
                    throw ValidationException::withMessages(['batch' => 'Produk harus aktif dan estimasi siap harus berada di masa mendatang.']);
                }
                if ($batch->capacity_pouches <= $batch->allocated_pouches) {
                    throw ValidationException::withMessages(['batch' => 'Kuota batch sudah penuh.']);
                }
            } elseif ($batch->orders()->where('status', 'menunggu_konfirmasi')->exists()) {
                throw ValidationException::withMessages(['batch' => 'Putuskan seluruh pesanan menunggu konfirmasi sebelum menutup penerimaan.']);
            }

            $before = $batch->getAttributes();
            $changed = $batch->is_open_for_orders !== $open;
            $batch->is_open_for_orders = $open;
            $batch->save();
            if ($settings) {
                if ($open) {
                    $settings->active_batch_id = $batch->id;
                } elseif ($settings->active_batch_id === $batch->id) {
                    $settings->active_batch_id = null;
                }
                $settings->save();
            }
            if ($changed) {
                $this->audit($actor, $batch, $open ? 'batch.opened' : 'batch.closed', $before);
            }
        }, 3);
    }
}
