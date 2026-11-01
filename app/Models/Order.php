<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    // Prices, tokens, quantity, allocation and statuses require explicit trusted assignment.
    protected $fillable = ['customer_name', 'customer_phone', 'customer_notes'];

    protected $hidden = ['tracking_token_hash', 'idempotency_key'];

    protected function casts(): array
    {
        return [
            'quantity_pouches' => 'integer', 'unit_price_rupiah' => 'integer',
            'total_rupiah' => 'integer', 'allocated_pouches' => 'integer',
            'tracking_token_created_at' => 'immutable_datetime', 'estimated_ready_at' => 'immutable_datetime',
            'confirmed_at' => 'immutable_datetime', 'completed_at' => 'immutable_datetime', 'cancelled_at' => 'immutable_datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'batch_id');
    }

    public function paymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }

    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function cancellationRequests(): HasMany
    {
        return $this->hasMany(CancellationRequest::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
