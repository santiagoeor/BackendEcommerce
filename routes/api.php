<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\authController;

Route::post('v1/login', [authController::class, 'login']);
Route::get('v1/testsDeploy', [authController::class, 'testsDeploy']);

Route::prefix('v1')->group(function () {
    require __DIR__ . '/products/api_products.php';
});

Route::group(['middleware' => ['jwt.verify']], function () {

    Route::post('v1/logout', [authController::class, 'logout']);
});