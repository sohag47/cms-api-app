<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;


// use App\Http\Controllers\Admin\CategoryController;
// use App\Http\Controllers\Admin\FrameworkController;
// use App\Http\Controllers\Admin\LibraryController;
// use App\Http\Controllers\Admin\PatronCategoryController;
// use App\Http\Controllers\Admin\PatronController;
// use App\Http\Controllers\Admin\TabController;

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

Route::prefix('dropdown')->group(function () {
    Route::get('categories', [CategoryController::class, 'dropdown']);
    // Route::prefix('settings')->group(function () {
    //     Route::get('libraries', [LibraryController::class, 'dropdown'])->name('libraries.dropdown');
    //     Route::get('patron-categories', [PatronCategoryController::class, 'dropdown']);
    //     Route::get('frameworks', [FrameworkController::class, 'dropdown']);
    //     Route::get('tabs', [TabController::class, 'dropdown']);
    // });
    // Route::get('patrons', [PatronController::class, 'dropdown']);
});