<?php

namespace Database\Settings\Seeders;

use App\Enums\StatusEnums;
use App\Models\Settings\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $data = [
            [
                'name' => 'Server',
                'slug' => 'server',
                'status' => StatusEnums::ACTIVE,
                'created_at' => now(),
            ],
            [
                'name' => 'Desktop',
                'slug' => 'desktop',
                'status' => StatusEnums::ACTIVE,
                'created_at' => now(),
            ],
            [
                'name' => 'Laptop',
                'slug' => 'laptop',
                'status' => StatusEnums::ACTIVE,
                'created_at' => now(),
            ],
            [
                'name' => 'Tablet',
                'slug' => 'tablet',
                'status' => StatusEnums::ACTIVE,
                'created_at' => now(),
            ],
            [
                'name' => 'Mobile',
                'slug' => 'mobile',
                'status' => StatusEnums::ACTIVE,
                'created_at' => now(),
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'status' => StatusEnums::ACTIVE,
                'created_at' => now(),
            ]
        ];

        Category::insert($data);
    }
}
