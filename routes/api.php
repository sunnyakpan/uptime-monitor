<?php

use App\Http\Controllers\MonitorController;
use Illuminate\Support\Facades\Route;

Route::post('/monitors', [MonitorController::class, 'store']);
Route::get('/monitors', [MonitorController::class, 'index']);
Route::get('/monitors/{id}/history', [MonitorController::class, 'history']);