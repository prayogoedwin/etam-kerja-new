<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepanController;

Route::get('/', function () {
    return view('depan.depan_index');
});

Route::get('/depan/bkk', [DepanController::class, 'bkk']);
