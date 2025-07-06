<?php

namespace Database\Seeders;

use App\Enums\StatusEnums;
use App\Models\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'client_id' => 1,
                'street_address' => '123 Green Road',
                'country_id' => 1,
                'contact_person_id' => 1,
                'status' => StatusEnums::ACTIVE,
                'created_at' => now(),
            ],
            [
                'client_id' => 2,
                'street_address' => '456 Blue Street',
                'country_id' => 1,
                'contact_person_id' => 1,
                'status' => StatusEnums::ACTIVE,
                'created_at' => now(),
            ]
        ];
        Address::insert($data);
    }
}
