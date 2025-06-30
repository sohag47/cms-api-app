<?php

namespace Database\Settings\Seeders;

use App\Enums\StatusEnums;
use App\Models\Brand;
use App\Models\ProductTypes;
use App\Models\Settings\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'M', // Meter
                'created_at' => now(),
            ],
            [
                'name' => 'FT', // Foot
                'created_at' => now(),
            ],
            [
                'name' => 'DOZ', // Dozen
                'created_at' => now(),
            ],
            [
                'name' => 'PCS', // Pieces (Standard uppercase format)
                'created_at' => now(),
            ],
            [
                'name' => 'SQM', // Square Meter (Common abbreviation)
                'created_at' => now(),
            ],
            [
                'name' => 'SQFT', // Square Foot (Common abbreviation)
                'created_at' => now(),
            ],
            [
                'name' => 'CBM', // Cubic Meter
                'created_at' => now(),
            ]
        ];


        Unit::insert($data);
    }
}
