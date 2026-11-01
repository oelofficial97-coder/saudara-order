<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function checks(): array
    {
        return [
            'products' => [
                'chk_products_price' => 'price_rupiah > 0',
                'chk_products_unit' => "unit = 'pouch'",
                'chk_products_singleton' => 'singleton_key = 1',
            ],
            'business_settings' => ['chk_settings_singleton' => 'singleton_key = 1'],
            'production_batches' => [
                'chk_batches_capacity' => 'capacity_pouches > 0',
                'chk_batches_allocation' => 'allocated_pouches >= 0 AND allocated_pouches <= capacity_pouches',
            ],
            'orders' => [
                'chk_orders_quantity' => 'quantity_pouches > 0',
                'chk_orders_price' => 'unit_price_rupiah > 0',
                'chk_orders_total' => 'total_rupiah = quantity_pouches * unit_price_rupiah',
                'chk_orders_allocation' => "(status IN ('dikonfirmasi', 'diproduksi', 'siap_diambil', 'selesai') AND allocated_pouches = quantity_pouches) OR (status IN ('menunggu_konfirmasi', 'ditolak', 'batal') AND allocated_pouches = 0)",
                'chk_orders_token' => "tracking_token_hash REGEXP '^[0-9a-f]{64}$'",
            ],
            'payment_proofs' => [
                'chk_proofs_size' => 'file_size_bytes > 0 AND file_size_bytes <= 5242880',
                'chk_proofs_mime' => "mime_type IN ('image/jpeg', 'image/png')",
            ],
            'cash_transactions' => ['chk_cash_amount' => 'amount_rupiah > 0'],
        ];
    }

    public function up(): void
    {
        // SQLite tests cover relationships, not MySQL-specific CHECK enforcement.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->checks() as $table => $checks) {
            foreach ($checks as $name => $expression) {
                DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$name} CHECK ({$expression})");
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach (array_reverse($this->checks()) as $table => $checks) {
            foreach ($checks as $name => $expression) {
                DB::statement("ALTER TABLE {$table} DROP CHECK {$name}");
            }
        }
    }
};
