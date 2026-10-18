<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendPagesTest extends TestCase
{
    public function test_home_page_is_accessible_through_its_named_route(): void
    {
        $this->get(route('home'))
            ->assertOk();
    }

    public function test_preorder_page_exposes_the_prototype_form_contract(): void
    {
        $this->get(route('preorder'))
            ->assertOk()
            ->assertSee('id="prototype-form"', false)
            ->assertSee('Rp5.000')
            ->assertSee('value="transfer_manual"', false)
            ->assertSee('value="bayar_saat_ambil"', false)
            ->assertSee('Tidak ada data yang dikirim atau disimpan.');
    }

    public function test_tracking_page_exposes_private_access_and_demo_contracts(): void
    {
        $this->get(route('tracking'))
            ->assertOk()
            ->assertSee('id="tracking-form"', false)
            ->assertSee('DEMO')
            ->assertSee('kunci')
            ->assertSee('tautan pribadi')
            ->assertSee('kode pesanan biasa');
    }
}
