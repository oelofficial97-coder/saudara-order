<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'slug', 'flavor_name', 'description', 'photo_path', 'price_rupiah', 'unit', 'is_active'];

    protected function casts(): array
    {
        return ['price_rupiah' => 'integer', 'is_active' => 'boolean'];
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class);
    }
}
