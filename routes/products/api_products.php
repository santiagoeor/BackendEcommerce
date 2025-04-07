<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\products\productsController;

Route::get('listProducts', [productsController::class, 'index']);
Route::post('createProduct', [productsController::class, 'create']);