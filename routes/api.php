<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\CheckoutController;

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

Route::prefix('v1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user-profile', [AuthController::class, 'userProfile']);
        Route::post('products.storeProducts', [ProductsController::class, 'storeProduct']);
        Route::put('products.updateProducts/{id}', [ProductsController::class, 'updateProduct']);
        Route::delete('products.destroyProducts/{id}', [ProductsController::class, 'destroyProduct']);
        Route::post('cart.addToCart', [CartController::class, 'addToCart']);
        Route::delete('cart.remove/{id}', [CartController::class, 'removeFromCart']);
        Route::delete('cart.clearCart', [CartController::class, 'clearCart']);
        Route::get('cart.viewCart', [CartController::class, 'viewCart']);
        Route::post('voucher.checkVoucher', [VoucherController::class, 'checkVoucher']);
        Route::post('voucher.createVoucher', [VoucherController::class, 'createVoucher']);
        Route::put('voucher.updateVoucher/{id}', [VoucherController::class, 'updateVoucher']);
        Route::delete('voucher.deleteVoucher/{id}', [VoucherController::class, 'deleteVoucher']);
        Route::post('checkout', [CheckoutController::class, 'checkout']);
    });

    Route::get('products.getAllProducts', [ProductsController::class, 'getAllProduct']);
    
    
    
});

