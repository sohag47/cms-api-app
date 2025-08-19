# Role-Based Authentication System Documentation

## Overview

This Laravel application implements a comprehensive role-based authentication system using the Spatie Laravel Permission package. The system provides fine-grained access control with roles, permissions, and middleware protection.

## System Architecture

### Roles Hierarchy

1. **Super Admin** - Full system access
2. **Admin** - Administrative access (excluding super admin functions)
3. **Manager** - Management level access
4. **Editor** - Content management access
5. **Sales** - Sales and client focused access
6. **User** - Basic user access
7. **Customer** - External user access

### Permission Categories

#### User Management

-   `view-users` - View user listings
-   `create-users` - Create new users
-   `edit-users` - Edit user details
-   `delete-users` - Delete users

#### Role Management

-   `view-roles` - View role listings
-   `create-roles` - Create new roles
-   `edit-roles` - Edit role details
-   `delete-roles` - Delete roles

#### Permission Management

-   `view-permissions` - View permission listings
-   `create-permissions` - Create new permissions
-   `edit-permissions` - Edit permission details
-   `delete-permissions` - Delete permissions

#### Product Management

-   `view-products` - View product listings
-   `create-products` - Create new products
-   `edit-products` - Edit product details
-   `delete-products` - Delete products

#### Order Management

-   `view-orders` - View order listings
-   `create-orders` - Create new orders
-   `edit-orders` - Edit order details
-   `delete-orders` - Delete orders
-   `approve-orders` - Approve orders
-   `reject-orders` - Reject orders

#### Content Management

-   `view-posts` - View blog posts
-   `create-posts` - Create new posts
-   `edit-posts` - Edit post content
-   `delete-posts` - Delete posts
-   `publish-posts` - Publish/unpublish posts

#### Dashboard Access

-   `view-dashboard` - Access to basic dashboard
-   `view-admin-dashboard` - Access to admin dashboard

## API Endpoints

### Authentication Required

All role/permission management endpoints require authentication via Sanctum tokens.

### Role Management

#### Get All Roles

```http
GET /api/admin/roles
Authorization: Bearer {token}
Permission Required: view-roles
```

#### Get Single Role

```http
GET /api/admin/roles/{id}
Authorization: Bearer {token}
Permission Required: view-roles
```

#### Create Role

```http
POST /api/admin/roles
Authorization: Bearer {token}
Permission Required: create-roles

Body:
{
    "name": "Custom Role",
    "permissions": [1, 2, 3] // Array of permission IDs
}
```

#### Update Role

```http
PUT /api/admin/roles/{id}
Authorization: Bearer {token}
Permission Required: edit-roles

Body:
{
    "name": "Updated Role Name",
    "permissions": [1, 2, 3, 4]
}
```

#### Delete Role

```http
DELETE /api/admin/roles/{id}
Authorization: Bearer {token}
Permission Required: delete-roles
```

#### Assign Permission to Role

```http
POST /api/admin/roles/{id}/assign-permission
Authorization: Bearer {token}
Permission Required: edit-roles

Body:
{
    "permission_id": 5
}
```

#### Revoke Permission from Role

```http
POST /api/admin/roles/{id}/revoke-permission
Authorization: Bearer {token}
Permission Required: edit-roles

Body:
{
    "permission_id": 5
}
```

### Permission Management

#### Get All Permissions

```http
GET /api/admin/permissions
Authorization: Bearer {token}
Permission Required: view-permissions
```

#### Create Permission

```http
POST /api/admin/permissions
Authorization: Bearer {token}
Permission Required: create-permissions

Body:
{
    "name": "custom-permission",
    "guard_name": "web"
}
```

### User Role Management

#### Assign Role to User

```http
POST /api/admin/users/{id}/assign-role
Authorization: Bearer {token}
Permission Required: edit-users

Body:
{
    "role_id": 2
}
```

#### Remove Role from User

```http
POST /api/admin/users/{id}/remove-role
Authorization: Bearer {token}
Permission Required: edit-users

Body:
{
    "role_id": 2
}
```

#### Give Direct Permission to User

```http
POST /api/admin/users/{id}/give-permission
Authorization: Bearer {token}
Permission Required: edit-users

Body:
{
    "permission_id": 3
}
```

#### Revoke Permission from User

```http
POST /api/admin/users/{id}/revoke-permission
Authorization: Bearer {token}
Permission Required: edit-users

Body:
{
    "permission_id": 3
}
```

## Middleware Usage

### In Routes

