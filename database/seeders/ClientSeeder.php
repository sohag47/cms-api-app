<?php

namespace Database\Seeders;

use App\Enums\ClientOrigin;
use App\Enums\ClientType;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'ABC Enterprises',
                'email' => 'john.doe@abc.com',
                'phone' => '1234567890',
                'thumb_image' => null,
                'address' => '123 Main Street',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62701',
                'client_origin' => ClientOrigin::LOCAL->value,
                'client_type' => ClientType::CUSTOMER->value,
                'country_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Global Traders',
                'client_origin' => ClientOrigin::LOCAL->value,
                'client_type' => ClientType::CUSTOMER->value,
                'thumb_image' => 'path/to/thumb_image.jpg',
                'email' => 'jane.smith@globaltraders.com',
                'phone' => null,
                'address' => '456 Market Street',
                'city' => 'New York',
                'state' => 'New York',
                'postal_code' => '10001',
                'country_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Metro Supplies',
                'client_origin' => ClientOrigin::LOCAL->value,
                'client_type' => ClientType::CUSTOMER->value,
                'thumb_image' => null,
                'email' => 'michael.brown@metrosupplies.com',
                'phone' => '9876543210',
                'address' => '789 Downtown Ave',
                'city' => 'Chicago',
                'state' => 'Illinois',
                'postal_code' => '60601',
                'country_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Oceanic Exports',
                'client_origin' => ClientOrigin::LOCAL->value,
                'client_type' => ClientType::CUSTOMER->value,
                'thumb_image' => null,
                'email' => 'emily.davis@oceanicexports.com',
                'phone' => null,
                'address' => '12 Seaside Blvd',
                'city' => 'Los Angeles',
                'state' => 'California',
                'postal_code' => '90001',
                'country_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tech Innovators',
                'client_origin' => ClientOrigin::LOCAL->value,
                'client_type' => ClientType::CUSTOMER->value,
                'thumb_image' => 'path/to/tech_thumb.jpg',
                'email' => 'sarah.lee@techinnovators.com',
                'phone' => '5551234567',
                'address' => '100 Innovation Drive',
                'city' => 'Austin',
                'state' => 'Texas',
                'postal_code' => '73301',
                'country_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Client::insert($data);
    }
}
