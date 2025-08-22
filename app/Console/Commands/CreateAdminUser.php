<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin 
                            {email? : The email of the admin user} 
                            {--name= : The name of the admin user}
                            {--password= : The password of the admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user with Super Admin role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter admin email');
        $name = $this->option('name') ?? $this->ask('Enter admin name', 'Super Admin');
        $password = $this->option('password') ?? $this->secret('Enter admin password');

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            if ($this->confirm('User with this email already exists. Do you want to assign Super Admin role?')) {
                $existingUser->assignRole('Super Admin');
                $this->info("Super Admin role assigned to existing user: {$email}");

                return;
            } else {
                $this->error('Operation cancelled.');

                return;
            }
        }

        // Check if Super Admin role exists
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if (! $superAdminRole) {
            $this->error('Super Admin role does not exist. Please run the RolePermissionSeeder first.');

            return;
        }

        // Create new admin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('Super Admin');

        $this->info('Admin user created successfully!');
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', 'Super Admin'],
                ['Created At', $user->created_at],
            ]
        );
    }
}
