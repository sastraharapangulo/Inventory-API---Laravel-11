<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IncomingTrController;
use App\Http\Controllers\Api\OutgoingTrController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::apiResource('user', UserController::class);
    Route::apiResource('product', ProductController::class);
    Route::apiResource('incoming', IncomingTrController::class);
    Route::apiResource('outgoing', OutgoingTrController::class);
});
