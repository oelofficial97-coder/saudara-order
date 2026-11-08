<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\User;
use App\Services\BatchService;
use Database\Seeders\ProductSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminWorkspaceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->seed(ProductSeeder::class);
    }

    private function owner(): User
    {
        return User::factory()->create(['role' => 'pemilik']);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'batch_name' => 'Batch pengujian', 'capacity_pouches' => 10,
            'estimated_ready_at' => now()->addDays(7)->setTimezone('Asia/Jakarta')->format('Y-m-d\TH:i'),
            'notes' => 'Fixture testing terisolasi',
        ], $overrides);
    }

    private function batch(array $overrides = []): ProductionBatch
    {
        $batch = new ProductionBatch;
        $batch->forceFill(array_replace([
            'product_id' => Product::sole()->id, 'batch_code' => 'TEST-'.Str::ulid(),
            'batch_name' => 'Batch pengujian', 'capacity_pouches' => 10,
            'allocated_pouches' => 0, 'is_open_for_orders' => false,
            'estimated_ready_at' => now()->addDays(7),
        ], $overrides))->save();

        return $batch;
    }

    private function settingsData(): array
    {
        // Fixtures only: never written to the development database.
        return [
            'business_name' => 'Usaha pengujian', 'whatsapp_number' => '6280000000000',
            'pickup_address' => 'Alamat khusus testing', 'pickup_maps_url' => null,
            'bank_name' => 'BANK TEST', 'bank_account_number' => '00000000', 'bank_account_name' => 'Fixture testing',
        ];
    }

    private function settings(?ProductionBatch $batch = null): BusinessSetting
    {
        $settings = new BusinessSetting($this->settingsData());
        $settings->active_batch_id = $batch?->id;
        $settings->save();

        return $settings;
    }

    private function order(ProductionBatch $batch, string $status, int $quantity): Order
    {
        $order = new Order;
        $allocated = in_array($status, ['dikonfirmasi', 'diproduksi', 'siap_diambil', 'selesai']) ? $quantity : 0;
        $order->forceFill([
            'batch_id' => $batch->id, 'order_code' => 'TEST-'.Str::ulid(),
            'idempotency_key' => Str::uuid()->toString(), 'customer_name' => 'Fixture testing',
            'customer_phone' => '0000000000', 'quantity_pouches' => $quantity,
            'unit_price_rupiah' => 5000, 'total_rupiah' => $quantity * 5000,
            'payment_method' => 'bayar_saat_ambil', 'status' => $status,
            'allocated_pouches' => $allocated, 'tracking_token_hash' => hash('sha256', random_bytes(32)),
            'tracking_token_created_at' => now(),
        ])->save();

        return $order;
    }

    public function test_guests_are_redirected_and_no_public_registration_exists(): void
    {
        $batch = $this->batch();
        foreach (['/admin/dashboard', '/admin/batches', '/admin/batches/create', "/admin/batches/{$batch->id}/edit", '/admin/settings'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }
        $this->post('/admin/batches', $this->payload())->assertRedirect(route('admin.login'));
        $this->get('/admin/login')->assertOk()->assertSee('autocomplete="current-password"', false);
        $this->get('/register')->assertNotFound();
        $this->get('/admin/register')->assertNotFound();
        $this->post('/admin/register')->assertNotFound();
    }

    public function test_login_accepts_active_owner_and_rotates_session(): void
    {
        $password = Str::password(24);
        $owner = User::factory()->create(['role' => 'pemilik', 'username' => 'owner.test', 'password' => $password]);
        $this->withSession(['marker' => 'keep']);
        $oldId = session()->getId();
        $this->post(route('admin.login.store'), ['username' => ' OWNER.TEST ', 'password' => $password])
            ->assertRedirect(route('admin.dashboard'))->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($owner);
        $this->assertNotSame($oldId, session()->getId());
        $this->get('/admin/login')->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_accepts_active_manager(): void
    {
        $password = Str::password(24);
        $manager = User::factory()->create(['password' => $password]);
        $this->post('/admin/login', ['username' => $manager->username, 'password' => $password])->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($manager);
    }

    public function test_wrong_password_and_inactive_account_are_rejected_without_flashing_password(): void
    {
        $password = Str::password(24);
        $user = User::factory()->create(['password' => $password]);
        $this->from('/admin/login')->post('/admin/login', ['username' => $user->username, 'password' => 'wrong'])
            ->assertRedirect('/admin/login')->assertSessionHasErrors('username')->assertSessionMissing('_old_input.password');
        $this->assertGuest();
        $user->is_active = false;
        $user->save();
        $this->post('/admin/login', ['username' => $user->username, 'password' => $password])->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_validates_required_fields_and_rate_limits_attempts(): void
    {
        $this->post('/admin/login', [])->assertSessionHasErrors(['username', 'password']);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/admin/login', ['username' => 'unknown', 'password' => 'wrong'])->assertSessionHasErrors('username');
        }
        $this->post('/admin/login', ['username' => 'UNKNOWN', 'password' => 'wrong'])->assertStatus(429);
        $this->travel(61)->seconds();
        $this->post('/admin/login', ['username' => 'unknown', 'password' => 'wrong'])->assertSessionHasErrors('username');
    }

    public function test_logout_invalidates_session_and_rotates_csrf_token(): void
    {
        $this->actingAs($this->owner())->withSession(['marker' => 'private', '_token' => 'old-test-token']);
        $oldId = session()->getId();
        $this->post('/admin/logout')->assertRedirect(route('admin.login'))->assertSessionMissing('marker');
        $this->assertGuest();
        $this->assertNotSame($oldId, session()->getId());
        $this->assertNotSame('old-test-token', session()->token());
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
        $this->get('/admin/logout')->assertStatus(405);
    }

    public function test_account_deactivation_revokes_existing_admin_session(): void
    {
        $user = $this->owner();
        $this->actingAs($user);
        User::whereKey($user->id)->update(['is_active' => false]);
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_manager_can_view_but_cannot_mutate_batches_or_settings(): void
    {
        $batch = $this->batch();
        $this->actingAs(User::factory()->create());
        $this->get('/admin/dashboard')->assertOk();
        $this->get('/admin/batches')->assertOk()->assertDontSee('Tambah batch')->assertDontSee('Buka penerimaan');
        $this->get('/admin/batches/create')->assertForbidden();
        $this->get("/admin/batches/{$batch->id}/edit")->assertForbidden();
        $this->post('/admin/batches', $this->payload())->assertForbidden();
        $this->put("/admin/batches/{$batch->id}", $this->payload())->assertForbidden();
        $this->post("/admin/batches/{$batch->id}/open")->assertForbidden();
        $this->post("/admin/batches/{$batch->id}/close")->assertForbidden();
        $this->get('/admin/settings')->assertForbidden();
        $this->put('/admin/settings', $this->settingsData())->assertForbidden();
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_dashboard_empty_state_and_actual_database_summary(): void
    {
        $this->actingAs($this->owner())->get('/admin/dashboard')->assertOk()
            ->assertSee('Belum ada batch aktif')->assertViewHas('productCount', 1)->assertViewHas('remaining', null)
            ->assertHeader('Cache-Control', 'no-store, private')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $batch = $this->batch(['capacity_pouches' => 12, 'allocated_pouches' => 5, 'is_open_for_orders' => true]);
        $this->settings($batch);
        $this->get('/admin/dashboard')->assertOk()->assertViewHas('remaining', 7)
            ->assertViewHas('activeBatch', fn ($actual) => $actual->is($batch))->assertSee('Terbuka');
    }

    public function test_owner_can_create_update_and_list_batch_with_audit_and_wib_conversion(): void
    {
        $this->actingAs($this->owner())->get('/admin/batches/create')->assertOk();
        $data = $this->payload(['estimated_ready_at' => '2030-01-10T14:30', 'is_open_for_orders' => true, 'allocated_pouches' => 999]);
        $this->post('/admin/batches', $data)->assertRedirect(route('admin.batches.index'))->assertSessionHasNoErrors();
        $batch = ProductionBatch::sole();
        $this->assertFalse($batch->is_open_for_orders);
        $this->assertSame(0, $batch->allocated_pouches);
        $this->assertSame('2030-01-10 07:30', $batch->estimated_ready_at->format('Y-m-d H:i'));
        $this->get("/admin/batches/{$batch->id}/edit")->assertOk()->assertSee('2030-01-10T14:30');
        $this->put("/admin/batches/{$batch->id}", $this->payload(['batch_name' => 'Batch diperbarui', 'capacity_pouches' => 20]))
            ->assertRedirect(route('admin.batches.index'))->assertSessionHasNoErrors();
        $this->assertSame(20, $batch->fresh()->capacity_pouches);
        $this->get('/admin/batches')->assertOk()->assertSee('Batch diperbarui');
        $this->assertDatabaseHas('audit_logs', ['event' => 'batch.created', 'batch_id' => $batch->id]);
        $this->assertDatabaseHas('audit_logs', ['event' => 'batch.updated', 'batch_id' => $batch->id]);
        $this->delete("/admin/batches/{$batch->id}")->assertStatus(405);
        $this->assertDatabaseCount('production_batches', 1);
    }

    public function test_capacity_and_date_validation_reject_invalid_input(): void
    {
        $this->actingAs($this->owner());
        foreach ([0, -1, 1.5, 4294967296, 'invalid'] as $capacity) {
            $this->post('/admin/batches', $this->payload(['capacity_pouches' => $capacity]))->assertSessionHasErrors('capacity_pouches');
        }
        $this->post('/admin/batches', $this->payload(['estimated_ready_at' => 'not-a-date']))->assertSessionHasErrors('estimated_ready_at');
        $this->post('/admin/batches', $this->payload(['batch_name' => '']))->assertSessionHasErrors('batch_name');
        $this->assertDatabaseCount('production_batches', 0);
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_capacity_cannot_fall_below_allocation_and_pending_orders_do_not_count(): void
    {
        $batch = $this->batch(['allocated_pouches' => 7]);
        $this->order($batch, 'dikonfirmasi', 7);
        $this->order($batch, 'menunggu_konfirmasi', 100);
        $this->actingAs($this->owner())->put("/admin/batches/{$batch->id}", $this->payload(['capacity_pouches' => 6]))->assertSessionHasErrors('capacity_pouches');
        $this->assertSame(10, $batch->fresh()->capacity_pouches);
        $this->put("/admin/batches/{$batch->id}", $this->payload(['capacity_pouches' => 8]))->assertSessionHasNoErrors();
        $this->assertSame(8, $batch->fresh()->capacity_pouches);
        $this->assertSame(7, $batch->fresh()->allocated_pouches);
    }

    public function test_capacity_is_locked_after_started_at_or_first_production_order(): void
    {
        $batch = $this->batch(['started_at' => now()]);
        $this->actingAs($this->owner())->put("/admin/batches/{$batch->id}", $this->payload(['capacity_pouches' => 20]))->assertSessionHasErrors('capacity_pouches');
        $other = $this->batch();
        $this->order($other, 'diproduksi', 3);
        $this->put("/admin/batches/{$other->id}", $this->payload(['capacity_pouches' => 20]))->assertSessionHasErrors('capacity_pouches');
        $this->put("/admin/batches/{$other->id}", $this->payload(['batch_name' => 'Nama diperbarui']))->assertSessionHasNoErrors();
        $this->assertSame(10, $other->fresh()->capacity_pouches);
    }

    public function test_only_one_batch_can_open_and_repeated_actions_do_not_duplicate_audit(): void
    {
        $settings = $this->settings();
        $first = $this->batch();
        $second = $this->batch();
        $this->actingAs($this->owner())->post("/admin/batches/{$first->id}/open")->assertSessionHasNoErrors();
        $this->assertSame($first->id, $settings->fresh()->active_batch_id);
        $this->post("/admin/batches/{$first->id}/open")->assertSessionHasNoErrors();
        $this->assertSame(1, AuditLog::where('event', 'batch.opened')->count());
        $this->post("/admin/batches/{$second->id}/open")->assertSessionHasErrors('batch');
        $this->assertFalse($second->fresh()->is_open_for_orders);
        $this->post("/admin/batches/{$first->id}/close")->assertSessionHasNoErrors();
        $this->assertNull($settings->fresh()->active_batch_id);
        $this->post("/admin/batches/{$first->id}/close")->assertSessionHasNoErrors();
        $this->assertSame(1, AuditLog::where('event', 'batch.closed')->count());
        $this->post("/admin/batches/{$second->id}/open")->assertSessionHasNoErrors();
        $this->assertSame(1, ProductionBatch::where('is_open_for_orders', true)->count());
    }

    public function test_open_requires_real_settings_active_product_future_estimate_and_remaining_quota(): void
    {
        $batch = $this->batch();
        $this->actingAs($this->owner())->post("/admin/batches/{$batch->id}/open")->assertSessionHasErrors('batch');
        $this->settings();
        $batch->estimated_ready_at = now()->subDay();
        $batch->save();
        $this->post("/admin/batches/{$batch->id}/open")->assertSessionHasErrors('batch');
        $batch->estimated_ready_at = now()->addDay();
        $batch->allocated_pouches = 10;
        $batch->save();
        $this->post("/admin/batches/{$batch->id}/open")->assertSessionHasErrors('batch');
        $batch->allocated_pouches = 0;
        $batch->save();
        Product::sole()->update(['is_active' => false]);
        $this->post("/admin/batches/{$batch->id}/open")->assertSessionHasErrors('batch');
        $this->assertFalse($batch->fresh()->is_open_for_orders);
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_close_is_rejected_until_pending_submissions_are_decided(): void
    {
        $batch = $this->batch(['is_open_for_orders' => true]);
        $settings = $this->settings($batch);
        $this->order($batch, 'menunggu_konfirmasi', 3);
        $this->actingAs($this->owner())->post("/admin/batches/{$batch->id}/close")->assertSessionHasErrors('batch');
        $this->assertTrue($batch->fresh()->is_open_for_orders);
        $this->assertSame($batch->id, $settings->fresh()->active_batch_id);
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_completed_batches_are_immutable(): void
    {
        $batch = $this->batch(['completed_at' => now()]);
        $this->settings();
        $this->actingAs($this->owner())->put("/admin/batches/{$batch->id}", $this->payload())->assertSessionHasErrors('batch_name');
        $this->post("/admin/batches/{$batch->id}/open")->assertSessionHasErrors('batch');
        $this->post("/admin/batches/{$batch->id}/close")->assertSessionHasErrors('batch');
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_settings_can_be_initialized_and_updated_without_accepting_active_pointer_input(): void
    {
        $batch = $this->batch();
        $this->actingAs($this->owner())->get('/admin/settings')->assertOk();
        $this->put('/admin/settings', [...$this->settingsData(), 'active_batch_id' => $batch->id])->assertSessionHasNoErrors();
        $this->assertNull(BusinessSetting::sole()->active_batch_id);
        $this->put('/admin/settings', [...$this->settingsData(), 'business_name' => 'Usaha diperbarui'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('business_settings', 1);
        $this->assertDatabaseHas('business_settings', ['business_name' => 'Usaha diperbarui']);
        $this->assertSame(2, AuditLog::where('event', 'settings.updated')->count());
    }

    public function test_first_owner_command_creates_hashed_account_and_refuses_a_second_owner(): void
    {
        $password = Str::password(24);
        $this->artisan('saudara:owner')
            ->expectsQuestion('Nama pemilik', 'Pemilik testing')
            ->expectsQuestion('Username (huruf, angka, titik, garis bawah atau tanda hubung)', 'Owner.Test')
            ->expectsQuestion('Kata sandi (minimal 12 karakter, huruf besar/kecil, angka dan simbol)', $password)
            ->expectsQuestion('Ulangi kata sandi', $password)
            ->assertSuccessful();
        $user = User::sole();
        $this->assertSame('pemilik', $user->role);
        $this->assertSame('owner.test', $user->username);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check($password, $user->password));
        $this->artisan('saudara:owner')->assertFailed();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_first_owner_command_rejects_weak_password_without_creating_user(): void
    {
        $this->artisan('saudara:owner')
            ->expectsQuestion('Nama pemilik', 'Pemilik testing')
            ->expectsQuestion('Username (huruf, angka, titik, garis bawah atau tanda hubung)', 'owner.test')
            ->expectsQuestion('Kata sandi (minimal 12 karakter, huruf besar/kecil, angka dan simbol)', 'short')
            ->expectsQuestion('Ulangi kata sandi', 'short')
            ->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_malformed_login_input_is_validated_without_server_error(): void
    {
        $this->post('/admin/login', ['username' => ['unexpected'], 'password' => ['unexpected']])
            ->assertSessionHasErrors(['username', 'password']);
        $this->assertGuest();
    }

    public function test_open_batch_cannot_lose_its_future_estimate(): void
    {
        $batch = $this->batch(['is_open_for_orders' => true]);
        $this->settings($batch);
        $this->actingAs($this->owner());
        foreach ([null, '2000-01-01T12:00'] as $estimate) {
            $this->put("/admin/batches/{$batch->id}", $this->payload(['estimated_ready_at' => $estimate]))
                ->assertSessionHasErrors('estimated_ready_at');
        }
        $this->assertTrue($batch->fresh()->estimated_ready_at->isFuture());
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_login_and_batch_mutations_require_csrf_tokens(): void
    {
        // Enable the real CSRF middleware for these requests; DB stays SQLite memory.
        $this->app['env'] = 'local';
        $this->post('/admin/login', ['username' => 'test', 'password' => 'test'])->assertStatus(419);
        $this->actingAs($this->owner())->post('/admin/batches', $this->payload())->assertStatus(419);
        $this->assertDatabaseCount('production_batches', 0);
    }

    public function test_batch_creation_rolls_back_if_audit_write_fails(): void
    {
        $owner = $this->owner();
        AuditLog::creating(fn () => throw new \RuntimeException('Simulated audit failure'));
        try {
            app(BatchService::class)->create($owner, $this->payload());
            $this->fail('Expected audit failure.');
        } catch (\RuntimeException $error) {
            $this->assertSame('Simulated audit failure', $error->getMessage());
        }
        $this->assertDatabaseCount('production_batches', 0);
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_batch_pagination_renders_with_the_admin_stylesheet(): void
    {
        for ($i = 0; $i < 13; $i++) {
            $this->batch(['batch_name' => 'Batch '.$i]);
        }
        $this->actingAs($this->owner())->get('/admin/batches')->assertOk()
            ->assertSee('Halaman 1 dari 2')->assertSee('rel="next"', false);
        $this->get('/admin/batches?page=2')->assertOk()->assertSee('Halaman 2 dari 2')->assertSee('rel="prev"', false);
    }
}
