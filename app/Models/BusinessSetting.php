<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessSetting extends Model
{
    protected $fillable = ['business_name', 'whatsapp_number', 'pickup_address', 'pickup_maps_url', 'bank_name', 'bank_account_number', 'bank_account_name'];

    protected $hidden = ['bank_account_number', 'bank_account_name'];

    public function activeBatch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'active_batch_id');
    }
}
