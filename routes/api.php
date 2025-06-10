<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\KategoriController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('kategori', [KategoriController::class, 'index']);
Route::post('kategori/tambah', [KategoriController::class, 'store']);
Route::get('kategori/{id}', [KategoriController::class, 'show']);
Route::put('kategori/{id}', [KategoriController::class, 'update']);
Route::delete('kategori/{id}', [KategoriController::class, 'destroy']);

Route::get('wishlist', [WishlistController::class, 'index']);
Route::get('wishlist/{id}', [WishlistController::class, 'show']);
Route::post('wishlist/tambah', [WishlistController::class, 'store']);
Route::put('wishlist/{id}', [WishlistController::class, 'update']);
Route::delete('wishlist/{id}', [WishlistController::class, 'destroy']);

// Route::get('user', [UserController::class, 'index']);
// Route::get('user/{id}', [UserController::class, 'show']);
// Route::post('user/tambah', [UserController::class, 'store']);
// Route::put('user/{id}', [UserController::class, 'update']);
// Route::delete('user/{id}', [UserController::class, 'destroy']);
