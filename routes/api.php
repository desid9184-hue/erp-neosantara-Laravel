<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\CampaignController;

// ========================================
// API TEST
// ========================================
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API ERP NeoSantara berhasil diakses',
    ]);
});

// ========================================
// LOGIN
// ========================================
Route::post('/login', [AuthController::class, 'login']);

// ========================================
// ROUTE YANG WAJIB LOGIN
// ========================================
Route::middleware('auth:sanctum')->group(function () {

    // ------------------------------------
    // DATA BARANG
    // ------------------------------------

    // GET semua barang
    Route::get('/barangs', [BarangController::class, 'index']);

    // GET barang berdasarkan ID
    Route::get('/barangs/{id}', [BarangController::class, 'show']);


    // ------------------------------------
    // KAMPANYE PEMASARAN
    // ------------------------------------

    // GET semua kampanye beserta barang
    Route::get('/campaigns', [CampaignController::class, 'index']);

    // GET detail kampanye berdasarkan ID
    Route::get('/campaigns/{id}', [CampaignController::class, 'show']);

    // POST tambah kampanye
    Route::post('/campaigns', [CampaignController::class, 'store']);

    // PUT edit kampanye
    Route::put('/campaigns/{id}', [CampaignController::class, 'update']);

    // PATCH edit sebagian data kampanye
    Route::patch('/campaigns/{id}', [CampaignController::class, 'update']);

    // DELETE hapus kampanye
    Route::delete('/campaigns/{id}', [CampaignController::class, 'destroy']);


    // ------------------------------------
    // LOGOUT
    // ------------------------------------
    Route::post('/logout', [AuthController::class, 'logout']);


    // ------------------------------------
    // DATA PENGGUNA LOGIN
    // ------------------------------------
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    });

});
