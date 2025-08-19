<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@tenderhub.com',
        ]);
        $admin->assignRole('Super Admin');

        // Create manager user
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@tenderhub.com',
        ]);
        $manager->assignRole('Manager');

        // Create editor user
        $editor = User::factory()->create([
            'name' => 'Editor User',
            'email' => 'editor@tenderhub.com',
        ]);
        $editor->assignRole('Editor');

        // Create sales user
        $sales = User::factory()->create([
            'name' => 'Sales User',
            'email' => 'sales@tenderhub.com',
        ]);
        $sales->assignRole('Sales');

        // Create regular users with different roles
        $users = User::factory(5)->create();
        foreach ($users as $user) {
            $user->assignRole('User');
        }

        // Create customer users
        $customers = User::factory(5)->create();
        foreach ($customers as $customer) {
            $customer->assignRole('Customer');
        }
    }
}
