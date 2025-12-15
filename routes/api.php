<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LowonganController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DashboardEksekutifController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', fn () => ['pong' => true]);
Route::get('/lowongan', [LowonganController::class, 'index']);

Route::prefix('v1')->group(function () {
    Route::get('/dashboard-eksekutif', [ApiDashboardEksekutifController::class, 'index']);
});

// Route::get('/dashboard/pencari', [DashboardController::class, 'pencari']);
// Route::get('/dashboard/lamaran', [DashboardController::class, 'prosesLamaran']);
// Route::get('/dashboard/perusahaan', [DashboardController::class, 'perusahaan']);
// Route::get('/dashboard/lowongan', [DashboardController::class, 'lowongan']);
// Route::get('/dashboard/penempatan', [DashboardController::class, 'penempatan']);
// Route::get('/dashboard/top_pendidikan', [DashboardController::class, 'topPendidikan']);
// Route::get('/dashboard/top_jurusan', [DashboardController::class, 'topJurusan']);
// Route::get('/dashboard/top_sektor', [DashboardController::class, 'topSektor']);






