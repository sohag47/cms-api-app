<?php

namespace Database\Seeders;


use Database\Settings\Seeders\BrandSeeder;
use Database\Settings\Seeders\CategorySeeder;
use Database\Settings\Seeders\CountrySeeder;
use Database\Settings\Seeders\CurrencySeeder;
use Database\Settings\Seeders\ProductTypeSeeder;
use Database\Settings\Seeders\UnitSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            CurrencySeeder::class,
            CountrySeeder::class,
            ProductTypeSeeder::class,
            UnitSeeder::class,
        ]);
    }
}