```php
// Require specific role
Route::middleware(['role:Admin'])->group(function () {
    // Admin only routes
});

// Require specific permission
Route::middleware(['permission:view-users'])->group(function () {
    // Routes requiring view-users permission
});

// Multiple permissions (user must have ALL)
Route::middleware(['permission:edit-users,delete-users'])->group(function () {
    // Routes requiring both edit and delete user permissions
});
```

### In Controllers

```php
// Check if user has role
if (auth()->user()->hasRole('Admin')) {
    // Admin specific logic
}

// Check if user has permission
if (auth()->user()->hasPermissionTo('edit-users')) {
    // Allow editing users
}

// Check multiple permissions
if (auth()->user()->hasAllPermissions(['edit-users', 'delete-users'])) {
    // User has both permissions
}

// Check any of multiple permissions
if (auth()->user()->hasAnyPermission(['edit-users', 'view-users'])) {
    // User has at least one permission
}
```

### In Blade Templates

```php
@role('Admin')
    <p>Admin only content</p>
@endrole

@hasrole('Manager')
    <p>Manager content</p>
@endhasrole

@can('edit-users')
    <button>Edit User</button>
@endcan

@hasanyrole('Admin|Manager')
    <p>Admin or Manager content</p>
@endhasanyrole
```

## Artisan Commands

### Create Admin User

```bash
# Interactive mode
php artisan make:admin

# With parameters
php artisan make:admin admin@example.com --name="Super Admin" --password="secret123"
```

### Seed Roles and Permissions

```bash
php artisan db:seed --class=RolePermissionSeeder
```

### Seed Users with Roles

```bash
php artisan db:seed --class=UserSeeder
```

## Model Usage

### User Model

```php
$user = User::find(1);

// Assign role
$user->assignRole('Admin');
$user->assignRole(['Admin', 'Editor']); // Multiple roles

// Remove role
$user->removeRole('Admin');

// Sync roles (removes all current roles and assigns new ones)
$user->syncRoles(['Manager', 'Editor']);

// Give permission directly
$user->givePermissionTo('edit-posts');

// Revoke permission
$user->revokePermissionTo('edit-posts');

// Get all permissions (via roles and direct permissions)
$permissions = $user->getAllPermissions();

// Get roles
$roles = $user->roles;
```

### Role Model

```php
use Spatie\Permission\Models\Role;

$role = Role::findByName('Admin');

// Assign permissions
$role->givePermissionTo(['edit-users', 'delete-users']);

// Revoke permissions
$role->revokePermissionTo('delete-users');

// Sync permissions
$role->syncPermissions(['edit-users', 'view-users']);

// Get permissions
$permissions = $role->permissions;
```

## Security Considerations

1. **Super Admin Protection**: Super Admin role cannot be deleted and can only be modified by other Super Admins.

2. **Role Deletion**: Roles cannot be deleted if they are assigned to users.

3. **Permission Deletion**: Permissions cannot be deleted if they are assigned to roles.

4. **Middleware Order**: Ensure authentication middleware comes before permission middleware.

5. **Token Expiration**: All protected routes include token expiration middleware.

## Database Structure

### Tables Created

-   `roles` - Stores role information
-   `permissions` - Stores permission information
-   `model_has_permissions` - Many-to-many relationship between models and permissions
-   `model_has_roles` - Many-to-many relationship between models and roles
-   `role_has_permissions` - Many-to-many relationship between roles and permissions

### Indexes

All relationship tables have proper indexes for optimal query performance.

## Error Handling

The system provides comprehensive error handling:

-   401 Unauthorized for unauthenticated requests
-   403 Forbidden for insufficient permissions
-   422 Unprocessable Entity for validation errors
-   500 Internal Server Error for system errors

## Testing

### Test User Credentials

After running the UserSeeder:

-   Super Admin: `admin@tenderhub.com`
-   Manager: `manager@tenderhub.com`
-   Editor: `editor@tenderhub.com`
-   Sales: `sales@tenderhub.com`

Default password for all test users is generated by the UserFactory.

## Extending the System

### Adding New Permissions

1. Add permission name to RolePermissionSeeder
2. Run the seeder: `php artisan db:seed --class=RolePermissionSeeder`
3. Assign to appropriate roles
4. Use in routes/controllers as needed

### Adding New Roles

1. Create role in RolePermissionSeeder
2. Assign appropriate permissions
3. Run seeder to create the role
4. Update role hierarchy documentation

This role-based authentication system provides a solid foundation for managing user access in your Laravel application with granular control over permissions and roles.
