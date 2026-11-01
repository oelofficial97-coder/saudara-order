<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('singleton_key')->default(1)->unique();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('flavor_name');
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->unsignedBigInteger('price_rupiah')->default(5000);
            $table->string('unit', 20)->default('pouch');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('batch_code', 50)->unique();
            $table->string('batch_name', 100);
            $table->unsignedInteger('capacity_pouches');
            $table->unsignedInteger('allocated_pouches')->default(0);
            $table->boolean('is_open_for_orders')->default(true);
            $table->dateTime('estimated_ready_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['is_open_for_orders', 'completed_at']);
        });

        // Create the referenced batch first, avoiding circular migration dependencies.
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('singleton_key')->default(1)->unique();
            $table->string('business_name', 100)->default('Keripik Singkong 4 Saudara');
            $table->string('whatsapp_number', 25);
            $table->text('pickup_address');
            $table->string('pickup_maps_url', 500)->nullable();
            $table->string('bank_name', 50);
            $table->string('bank_account_number', 50);
            $table->string('bank_account_name', 100);
            $table->foreignId('active_batch_id')->nullable()->unique()->constrained('production_batches')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('production_batches')->restrictOnDelete();
            $table->string('order_code', 50)->unique();
            $table->string('idempotency_key', 64)->unique();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 25);
            $table->enum('order_source', ['website', 'whatsapp', 'langsung', 'titipan'])->default('website');
            $table->unsignedInteger('quantity_pouches');
            $table->unsignedBigInteger('unit_price_rupiah')->default(5000);
            $table->unsignedBigInteger('total_rupiah');
            $table->enum('payment_method', ['transfer', 'bayar_saat_ambil']);
            $table->enum('status', ['menunggu_konfirmasi', 'dikonfirmasi', 'diproduksi', 'siap_diambil', 'selesai', 'ditolak', 'batal'])->default('menunggu_konfirmasi');
            $table->enum('payment_status', ['belum_lunas', 'menunggu_verifikasi', 'lunas', 'perlu_pengembalian', 'dikembalikan'])->default('belum_lunas');
            $table->unsignedInteger('allocated_pouches')->default(0);
            $table->char('tracking_token_hash', 64)->unique();
            $table->timestamp('tracking_token_created_at');
            $table->dateTime('estimated_ready_at')->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['batch_id', 'status']);
            $table->index('created_at');
            $table->index('customer_phone');
            $table->index(['order_source', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['payment_status', 'created_at']);
        });

        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedInteger('file_size_bytes');
            $table->string('mime_type', 50);
            $table->enum('status', ['menunggu_verifikasi', 'diterima', 'ditolak'])->default('menunggu_verifikasi');
            $table->text('admin_notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_proof_id')->nullable()->unique()->constrained()->restrictOnDelete();
            $table->enum('type', ['penerimaan', 'pengembalian']);
            $table->unsignedBigInteger('amount_rupiah');
            $table->enum('payment_channel', ['transfer_bank', 'tunai']);
            $table->string('action_key', 64)->unique();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('transacted_at');
            $table->timestamps();
            $table->index('transacted_at');
            $table->index(['type', 'transacted_at']);
        });

        Schema::create('cancellation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->text('reason');
            $table->enum('status', ['menunggu_persetujuan', 'disetujui', 'ditolak'])->default('menunggu_persetujuan');
            $table->text('admin_notes')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
            $table->index('status');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('production_batches')->restrictOnDelete();
            $table->string('event', 60);
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['event', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'cancellation_requests', 'cash_transactions', 'payment_proofs', 'orders', 'business_settings', 'production_batches', 'products'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
