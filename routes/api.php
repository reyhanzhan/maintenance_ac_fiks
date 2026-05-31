<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TeknisiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('api.token')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware('api.token:teknisi')->prefix('teknisi')->group(function () {
    Route::get('/sync', [TeknisiController::class, 'sync']);
    Route::get('/reports', [TeknisiController::class, 'reports']);
    Route::post('/reports', [TeknisiController::class, 'storeReport']);
    Route::get('/reports/{report}', [TeknisiController::class, 'showReport']);
});
