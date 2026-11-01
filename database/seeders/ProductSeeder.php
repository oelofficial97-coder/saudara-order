<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Preserve later owner edits, including price, on subsequent deployments.
        Product::firstOrCreate(['singleton_key' => 1], [
            'slug' => 'keripik-singkong-4-saudara',
            'name' => 'Keripik Singkong 4 Saudara',
            'flavor_name' => 'Satu Rasa Khas',
            'description' => 'Keripik Singkong 4 Saudara dengan satu rasa khas.',
            'price_rupiah' => 5000,
            'unit' => 'pouch',
            'is_active' => true,
        ]);
    }
}
