<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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
    $response = [
        'success' => true,
        'message' => "Hello World, Welcome to Hell",
        'data' => [
            // 'user'=> VWUsers::get(), // one to one 
            // 'categories' => Category::with('posts')->paginate(10),// many to many
            // 'posts' => Post::with('category')->get(),// many to many
        ],
        'errors' => null,
    ];
    return response()->json($response, Response::HTTP_OK);
});

//! match route
Route::match(['get', 'post'], '/hello', function () {
    $response = [
        'success' => true,
        'message' => "Hello World, Welcome to Hell",
        "data" => null,
        'errors' => null,
    ];
    // abort(429, 'You do not have permission to access this resource.');
    return response()->json($response, Response::HTTP_OK); 
});

//! any route
Route::any('/any', function () {
    $response = [
        'success' => true,
        'message' => "Hello World, Welcome to Hell",
        "data" => null,
        'errors' => null,
    ];
    return response()->json($response, Response::HTTP_OK);
});

//! Route with parameter
Route::get('/hello/{id}', function ($id) {
    $response = [
        'success' => true,
        'message' => "Hello World, Welcome to Hell",
        'data' => [
            'id' => $id,
        ],
        'errors' => null,
    ];
    return response()->json($response, Response::HTTP_OK);
});

//! Route with optional parameter
Route::get('/hello-details/{id?}', function ($id = null) {
    $response = [
        'success' => true,
        'message' => "Hello World, Welcome to Hell",
        'data' => [
            'id' => $id,
        ],
        'errors' => null,
    ];
    return response()->json($response, Response::HTTP_OK);
});

//! Route with optional multiple parameter
Route::get('/hello/{name?}/{id?}', function ($name = null, $id = null) {
    $response = [
        'success' => true,
        'message' => "Hello World, Welcome to Hell",
        'data' => [
            'name' => $name,
            'id' => $id,
        ],
        'errors' => null,
    ];
    return response()->json($response, Response::HTTP_OK);
});
// Route with regular expression constraint
Route::get('/hello-test/{name}/{id}', function ($name, $id) {
    $response = [
        'success' => true,
        'message' => "Hello World, Welcome to Hell",
        'data' => [
            'name' => $name,
            'id' => $id,
        ],
        'errors' => null,
    ];
    return response()->json($response, Response::HTTP_OK);
})->where(['name' => '[a-zA-Z]+', 'id' => '[0-9]+']);

//! Route group
Route::prefix('v1')->group(function () {
    Route::get('/hello', function () {
        $response = [
            'success' => true,
            'message' => "Hello World, Welcome to Hell",
            'data' => null,
            'errors' => null,
        ];
        return response()->json($response, Response::HTTP_OK);
    });
    Route::get('/hi', function () {
        $response = [
            'success' => true,
            'message' => "Hi World, Welcome to Heaven",
            'data' => null,
            'errors' => null,
        ];
        return response()->json($response, Response::HTTP_OK);
    });
});

//! Route controller
Route::post('/upload-files', [DocumentController::class, 'store']);
Route::post('/delete-files', [DocumentController::class, 'destroy']);

// create login route
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

//! API resource
Route::apiResources([
    'welcome'=> LearningController::class,
    'categories' => CategoryController::class,
    'products' => ProductController::class,
    'users' => UserController::class,
]);

//! Route middleware
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['auth:sanctum', 'token.expiration'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::delete('/logout', [AuthController::class, 'logout']);

});

//! Route fallback
Route::fallback(function () {
    $response = [
        'success' => false,
        'message' => "Page not found",
        'data' => null,
        'errors' => null,
    ];
    return response()->json($response, Response::HTTP_NOT_FOUND);
});

//! Route redirect
Route::redirect('/here', '/api/there', Response::HTTP_MOVED_PERMANENTLY);

