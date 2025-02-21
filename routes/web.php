<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Middleware\VerifyCsrfToken;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//! Disabling Middleware Directly on the Route
Route::post('webhook/receive', function () {
    $response = [
        'success' => true,
        'message' => "Page found",
        'data' => null,
        'errors' => null,
    ];
    return response()->json($response, Response::HTTP_OK);
})->withoutMiddleware([VerifyCsrfToken::class]);