<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactPersonController;
use App\Http\Controllers\Settings\BrandController;
use App\Http\Controllers\Settings\CategoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Settings\CountryController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\ProductTypeController;
use App\Http\Controllers\Settings\UnitController;
use App\Models\Category;
use App\Models\User;
use App\Models\Post;
use App\Models\Profile;
use App\Models\VWUsers;
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
        'message' => "Hello World, Welcome to Hell",
        'data' => null,
        'errors' => null,
    ], Response::HTTP_OK);
});

// create login route
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Test routes for role/permission system
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/test/admin-only', function () {
        return response()->json(['message' => 'You are an admin!']);
    })->middleware('role:Super Admin');

    Route::get('/test/can-view-users', function () {
        return response()->json(['message' => 'You can view users!']);
    })->middleware('permission:view-users');

    Route::get('/test/user-info', function () {
        $user = auth()->user();
        return response()->json([
            'user' => $user->name,
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name')
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

    //? Role and Permission Management
    Route::prefix('admin')->middleware(['permission:view-dashboard'])->group(function () {
        // Role management routes
        Route::apiResource('roles', RoleController::class)->middleware('permission:view-roles');
        Route::post('roles/{role}/assign-permission', [RoleController::class, 'assignPermission'])
            ->middleware('permission:edit-roles');
        Route::post('roles/{role}/revoke-permission', [RoleController::class, 'revokePermission'])
            ->middleware('permission:edit-roles');
        
        // Permission management routes
        Route::apiResource('permissions', PermissionController::class)->middleware('permission:view-permissions');
        
        // User role management routes
        Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])
            ->middleware('permission:edit-users');
        Route::post('users/{user}/remove-role', [UserController::class, 'removeRole'])
            ->middleware('permission:edit-users');
        Route::post('users/{user}/give-permission', [UserController::class, 'givePermission'])
            ->middleware('permission:edit-users');
        Route::post('users/{user}/revoke-permission', [UserController::class, 'revokePermission'])
            ->middleware('permission:edit-users');
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
