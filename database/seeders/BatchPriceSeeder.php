<?php

namespace Database\Seeders;

use App\Models\BatchPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BatchPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'batch_no' => 'PCS',
                'price' => 1999.99,
                'product_id' => 2,
                'created_at' => now(),
            ],
        ];
        BatchPrice::insert($products);
    }
}
