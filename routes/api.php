<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DashboardEksekutifController;
use App\Http\Controllers\Api\Integrasi\Ak1Controller as IntegrasiAk1Controller;
use App\Http\Controllers\Api\Integrasi\AuthController as IntegrasiAuthController;
use App\Http\Controllers\Api\Integrasi\LowonganController as IntegrasiLowonganController;
use App\Http\Controllers\Api\Integrasi\MasterDataController as IntegrasiMasterDataController;
use App\Http\Controllers\Api\Integrasi\PencariKerjaController as IntegrasiPencariKerjaController;
use App\Http\Controllers\Api\LowonganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', fn () => ['pong' => true]);
Route::get('/lowongan', [LowonganController::class, 'index']);

Route::prefix('v1')->group(function () {
    Route::get('/dashboard-eksekutif', [DashboardEksekutifController::class, 'index']);
});

Route::get('/dashboard/pencari', [DashboardController::class, 'pencari']);
Route::get('/dashboard/lamaran', [DashboardController::class, 'prosesLamaran']);
Route::get('/dashboard/perusahaan', [DashboardController::class, 'perusahaan']);
Route::get('/dashboard/lowongan', [DashboardController::class, 'lowongan']);
Route::get('/dashboard/penempatan', [DashboardController::class, 'penempatan']);
Route::get('/dashboard/top_pendidikan', [DashboardController::class, 'topPendidikan']);
Route::get('/dashboard/top_jurusan', [DashboardController::class, 'topJurusan']);
Route::get('/dashboard/top_sektor', [DashboardController::class, 'topSektor']);

/*
|--------------------------------------------------------------------------
| API Integrasi eksternal
|--------------------------------------------------------------------------
*/
Route::prefix('v1/integrasi')->group(function () {
    Route::post('/auth/token', [IntegrasiAuthController::class, 'token']);
    Route::post('/auth/refresh', [IntegrasiAuthController::class, 'refresh']);

    Route::get('/lowongan/publik', [IntegrasiLowonganController::class, 'publik']);

    Route::middleware('integrasi.api')->group(function () {
        Route::get('/lowongan/internal', [IntegrasiLowonganController::class, 'internal']);

        Route::post('/pencari/register', [IntegrasiPencariKerjaController::class, 'register']);
        Route::get('/pencari/search', [IntegrasiPencariKerjaController::class, 'search']);
        Route::post('/pencari/search', [IntegrasiPencariKerjaController::class, 'search']);

        Route::get('/ak1', [IntegrasiAk1Controller::class, 'index']);
        Route::get('/ak1/{id}', [IntegrasiAk1Controller::class, 'show']);
        Route::post('/ak1', [IntegrasiAk1Controller::class, 'store']);

        Route::prefix('master')->group(function () {
            Route::get('/jenis-kelamin', [IntegrasiMasterDataController::class, 'jenisKelamin']);
            Route::get('/agama', [IntegrasiMasterDataController::class, 'agama']);
            Route::get('/kabkota', [IntegrasiMasterDataController::class, 'kabkota']);
            Route::get('/kecamatan', [IntegrasiMasterDataController::class, 'kecamatan']);
            Route::get('/pendidikan', [IntegrasiMasterDataController::class, 'pendidikan']);
            Route::get('/jurusan', [IntegrasiMasterDataController::class, 'jurusan']);
            Route::get('/status-perkawinan', [IntegrasiMasterDataController::class, 'statusPerkawinan']);
            Route::get('/disabilitas', [IntegrasiMasterDataController::class, 'disabilitas']);
            Route::get('/jenis-disabilitas', [IntegrasiMasterDataController::class, 'jenisDisabilitas']);
            Route::get('/jabatan-harapan', [IntegrasiMasterDataController::class, 'jabatanHarapan']);
        });
    });
});
