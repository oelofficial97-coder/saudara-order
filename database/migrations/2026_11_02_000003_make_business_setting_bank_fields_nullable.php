<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->string('bank_name', 50)->nullable()->change();
            $table->string('bank_account_number', 50)->nullable()->change();
            $table->string('bank_account_name', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Refuse lossy rollback: never replace NULL with fake bank data or delete rows.
        if (DB::table('business_settings')->whereNull('bank_name')
            ->orWhereNull('bank_account_number')->orWhereNull('bank_account_name')->exists()) {
            throw new RuntimeException('Rollback ditolak: masih ada informasi rekening NULL. Tidak ada data yang diubah.');
        }

        Schema::table('business_settings', function (Blueprint $table) {
            $table->string('bank_name', 50)->nullable(false)->change();
            $table->string('bank_account_number', 50)->nullable(false)->change();
            $table->string('bank_account_name', 100)->nullable(false)->change();
        });
    }
};
