<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepanController;

Route::get('/', function () {
    return view('depan.depan_index');
});

Route::get('/depan/bkk', [DepanController::class, 'bkk']);
Route::get('/depan/login', [DepanController::class, 'login']);
Route::get('/depan/register', [DepanController::class, 'register']);
Route::get('/depan/lowongan-kerja', [DepanController::class, 'lowongan_kerja']);
Route::get('/depan/lowongan-kerja-disabilitas', [DepanController::class, 'lowongan_kerja_disabilitas']);
Route::get('/depan/infografis', [DepanController::class, 'infografis']);
Route::get('/depan/galeri', [DepanController::class, 'galeri']);
Route::get('/depan/berita', [DepanController::class, 'berita']);
