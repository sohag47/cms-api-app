<?php

namespace Database\Seeders;

use App\Models\Settings\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 'USD',
                'name' => 'United States Dollar',
                'symbol' => '$',
                'usd_exchange_rate' => '1.00'
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'usd_exchange_rate' => '0.97'
            ],
            [
                'code' => 'AED',
                'name' => 'United Arab Emirates Dirham',
                'symbol' => 'د.إ',
                'usd_exchange_rate' => '3.67'
            ],
            [
                'code' => 'BDT',
                'name' => 'Bangladeshi Taka',
                'symbol' => '৳',
                'usd_exchange_rate' => '122.00'
            ],
            [
                'code' => 'INR',
                'name' => 'Indian Rupee',
                'symbol' => '₹',
                'usd_exchange_rate' => '86.45'
            ]
        ];

        Currency::insert($data);
    }
}
