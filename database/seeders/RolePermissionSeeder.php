<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for different modules
        $permissions = [
            // User management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Role management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            // Permission management
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',

            // Product management
            'view-products',
            'create-products',
            'edit-products',
            'delete-products',

            // Category management
            'view-categories',
            'create-categories',
            'edit-categories',
            'delete-categories',

            // Order management
            'view-orders',
            'create-orders',
            'edit-orders',
            'delete-orders',
            'approve-orders',
            'reject-orders',

            // Post/Blog management
            'view-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'publish-posts',

            // Comment management
            'view-comments',
            'create-comments',
            'edit-comments',
            'delete-comments',
            'moderate-comments',

            // Client management
            'view-clients',
            'create-clients',
            'edit-clients',
            'delete-clients',

            // Document management
            'view-documents',
            'create-documents',
            'edit-documents',
            'delete-documents',

            // Profile management
            'view-profiles',
            'edit-profiles',

            // Dashboard access
            'view-dashboard',
            'view-admin-dashboard',

            // Settings
            'manage-settings',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - has most permissions except super admin specific ones
        $admin = Role::create(['name' => 'Admin']);
        $adminPermissions = [
            'view-users', 'create-users', 'edit-users', 'delete-users',
            'view-products', 'create-products', 'edit-products', 'delete-products',
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories',
            'view-orders', 'create-orders', 'edit-orders', 'delete-orders', 'approve-orders', 'reject-orders',
            'view-posts', 'create-posts', 'edit-posts', 'delete-posts', 'publish-posts',
            'view-comments', 'create-comments', 'edit-comments', 'delete-comments', 'moderate-comments',
            'view-clients', 'create-clients', 'edit-clients', 'delete-clients',
            'view-documents', 'create-documents', 'edit-documents', 'delete-documents',
            'view-profiles', 'edit-profiles',
            'view-dashboard', 'view-admin-dashboard',
        ];
        $admin->givePermissionTo($adminPermissions);

        // Manager - moderate permissions
        $manager = Role::create(['name' => 'Manager']);
        $managerPermissions = [
            'view-users', 'edit-users',
            'view-products', 'create-products', 'edit-products',
            'view-categories', 'create-categories', 'edit-categories',
            'view-orders', 'create-orders', 'edit-orders', 'approve-orders',
            'view-posts', 'create-posts', 'edit-posts', 'publish-posts',
            'view-comments', 'moderate-comments',
            'view-clients', 'create-clients', 'edit-clients',
            'view-documents', 'create-documents', 'edit-documents',
            'view-profiles', 'edit-profiles',
            'view-dashboard',
        ];
        $manager->givePermissionTo($managerPermissions);

        // Editor - content focused permissions
        $editor = Role::create(['name' => 'Editor']);
        $editorPermissions = [
            'view-products', 'create-products', 'edit-products',
            'view-categories', 'create-categories', 'edit-categories',
            'view-posts', 'create-posts', 'edit-posts',
            'view-comments', 'create-comments', 'moderate-comments',
            'view-documents', 'create-documents', 'edit-documents',
            'view-profiles', 'edit-profiles',
            'view-dashboard',
        ];
        $editor->givePermissionTo($editorPermissions);

        // Sales - order and client focused permissions
        $sales = Role::create(['name' => 'Sales']);
        $salesPermissions = [
            'view-products',
            'view-orders', 'create-orders', 'edit-orders',
            'view-clients', 'create-clients', 'edit-clients',
            'view-documents', 'create-documents',
            'view-profiles', 'edit-profiles',
            'view-dashboard',
        ];
        $sales->givePermissionTo($salesPermissions);

        // User - basic user permissions
        $user = Role::create(['name' => 'User']);
        $userPermissions = [
            'view-products',
            'view-posts',
            'view-comments', 'create-comments',
            'view-profiles', 'edit-profiles',
        ];
        $user->givePermissionTo($userPermissions);

        // Customer - external user permissions
        $customer = Role::create(['name' => 'Customer']);
        $customerPermissions = [
            'view-products',
            'view-posts',
            'view-comments', 'create-comments',
            'view-profiles', 'edit-profiles',
        ];
        $customer->givePermissionTo($customerPermissions);
    }
}
