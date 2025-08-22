<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactPersonController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings\BrandController;
use App\Http\Controllers\Settings\CategoryController;
use App\Http\Controllers\Settings\CountryController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\ProductTypeController;
use App\Http\Controllers\Settings\UnitController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//! Basic Route
Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => 'Tender Hub API v1.0',
        'data' => [
            'version' => '1.0.0',
            'documentation' => 'API documentation available',
            'endpoints' => [
                'auth' => '/api/auth/*',
                'admin' => '/api/admin/*',
                'public' => '/api/*',
            ],
        ],
        'errors' => null,
    ], Response::HTTP_OK);
});

//! Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Protected auth routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('/revoke-all-tokens', [AuthController::class, 'revokeAllTokens']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::get('/permissions', [AuthController::class, 'permissions']);
    });
});

// Legacy auth routes (keep for backward compatibility)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Test routes for role/permission system
Route::middleware(['auth:sanctum'])->prefix('test')->group(function () {
    Route::get('/admin-only', function () {
        return response()->json(['message' => 'You are an admin!', 'timestamp' => now()]);
    })->middleware('role:Super Admin');

    Route::get('/can-view-users', function () {
        return response()->json(['message' => 'You can view users!', 'timestamp' => now()]);
    })->middleware('permission:view-users');

    Route::get('/user-info', function () {
        $user = auth()->user();

        return response()->json([
            'user' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('name'),
            /** @phpstan-ignore-next-line */
            'permissions' => $user->getAllPermissions()->pluck('name'),
            /** @phpstan-ignore-next-line */
            'direct_permissions' => $user->getDirectPermissions()->pluck('name'),
            /** @phpstan-ignore-next-line */
            'role_permissions' => $user->getPermissionsViaRoles()->pluck('name'),
            /** @phpstan-ignore-next-line */
            'grouped_permissions' => $user->getAllPermissions()->groupBy(function ($permission) {
                // Group by permission category
                $groupMappings = [
                    'user' => 'User Management',
                    'role' => 'Role Management',
                    'permission' => 'Permission Management',
                    'product' => 'Product Management',
                    'categor' => 'Category Management',
                    'order' => 'Order Management',
                    'post' => 'Content Management',
                    'comment' => 'Comment Management',
                    'client' => 'Client Management',
                    'document' => 'Document Management',
                    'profile' => 'Profile Management',
                    'dashboard' => 'Dashboard & Analytics',
                    'setting' => 'System Settings',
                ];

                foreach ($groupMappings as $keyword => $group) {
                    if (strpos($permission->name, $keyword) !== false) {
                        return $group;
                    }
                }

                return 'Other Permissions';
            })->map(function ($permissions, $group) {
                return [
                    'group_name' => $group,
                    'permissions' => $permissions->map(function ($permission) {
                        $parts = explode('-', $permission->name);

                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'action' => $parts[0] ?? 'other',
                            'module' => isset($parts[1]) ? implode('-', array_slice($parts, 1)) : 'general',
                        ];
                    })->toArray(),
                    'count' => $permissions->count(),
                ];
            })->values()->toArray(),
        ]);
    });

    Route::get('/role/{role}', function ($role) {
        $user = auth()->user();
        /** @phpstan-ignore-next-line */
        $hasRole = $user->hasRole($role);

        return response()->json([
            'user' => $user->name,
            'checking_role' => $role,
            'has_role' => $hasRole,
            'user_roles' => $user->roles->pluck('name'),
        ]);
    });

    Route::get('/permission/{permission}', function ($permission) {
        $user = auth()->user();
        /** @phpstan-ignore-next-line */
        $hasPermission = $user->hasPermissionTo($permission);

        return response()->json([
            'user' => $user->name,
            'checking_permission' => $permission,
            'has_permission' => $hasPermission,
            /** @phpstan-ignore-next-line */
            'user_permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    });
});

//! Route middleware
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth:sanctum', 'token.expiration'])->group(function () {
    //? auth
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::delete('/logout', [AuthController::class, 'logout']);

    // file handle
    Route::post('/upload-files', [DocumentController::class, 'store']);
    Route::post('/delete-files', [DocumentController::class, 'destroy']);

    //? Role and Permission Management (Admin Panel)
    Route::prefix('admin')->middleware(['auth:sanctum', 'token.expiration'])->group(function () {
        // Check for admin dashboard permission first
        Route::get('/dashboard', function () {
            return response()->json([
                'message' => 'Welcome to Admin Dashboard',
                'user' => auth()->user()->name,
                'roles' => auth()->user()->roles->pluck('name'),
                'timestamp' => now(),
            ]);
        })->middleware('permission:view-admin-dashboard');

        // Role management routes
        Route::middleware('permission:view-roles')->group(function () {
            Route::apiResource('roles', RoleController::class);
            Route::post('roles/{role}/assign-permission', [RoleController::class, 'assignPermission'])
                ->middleware('permission:edit-roles');
            Route::post('roles/{role}/revoke-permission', [RoleController::class, 'revokePermission'])
                ->middleware('permission:edit-roles');
        });

        // Permission management routes
        Route::middleware('permission:view-permissions')->group(function () {
            Route::apiResource('permissions', PermissionController::class);
            Route::get('permissions-grouped', [PermissionController::class, 'grouped']);
        });

        // User role management routes
        Route::middleware('permission:edit-users')->group(function () {
            Route::post('users/{user}/assign-role', [UserController::class, 'assignRole']);
            Route::post('users/{user}/remove-role', [UserController::class, 'removeRole']);
            Route::post('users/{user}/give-permission', [UserController::class, 'givePermission']);
            Route::post('users/{user}/revoke-permission', [UserController::class, 'revokePermission']);
        });
    });

    //? settings
    Route::prefix('settings')->group(function () {
        Route::apiResource('countries', CountryController::class)->only(['index']);
        Route::apiResources([
            'categories' => CategoryController::class,
            'brands' => BrandController::class,
            'currencies' => CurrencyController::class,
            'product-types' => ProductTypeController::class,
            'units' => UnitController::class,
        ]);

        //* for bulk data manage [insert/update]
        Route::post('/categories/bulk-insert', [CategoryController::class, 'bulkInsert']);
    });

    //? All CURD resource route
    Route::apiResources([
        'welcome' => LearningController::class,
        'products' => ProductController::class,
        'users' => UserController::class,
        'clients' => ClientController::class,
        'contact-persons' => ContactPersonController::class,
        'address' => AddressController::class,
    ]);
});
