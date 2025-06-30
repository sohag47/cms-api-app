<?php

namespace Database\Seeders;

use App\Enums\StatusEnums;
use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Dell', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
            ['name' => 'HP', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
            ['name' => 'Apple', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
            ['name' => 'Samsung', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
            ['name' => 'Sony', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
            ['name' => 'LG', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
            ['name' => 'Acer', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
            ['name' => 'Asus', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
            ['name' => 'Lenovo', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
            ['name' => 'MSI', 'logo' => null, 'status' => StatusEnums::ACTIVE, 'created_at' => now()],
        ];
        Brand::insert($brands);
    }
}
