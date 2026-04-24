<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

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

// Orders API routes
Route::get('orders/statistics', [OrderController::class, 'statistics']);
Route::apiResource('orders', OrderController::class);

// Customers API routes
Route::apiResource('customers', CustomerController::class);

// Products API routes
Route::apiResource('products', ProductController::class);
