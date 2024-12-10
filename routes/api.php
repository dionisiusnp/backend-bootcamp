<?php

use App\Http\Middleware\DisableCors;
use Illuminate\Support\Facades\Route;

Route::middleware([DisableCors::class])->group(function () {
    Route::post('register', [\App\Http\Controllers\UsersController::class, 'store']);
    Route::post('login', [\App\Http\Controllers\UsersController::class, 'login']);

    Route::middleware(['auth:api'])->group(function () {
        //SELECT
        Route::get('select/product-categories', [App\Http\Controllers\SelectController::class, 'categories']);
        Route::get('select/payment-methods', [App\Http\Controllers\SelectController::class, 'payments']);
        Route::get('select/customers', [App\Http\Controllers\SelectController::class, 'customers']);
        
        //SELLER
        Route::get('chat/seller', [\App\Http\Controllers\ChatController::class, 'indexSeller']);
        Route::resource('payment-method', \App\Http\Controllers\PaymentMethodController::class)->only(['index']);
        Route::resource('product-category', \App\Http\Controllers\ProductCategoryController::class)->except(['create', 'edit']);
        Route::resource('product', \App\Http\Controllers\ProductController::class)->except(['create', 'edit']);

        //BUYER
        Route::resource('user', \App\Http\Controllers\UsersController::class)->except(['create', 'edit', 'store']);
        Route::resource('wishlist', \App\Http\Controllers\WishlistController::class)->except(['create', 'edit', 'update', 'show']);
        Route::resource('chat', \App\Http\Controllers\ChatController::class)->except(['create', 'edit']);
        Route::resource('order', \App\Http\Controllers\OrderController::class)->except(['create', 'store', 'edit']);
        Route::resource('order-item', \App\Http\Controllers\OrderItemController::class)->except(['create', 'edit', 'update']);
        Route::get('cart', [\App\Http\Controllers\OrderItemController::class, 'cart']);
    });
});
