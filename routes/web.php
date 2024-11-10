<?php
use Illuminate\Support\Facades\Route;
use App\Captcha\SimpleCaptcha; // Replace with your actual namespace
use App\Http\Controllers\DepanController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;


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
Route::post('/depan/daftar-akun', [DepanController::class, 'daftar_akun'])->name('daftar-akun');






// Route::get('/captcha', function () {
//     return response()->json(['captcha' => captcha_src()]);
// });


Route::get('/captcha', function () {
    // Menghasilkan gambar CAPTCHA baru
    $captcha = captcha_src(); // Mendapatkan URL gambar CAPTCHA
    return response()->json(['captcha' => $captcha]); // Mengembalikan URL gambar sebagai JSON
});

Route::prefix('dapur')->middleware('auth')->group(function () {

    //route untuk admin
    Route::get('/dashboard', [BackController::class, 'index'])->name('dashboard');
    Route::get('/sample', [BackController::class, 'sample'])->name('sample');
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/get_roles', [RoleController::class, 'getRoles'])->name('roles.getRoles');


});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Route::name('setting')->prefix('setting')->group(function () {
//     Route::get('/banner', [BackController::class, 'settingBanner'])->name('banner');
//   });

