<?php

namespace Database\Seeders;

use App\Models\ContactPerson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Minhazul Islam Sohag',
                'email' => 'sohag@email.com',
                'phone' => '01876333284',
                'designation' => 'Software engineer',
                'client_id' => null,
                'created_at' => now(),
            ],
            [
                'name' => 'Muhuduzzaman Mahim',
                'email' => 'mahim@email.com',
                'phone' => '01876333285',
                'designation' => 'Software engineer',
                'client_id' => null,
                'created_at' => now(),
            ]
        ];

        ContactPerson::insert($data);
    }
}
