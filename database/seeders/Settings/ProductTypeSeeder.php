<?php

namespace Database\Settings\Seeders;

use App\Enums\StatusEnums;
use App\Models\Brand;
use App\Models\Settings\ProductType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productTypes = [
            ['name' => 'Laptop', 'created_at' => now()],
            ['name' => 'Desktop', 'created_at' => now()],
            ['name' => 'Mobile', 'created_at' => now()],
            ['name' => 'Tablet', 'created_at' => now()],
            ['name' => 'Smart Watch', 'created_at' => now()],
            ['name' => 'Printer', 'created_at' => now()],
            ['name' => 'Scanner', 'created_at' => now()],
            ['name' => 'Camera', 'created_at' => now()],
            ['name' => 'Headphone', 'created_at' => now()],
            ['name' => 'Speaker', 'created_at' => now()],
            ['name' => 'Monitor', 'created_at' => now()],
            ['name' => 'Projector', 'created_at' => now()],
            ['name' => 'UPS', 'created_at' => now()],
            ['name' => 'Router', 'created_at' => now()],
            ['name' => 'Switch', 'created_at' => now()],
            ['name' => 'Server', 'created_at' => now()],
            ['name' => 'Storage', 'created_at' => now()],
            ['name' => 'Software', 'created_at' => now()],
            ['name' => 'Accessories', 'created_at' => now()],
            ['name' => 'Others', 'created_at' => now()],
        ];
        ProductType::insert($productTypes);
    }
}
