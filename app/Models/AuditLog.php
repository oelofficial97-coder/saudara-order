<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['*'];

    protected $hidden = ['old_values', 'new_values', 'ip_address', 'user_agent'];

    protected function casts(): array
    {
        return ['old_values' => 'array', 'new_values' => 'array', 'created_at' => 'immutable_datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $log): void {
            // Audit snapshots allow domain changes only, excluding credentials/tokens/PII.
            $allowed = array_flip([
                'product_id', 'batch_id', 'active_batch_id', 'order_id', 'payment_proof_id',
                'capacity_pouches', 'allocated_pouches', 'quantity_pouches',
                'price_rupiah', 'unit_price_rupiah', 'total_rupiah', 'amount_rupiah',
                'status', 'payment_status', 'payment_method', 'payment_channel', 'type',
                'order_source', 'is_open_for_orders', 'is_active', 'role',
                'estimated_ready_at', 'started_at', 'completed_at', 'confirmed_at',
                'cancelled_at', 'verified_at', 'decided_at', 'transacted_at',
            ]);
            foreach (['old_values', 'new_values'] as $field) {
                if ($log->$field !== null) {
                    $log->$field = array_filter(
                        array_intersect_key($log->$field, $allowed),
                        fn ($value) => is_scalar($value) || $value === null,
                    );
                }
            }
        });

        // Model guard only: direct SQL/query builder require restricted DB access.
        static::updating(fn () => throw new \LogicException('Audit log tidak dapat diubah.'));
        static::deleting(fn () => throw new \LogicException('Audit log tidak dapat dihapus.'));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'batch_id');
    }
}
