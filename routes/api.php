<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\VoucherController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;



Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);

    //product
    Route::get('/products', [ProductController::class, 'getProducts']);
    Route::post('/store-product', [ProductController::class, 'store']);

    //vouchers
    Route::get('/vouchers', [VoucherController::class, 'getVouchers']);
    Route::post('/store-voucher', [VoucherController::class, 'store']);

    //orders
    Route::post('/store-order', [ProductController::class, 'order']);

    Route::get('/activate-vouchers', function () {
        Artisan::call('activate:vouchers');

        return response()->json([
            'status' => 'success',
            'message' => 'Vouchers activated successfully.',
            'output' => Artisan::output(),
        ]);

    });
});
