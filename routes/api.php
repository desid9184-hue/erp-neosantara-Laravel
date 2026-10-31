<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\AuthController;

// API Test
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API ERP NeoSantara berhasil diakses',
    ]);
});

// Login
Route::post('/login', [AuthController::class, 'login']);

// Semua fitur berikut wajib login
Route::middleware('auth:sanctum')->group(function () {

    // Kampanye Pemasaran
    Route::get('/campaigns', [CampaignController::class, 'index']);
    Route::post('/campaigns', [CampaignController::class, 'store']);
    Route::put('/campaigns/{id}', [CampaignController::class, 'update']);
    Route::patch('/campaigns/{id}', [CampaignController::class, 'update']);
    Route::delete('/campaigns/{id}', [CampaignController::class, 'destroy']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Data pengguna login
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    });
});
