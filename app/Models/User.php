<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class, 'recorded_by');
    }

    public function uploadedPaymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class, 'uploaded_by');
    }

    public function verifiedPaymentProofs(): HasMany
    {
        return $this->hasMany(PaymentProof::class, 'verified_by');
    }

    public function decidedCancellationRequests(): HasMany
    {
        return $this->hasMany(CancellationRequest::class, 'decided_by');
    }
}
