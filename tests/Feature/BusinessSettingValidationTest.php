<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BusinessSettingValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Minimal isolated fixtures: no migrate/reset command or application migration is run.
        // These fixtures describe the target nullable schema, not the current live MySQL schema.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('role');
            $table->boolean('is_active');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('singleton_key')->default(1)->unique();
        });
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('singleton_key')->default(1)->unique();
            $table->string('business_name', 100);
            $table->string('whatsapp_number', 25);
            $table->text('pickup_address');
            $table->string('pickup_maps_url', 500)->nullable();
            $table->string('bank_name', 50)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_name', 100)->nullable();
            $table->unsignedBigInteger('active_batch_id')->nullable();
            $table->timestamps();
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('event', 60);
            $table->text('description');
            $table->timestamp('created_at');
        });
        DB::table('products')->insert(['singleton_key' => 1]);
        $this->actingAs(User::factory()->create(['role' => 'pemilik']));
    }

    private function requiredData(): array
    {
        return [
            'business_name' => 'Usaha pengujian', 'whatsapp_number' => '6280000000000',
            'pickup_address' => 'Alamat khusus pengujian',
        ];
    }

    private function bankData(): array
    {
        return ['bank_name' => 'Bank pengujian', 'bank_account_number' => '0012345678', 'bank_account_name' => 'Pemilik pengujian'];
    }

    private function assertNullBankFields(): void
    {
        $settings = BusinessSetting::sole();
        foreach (array_keys($this->bankData()) as $field) {
            $this->assertNull($settings->$field);
        }
        $this->assertDatabaseCount('audit_logs', 1);
    }

    public function test_settings_save_without_any_bank_fields(): void
    {
        $this->put('/admin/settings', $this->requiredData())->assertRedirect(route('admin.settings.edit'))->assertSessionHasNoErrors();
        $this->assertNullBankFields();
    }

    public function test_empty_bank_fields_are_stored_as_null(): void
    {
        $this->put('/admin/settings', [...$this->requiredData(), ...array_fill_keys(array_keys($this->bankData()), '')])
            ->assertRedirect(route('admin.settings.edit'))->assertSessionHasNoErrors();
        $this->assertNullBankFields();
    }

    public function test_whitespace_bank_fields_are_stored_as_null(): void
    {
        $this->put('/admin/settings', [...$this->requiredData(), ...array_fill_keys(array_keys($this->bankData()), '   ')])
            ->assertSessionHasNoErrors();
        $this->assertNullBankFields();
    }

    public function test_explicit_null_bank_fields_are_accepted(): void
    {
        $this->put('/admin/settings', [...$this->requiredData(), ...array_fill_keys(array_keys($this->bankData()), null)])
            ->assertSessionHasNoErrors();
        $this->assertNullBankFields();
    }

    public function test_existing_bank_details_are_preserved_when_fields_are_omitted(): void
    {
        BusinessSetting::create([...$this->requiredData(), ...$this->bankData()]);
        $this->put('/admin/settings', [...$this->requiredData(), 'business_name' => 'Nama diperbarui'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('business_settings', [...$this->bankData(), 'business_name' => 'Nama diperbarui']);
        $this->assertDatabaseCount('business_settings', 1);
    }

    public function test_filled_bank_details_remain_valid_and_keep_leading_zeroes(): void
    {
        $this->put('/admin/settings', [...$this->requiredData(), ...$this->bankData()])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('business_settings', $this->bankData());
        $this->assertSame('0012345678', BusinessSetting::sole()->bank_account_number);
    }

    public function test_partially_filled_bank_details_are_optional_independently(): void
    {
        $this->put('/admin/settings', [...$this->requiredData(), 'bank_name' => 'Bank pengujian'])->assertSessionHasNoErrors();
        $settings = BusinessSetting::sole();
        $this->assertSame('Bank pengujian', $settings->bank_name);
        $this->assertNull($settings->bank_account_number);
        $this->assertNull($settings->bank_account_name);
    }

    public function test_filled_bank_details_still_reject_invalid_values(): void
    {
        foreach ([['bank_name' => str_repeat('a', 51)], ['bank_account_name' => str_repeat('a', 101)], ['bank_account_number' => 'ABC'], ['bank_account_number' => str_repeat('1', 51)], ['bank_name' => ['invalid']]] as $invalid) {
            $this->put('/admin/settings', [...$this->requiredData(), ...$invalid])->assertSessionHasErrors(array_keys($invalid));
        }
        $this->assertDatabaseCount('business_settings', 0);
        $this->assertDatabaseCount('audit_logs', 0);
    }

    public function test_business_name_whatsapp_and_address_remain_required(): void
    {
        foreach (array_keys($this->requiredData()) as $field) {
            $data = $this->requiredData();
            unset($data[$field]);
            $this->put('/admin/settings', $data)->assertSessionHasErrors($field);
        }
        $this->put('/admin/settings', [...$this->requiredData(), 'whatsapp_number' => '08123'])->assertSessionHasErrors('whatsapp_number');
        $this->assertDatabaseCount('business_settings', 0);
    }

    public function test_bank_inputs_are_optional_and_other_required_attributes_remain(): void
    {
        $response = $this->get('/admin/settings')->assertOk();
        foreach (array_keys($this->bankData()) as $field) {
            $this->assertMatchesRegularExpression('/<input\b[^>]*\bid="'.$field.'"[^>]*>/', $response->getContent());
            preg_match('/<input\b[^>]*\bid="'.$field.'"[^>]*>/', $response->getContent(), $matches);
            $this->assertDoesNotMatchRegularExpression('/\brequired\b/', $matches[0]);
        }
        $response->assertSee('name="business_name" maxlength="100" required', false)
            ->assertSee('name="whatsapp_number" type="tel" maxlength="15" required', false)
            ->assertSee('name="pickup_address" maxlength="2000" rows="3" required', false);
    }
}
