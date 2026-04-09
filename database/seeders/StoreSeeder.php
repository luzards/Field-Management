<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = [
            [
                'name' => 'Store Central Jakarta',
                'address' => 'Jl. MH Thamrin No. 1, Central Jakarta',
                'latitude' => -6.1951,
                'longitude' => 106.8228,
                'contact_phone' => '02112345678',
                'contact_name' => 'Ahmad',
            ],
            [
                'name' => 'Store Sudirman',
                'address' => 'Jl. Jend. Sudirman Kav. 52-53, Jakarta',
                'latitude' => -6.2268,
                'longitude' => 106.8100,
                'contact_phone' => '02123456789',
                'contact_name' => 'Siti',
            ],
            [
                'name' => 'Store Kuningan',
                'address' => 'Jl. HR Rasuna Said Kav. C-22, Jakarta',
                'latitude' => -6.2297,
                'longitude' => 106.8372,
                'contact_phone' => '02134567890',
                'contact_name' => 'Dewi',
            ],
            [
                'name' => 'Store Kelapa Gading',
                'address' => 'Jl. Boulevard Barat Raya, Kelapa Gading, Jakarta',
                'latitude' => -6.1570,
                'longitude' => 106.9060,
                'contact_phone' => '02145678901',
                'contact_name' => 'Rudi',
            ],
            [
                'name' => 'Store PIK',
                'address' => 'Jl. Pantai Indah Kapuk, Penjaringan, Jakarta',
                'latitude' => -6.1078,
                'longitude' => 106.7400,
                'contact_phone' => '02156789012',
                'contact_name' => 'Maya',
            ],
        ];

        foreach ($stores as $store) {
            Store::create($store);
        }
    }
}
