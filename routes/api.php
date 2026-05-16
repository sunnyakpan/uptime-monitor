<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MonitorController;
use Illuminate\Support\Facades\Route;

// Public routes — no auth required
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected routes — require Bearer token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',                        [AuthController::class, 'logout']);
    Route::post('/monitors',                      [MonitorController::class, 'store']);
    Route::get('/monitors',                       [MonitorController::class, 'index']);
    Route::get('/monitors/{id}/history',          [MonitorController::class, 'history']);
});