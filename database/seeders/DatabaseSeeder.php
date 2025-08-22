<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class, // Must run before UserSeeder
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
        ]);
    }
}
