<?php

namespace Database\Seeders;


use Database\Seeders\BrandSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\CurrencySeeder;
use Database\Seeders\ProductTypeSeeder;
use Database\Seeders\UnitSeeder;
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
            UnitSeeder::class,
            ProductTypeSeeder::class,
            ClientSeeder::class,
            
            ContactPersonSeeder::class,
            AddressSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
