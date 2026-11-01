<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\BusinessSetting;
use App\Models\CancellationRequest;
use App\Models\CashTransaction;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseFoundationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Each test owns a new in-memory DB. No refresh/reset command is needed.
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->seed(DatabaseSeeder::class);
    }

    private function batch(): ProductionBatch
    {
        return ProductionBatch::create([
            'product_id' => Product::sole()->id, 'batch_code' => 'TEST-BATCH',
            'batch_name' => 'Batch testing', 'capacity_pouches' => 10,
        ]);
    }

    private function order(): Order
    {
        $order = new Order;
        $order->forceFill([
            'batch_id' => $this->batch()->id, 'order_code' => 'TEST-ORDER',
            'idempotency_key' => 'test-idempotency', 'customer_name' => 'Fixture pengujian',
            'customer_phone' => '0000000000', 'quantity_pouches' => 3,
            'unit_price_rupiah' => 5000, 'total_rupiah' => 15000,
            'payment_method' => 'transfer', 'tracking_token_hash' => hash('sha256', random_bytes(32)),
            'tracking_token_created_at' => now(),
        ])->save();

        return $order->refresh();
    }

    private function cash(Order $order, User $admin, ?PaymentProof $proof = null): CashTransaction
    {
        $cash = new CashTransaction;
        $cash->forceFill([
            'order_id' => $order->id, 'payment_proof_id' => $proof?->id,
            'type' => 'penerimaan', 'amount_rupiah' => 15000, 'payment_channel' => 'transfer_bank',
            'action_key' => 'cash-test', 'recorded_by' => $admin->id, 'transacted_at' => now(),
        ])->save();

        return $cash;
    }

    public function test_migrations_create_all_core_and_support_tables_and_are_repeatable(): void
    {
        foreach (['users', 'products', 'business_settings', 'production_batches', 'orders', 'payment_proofs', 'cash_transactions', 'cancellation_requests', 'audit_logs', 'sessions', 'jobs', 'cache'] as $table) {
            $this->assertTrue(Schema::hasTable($table), $table);
        }
        $this->assertFalse(Schema::hasColumn('users', 'email'));
        $this->assertTrue(Schema::hasColumn('users', 'username'));
        $this->assertFalse(Schema::hasColumn('audit_logs', 'updated_at'));
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->assertDatabaseCount('products', 1);
    }

    public function test_foundation_migration_can_reverse_and_reapply_in_isolated_database(): void
    {
        $migration = require database_path('migrations/2026_11_02_000001_create_order_foundation_tables.php');
        $migration->down();
        $this->assertFalse(Schema::hasTable('orders'));
        $this->assertTrue(Schema::hasTable('users'));
        $migration->up();
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('products', 1);
    }

    public function test_seeder_is_idempotent_and_preserves_owner_edits_without_fake_accounts(): void
    {
        $product = Product::sole();
        $this->assertSame('Keripik Singkong 4 Saudara', $product->name);
        $this->assertSame('Satu Rasa Khas', $product->flavor_name);
        $this->assertSame(5000, $product->price_rupiah);
        $this->assertSame('pouch', $product->unit);
        $product->update(['price_rupiah' => 6000]);
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('products', 1);
        $this->assertSame(6000, $product->fresh()->price_rupiah);
        foreach (['users', 'orders', 'business_settings', 'production_batches'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
    }

    public function test_relationships_defaults_snapshot_and_sensitive_serialization(): void
    {
        $order = $this->order();
        $admin = User::factory()->create();
        $proof = new PaymentProof;
        $proof->forceFill([
            'order_id' => $order->id, 'file_path' => 'proofs/test.png', 'file_name' => 'test.png',
            'file_size_bytes' => 1024, 'mime_type' => 'image/png',
            'uploaded_by' => $admin->id, 'verified_by' => $admin->id,
        ])->save();
        $cash = $this->cash($order, $admin, $proof);
        $cancel = new CancellationRequest;
        $cancel->forceFill(['order_id' => $order->id, 'reason' => 'Fixture', 'decided_by' => $admin->id])->save();
        $audit = new AuditLog;
        $audit->forceFill([
            'user_id' => $admin->id, 'order_id' => $order->id, 'batch_id' => $order->batch_id,
            'event' => 'test.created', 'description' => 'Fixture', 'new_values' => ['quantity_pouches' => 3],
        ])->save();
        $settings = new BusinessSetting;
        $settings->forceFill([
            'business_name' => 'Fixture', 'whatsapp_number' => '0000000000', 'pickup_address' => 'Fixture',
            'bank_name' => 'TEST', 'bank_account_number' => 'TEST', 'bank_account_name' => 'TEST',
            'active_batch_id' => $order->batch_id,
        ])->save();

        $this->assertTrue($order->batch->product->is(Product::sole()));
        $this->assertTrue(Product::sole()->productionBatches->sole()->is($order->batch));
        $this->assertTrue($order->batch->orders->sole()->is($order));
        $this->assertTrue($settings->activeBatch->is($order->batch));
        $this->assertTrue($order->batch->activeBusinessSetting->is($settings));
        $this->assertTrue($proof->order->is($order));
        $this->assertTrue($proof->uploader->is($admin));
        $this->assertTrue($proof->verifier->is($admin));
        $this->assertTrue($proof->cashTransaction->is($cash));
        $this->assertTrue($cash->order->is($order));
        $this->assertTrue($cash->paymentProof->is($proof));
        $this->assertTrue($cash->recorder->is($admin));
        $this->assertTrue($cancel->order->is($order));
        $this->assertTrue($cancel->decider->is($admin));
        $this->assertTrue($audit->user->is($admin));
        $this->assertTrue($audit->order->is($order));
        $this->assertTrue($audit->batch->is($order->batch));
        foreach (['paymentProofs', 'cashTransactions', 'cancellationRequests', 'auditLogs'] as $relation) {
            $this->assertCount(1, $order->$relation);
        }
        foreach (['uploadedPaymentProofs', 'verifiedPaymentProofs', 'cashTransactions', 'decidedCancellationRequests', 'auditLogs'] as $relation) {
            $this->assertCount(1, $admin->$relation);
        }
        $this->assertCount(1, $order->batch->auditLogs);
        $this->assertSame(['quantity_pouches' => 3], $audit->fresh()->new_values);
        $this->assertSame('menunggu_konfirmasi', $order->status);
        $this->assertSame('belum_lunas', $order->payment_status);
        $this->assertSame('menunggu_verifikasi', $proof->fresh()->status);
        $this->assertSame(0, $order->allocated_pouches);
        $this->assertSame(0, $order->batch->allocated_pouches);
        Product::sole()->update(['price_rupiah' => 6000]);
        $this->assertSame(5000, $order->fresh()->unit_price_rupiah);
        $this->assertSame(15000, $order->fresh()->total_rupiah);
        $this->assertArrayNotHasKey('tracking_token_hash', $order->toArray());
        $this->assertArrayNotHasKey('idempotency_key', $order->toArray());
        $this->assertArrayNotHasKey('password', $admin->toArray());
        $this->assertArrayNotHasKey('file_path', $proof->toArray());
        $this->assertArrayNotHasKey('bank_account_number', $settings->toArray());
    }

    public function test_privileged_fields_are_not_mass_assignable_and_password_is_hashed(): void
    {
        $order = new Order;
        foreach (['status', 'payment_status', 'unit_price_rupiah', 'total_rupiah', 'allocated_pouches', 'tracking_token_hash', 'quantity_pouches'] as $field) {
            $this->assertFalse($order->isFillable($field));
        }
        $this->assertFalse((new User)->isFillable('role'));
        $this->assertFalse((new User)->isFillable('is_active'));
        $this->assertFalse((new CashTransaction)->isFillable('amount_rupiah'));
        $this->assertFalse((new AuditLog)->isFillable('event'));
        $user = new User(['password' => 'test-only-password']);
        $this->assertTrue(Hash::check('test-only-password', $user->password));
    }

    public function test_duplicate_order_idempotency_is_rejected(): void
    {
        $order = $this->order();
        $copy = $order->getAttributes();
        unset($copy['id']);
        $copy['order_code'] = 'OTHER';
        $copy['tracking_token_hash'] = hash('sha256', random_bytes(32));
        $this->expectException(QueryException::class);
        DB::table('orders')->insert($copy);
    }

    public function test_duplicate_tracking_hash_is_rejected(): void
    {
        $copy = $this->order()->getAttributes();
        unset($copy['id']);
        $copy['order_code'] = 'OTHER';
        $copy['idempotency_key'] = 'other';
        $this->expectException(QueryException::class);
        DB::table('orders')->insert($copy);
    }

    public function test_duplicate_cash_action_key_is_rejected(): void
    {
        $cash = $this->cash($this->order(), User::factory()->create());
        $copy = $cash->getAttributes();
        unset($copy['id']);
        $this->expectException(QueryException::class);
        DB::table('cash_transactions')->insert($copy);
    }

    public function test_referenced_batch_cannot_be_deleted(): void
    {
        $order = $this->order();
        $this->expectException(QueryException::class);
        $order->batch->delete();
    }

    public function test_cash_history_cannot_be_updated_through_eloquent(): void
    {
        $cash = $this->cash($this->order(), User::factory()->create());
        $cash->amount_rupiah = 1;
        $this->expectException(\LogicException::class);
        $cash->save();
    }

    public function test_audit_history_cannot_be_deleted_through_eloquent(): void
    {
        $audit = new AuditLog;
        $audit->forceFill(['event' => 'test', 'description' => 'Fixture'])->save();
        $this->expectException(\LogicException::class);
        $audit->delete();
    }

    public function test_audit_snapshots_exclude_credentials_tokens_and_nested_payloads(): void
    {
        $audit = new AuditLog;
        $audit->forceFill([
            'event' => 'test', 'description' => 'Fixture',
            'old_values' => ['password' => 'secret', 'tracking_token_hash' => 'secret', 'status' => 'belum_lunas'],
            'new_values' => ['tracking_token' => 'secret', 'idempotency_key' => 'secret', 'customer_phone' => 'private', 'quantity_pouches' => 3, 'status' => ['password' => 'secret']],
        ])->save();
        $this->assertSame(['status' => 'belum_lunas'], $audit->fresh()->old_values);
        $this->assertSame(['quantity_pouches' => 3], $audit->fresh()->new_values);
    }

    public function test_one_payment_proof_cannot_back_two_cash_entries(): void
    {
        $order = $this->order();
        $proof = new PaymentProof;
        $proof->forceFill([
            'order_id' => $order->id, 'file_path' => 'proofs/test.png',
            'file_name' => 'test.png', 'file_size_bytes' => 1024, 'mime_type' => 'image/png',
        ])->save();
        $copy = $this->cash($order, User::factory()->create(), $proof)->getAttributes();
        unset($copy['id']);
        $copy['action_key'] = 'other-action';
        $this->expectException(QueryException::class);
        DB::table('cash_transactions')->insert($copy);
    }

    public function test_orders_with_proof_history_cannot_be_deleted(): void
    {
        $order = $this->order();
        $proof = new PaymentProof;
        $proof->forceFill([
            'order_id' => $order->id, 'file_path' => 'proofs/test.png',
            'file_name' => 'test.png', 'file_size_bytes' => 1024, 'mime_type' => 'image/png',
        ])->save();
        $this->expectException(QueryException::class);
        $order->delete();
    }
}
