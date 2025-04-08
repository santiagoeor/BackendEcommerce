<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\products\productsController;

Route::get('listProducts', [productsController::class, 'index']);
Route::post('createProduct', [productsController::class, 'create']);
Route::put('updateProduct/{id}', [productsController::class, 'update']);
Route::delete('destroyProduct/{id}', [productsController::class, 'destroy']);