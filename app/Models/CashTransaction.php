<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    protected $guarded = ['*'];

    protected $hidden = ['action_key', 'notes'];

    protected function casts(): array
    {
        return ['amount_rupiah' => 'integer', 'transacted_at' => 'immutable_datetime'];
    }

    protected static function booted(): void
    {
        // Model guard only: direct SQL/query builder require restricted DB access.
        static::updating(fn () => throw new \LogicException('Catat koreksi sebagai transaksi baru.'));
        static::deleting(fn () => throw new \LogicException('Transaksi kas tidak dapat dihapus.'));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentProof(): BelongsTo
    {
        return $this->belongsTo(PaymentProof::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
