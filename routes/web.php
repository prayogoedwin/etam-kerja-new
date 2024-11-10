<?php
use Illuminate\Support\Facades\Route;
use App\Captcha\SimpleCaptcha; // Replace with your actual namespace
use App\Http\Controllers\DepanController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserPencariController;


use App\Http\Controllers\EtamFaqController;


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
Route::get('/depan/daftar', [DepanController::class, 'daftar']); //with role
Route::post('/depan/cek-awal-akun', [DepanController::class, 'cek_awal_akun'])->name('cek-awal-akun');
Route::post('/depan/cek-awal-otp', [DepanController::class, 'cek_awal_otp'])->name('cek-awal-otp');
Route::get('/depan/getkecamatanbyid/{kabkota_id}', [DepanController::class, 'getKecamatanByKabkota'])->name('get-kecamatan-bykabkota');
Route::get('/depan/getdesabyid/{kec_id}', [DepanController::class, 'getDesaByKec'])->name('get-desa-bykecamatan');
Route::get('/depan/getjurusanbyid/{pendidikan_id}', [DepanController::class, 'getJurusanByPendidikan'])->name('get-jurusan-bypendidikan');
Route::post('/depan/akhir_daftar-akun', [DepanController::class, 'akhir_daftar_akun'])->name('akhir-daftar-akun');





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

    Route::prefix('setting')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

        Route::get('/faqs', [EtamFaqController::class, 'index'])->name('faq.index');
        Route::post('/faq/add', [EtamFaqController::class, 'store'])->name('faq.add');
        Route::get('/faq/get/{id}', [EtamFaqController::class, 'getData'])->name('faq.detail');
        Route::delete('/faq/delete/{id}', [EtamFaqController::class, 'softdelete'])->name('faq.softdelete');
        Route::put('/faq/update/{id}', [EtamFaqController::class, 'update'])->name('faq.update');
    });

    Route::prefix('users')->group(function () {

        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::post('/admin/add', [AdminController::class, 'store'])->name('admin.add');
        Route::get('/admin/get/{id}', [AdminController::class, 'getAdmin'])->name('admin.detail');
        Route::put('/admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/admin/delete/{id}', [AdminController::class, 'softdelete'])->name('admin.softdelete');

        Route::get('/pencari', [UserPencariController::class, 'index'])->name('userpencari.index');
        Route::get('/penyedia', [AdminController::class, 'index'])->name('userperush.index');


    });
});


Route::middleware('guest')->get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/act_login', [AuthController::class, 'login'])->name('login.action');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::name('setting')->prefix('setting')->group(function () {
//     Route::get('/banner', [BackController::class, 'settingBanner'])->name('banner');
//   });

