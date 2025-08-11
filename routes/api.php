<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LowonganController;
use App\Http\Controllers\Api\DashboardController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', fn () => ['pong' => true]);
Route::get('/lowongan', [LowonganController::class, 'index']);



Route::get('/dashboard/pencari', [DashboardController::class, 'pencari']);
Route::get('/dashboard/lamaran', [DashboardController::class, 'prosesLamaran']);
Route::get('/dashboard/perusahaan', [DashboardController::class, 'perusahaan']);


