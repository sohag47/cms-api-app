<?php

namespace Database\Seeders;

use App\Enums\StatusEnums;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Riotouch LT86 IR Touch Interactive Flat Panel Display',
                'model' => 'LT86',
                'part_no' => '1',
                'unit' => 'PCS',
                'unit_price' => 1999.99,
                'status' => StatusEnums::ACTIVE,
                'brand_id' => 1,
                'currency_id' => 4,
                'origin_id' => 14,
                'manufacture_id' => 14,
                'product_type_id' => 1,
                'specifications' => '<div class="product-specifications">
                    <h2>Product Specifications</h2>
                    <ul>
                        <li><strong>Display Size:</strong> 86" Interactive Flat Panel</li>
                        <li><strong>Display Resolution:</strong> 4K HD</li>
                        <li><strong>Touch Technology:</strong> IR-touch, supports up to 20 touch points</li>
                        <li><strong>Audio:</strong> Built-in speakers, optional microphone</li>
                        <li><strong>Camera:</strong> Optional HD camera</li>
                        <li><strong>Object Recognition:</strong> Differentiates finger, stylus, and palm</li>
                        <li><strong>Connectivity:</strong> 3 HDMI ports, Display Port, VGA</li>
                        <li><strong>Wireless Compatibility:</strong> E-Share or Trans-screen for casting (Windows/iOS/Android)</li>
                        <li><strong>Software:</strong> U-mind whiteboard, app store, and customizable software options</li>
                        <li><strong>UI:</strong> Personalized UI with shortcuts for apps and files</li>
                        <li><strong>Design:</strong> Slim frame, plug-in OPS quick disassembly for flexible switching</li>
                        <li><strong>Power Supply:</strong> Built-in OS support</li>
                        <li><strong>Annotation:</strong> On-screen annotation tools for instant digital discussions</li>
                    </ul>
                </div>',
                'created_at' => now(),
            ],
        ];
        Product::insert($products);
    }
}
