<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductionBatch extends Model
{
    // Allocation and state are assigned explicitly by trusted transactional services.
    protected $fillable = ['product_id', 'batch_code', 'batch_name', 'capacity_pouches', 'estimated_ready_at', 'notes'];

    protected function casts(): array
    {
        return ['capacity_pouches' => 'integer', 'allocated_pouches' => 'integer', 'is_open_for_orders' => 'boolean', 'estimated_ready_at' => 'immutable_datetime', 'started_at' => 'immutable_datetime', 'completed_at' => 'immutable_datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'batch_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'batch_id');
    }

    public function activeBusinessSetting(): HasOne
    {
        return $this->hasOne(BusinessSetting::class, 'active_batch_id');
    }
}
