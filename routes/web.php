<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserRole;

use App\Captcha\SimpleCaptcha; // Replace with your actual namespace
use App\Http\Controllers\DepanController;
use App\Http\Controllers\BackController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserPencariController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\LowonganAdminController;
use App\Http\Controllers\LowonganPencariController;
use App\Http\Controllers\EtamFaqController;
use App\Http\Controllers\EtamInfografisController;
use App\Http\Controllers\EtamGaleriController;
use App\Http\Controllers\EtamBeritaController;
use App\Http\Controllers\Ak1PencariController;
use App\Http\Controllers\UserPenyediaController;
use App\Http\Controllers\ProfilPenyediaController;
use App\Http\Controllers\ProfilPencariController;
use App\Http\Controllers\ProfilBkkController;
use App\Http\Controllers\BkkPenyediaController;
use App\Http\Controllers\HistoryLamaranPencariController;
use App\Http\Controllers\Ak1Controller;
use App\Http\Controllers\DiterimaPencariController;
use App\Http\Controllers\PenempatanController;
use App\Http\Controllers\RekapController;
use App\Http\Middleware\TrackVisitors;
use App\Http\Controllers\DashboardPimpinanController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\JobFairController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\BkkController;

// Route::get('/', function () {
//     return view('depan.depan_index');
// });


Route::get('/', [DepanController::class, 'index'])->middleware(TrackVisitors::class);


Route::get('/depan/bkk', [DepanController::class, 'bkk']);
Route::get('/depan/login', [DepanController::class, 'login']);
Route::get('/depan/register', [DepanController::class, 'register']);
Route::get('depan/lowongan-kerja', [DepanController::class, 'lowongan_kerja'])->name('depan.lowongan-kerja');
Route::get('/depan/lowongan-kerja-disabilitas', [DepanController::class, 'lowongan_kerja_disabilitas']);
Route::get('/depan/lowongan-detail/{id}', [DepanController::class, 'lowongan_show'])->name('lowongan.show');
Route::get('/depan/infografis', [DepanController::class, 'infografis']);
Route::get('/depan/galeri', [DepanController::class, 'galeri']);
Route::get('/depan/berita', [DepanController::class, 'berita']);
Route::get('/depan/berita/{id}', [DepanController::class, 'show'])->name('berita.show');
Route::post('/depan/daftar-akun', [DepanController::class, 'daftar_akun'])->name('daftar-akun');
Route::get('/depan/daftar', [DepanController::class, 'daftar']); //with role
Route::post('/depan/cek-awal-akun', [DepanController::class, 'cek_awal_akun'])->name('cek-awal-akun');
Route::post('/depan/cek-awal-otp', [DepanController::class, 'cek_awal_otp'])->name('cek-awal-otp');
Route::get('/depan/getkecamatanbyid/{kabkota_id}', [DepanController::class, 'getKecamatanByKabkota'])->name('get-kecamatan-bykabkota');
Route::get('/depan/getdesabyid/{kec_id}', [DepanController::class, 'getDesaByKec'])->name('get-desa-bykecamatan');
Route::get('/depan/getjurusanbyid/{pendidikan_id}', [DepanController::class, 'getJurusanByPendidikan'])->name('get-jurusan-bypendidikan');
Route::post('/depan/akhir_daftar-akun', [DepanController::class, 'akhir_daftar_akun'])->name('akhir-daftar-akun');
Route::post('/depan/akhir_daftar-akun-perush', [DepanController::class, 'akhir_daftar_akun_perush'])->name('akhir-daftar-akun-perush');
Route::post('/depan/akhir_daftar-akun-bkk', [DepanController::class, 'akhir_daftar_akun_bkk'])->name('akhir-daftar-akun-bkk');
Route::get('/depan/getkabkotabyid/{prov_id}', [DepanController::class, 'getKabkotaByProv'])->name('get-kabkota-byprov');
Route::get('/getpendidikans', function () {
    $pendidikan = getPendidikan(); // Panggil fungsi helper
    return response()->json($pendidikan); // Kembalikan data sebagai JSON
})->name('get-all-pendidikan');




// Route::get('/captcha', function () {
//     return response()->json(['captcha' => captcha_src()]);
// });


Route::get('/captcha', function () {
    // Menghasilkan gambar CAPTCHA baru
    $captcha = captcha_src(); // Mendapatkan URL gambar CAPTCHA
    return response()->json(['captcha' => $captcha]); // Mengembalikan URL gambar sebagai JSON
});

Route::prefix('dapur')->middleware('auth')->group(function () {

    Route::get('/rekap31', [RekapController::class, 'ak3titik1'])->name('rekap.ak31');
    Route::get('/rekap32', [RekapController::class, 'ak3titik2'])->name('rekap.ak32');
    Route::get('/rekap34', [RekapController::class, 'ak3titik4'])->name('rekap.ak34');
    Route::get('/rekap37', [RekapController::class, 'ak3titik7'])->name('rekap.ak37');
    Route::get('/rekap38', [RekapController::class, 'ak3titik8'])->name('rekap.ak38');

    // Job Fair Routes
    Route::get('/jobfair', [JobFairController::class, 'index'])->name('jobfair.index');
    Route::post('/jobfair', [JobFairController::class, 'store'])->name('jobfair.store');
    Route::get('/jobfair/{id}', [JobFairController::class, 'show'])->name('jobfair.show');
    Route::put('/jobfair/{id}', [JobFairController::class, 'update'])->name('jobfair.update');
    Route::delete('/jobfair/{id}', [JobFairController::class, 'destroy'])->name('jobfair.destroy');

    // Additional Routes
    Route::post('/jobfair/{id}/verifikasi', [JobFairController::class, 'verifikasi'])->name('jobfair.verifikasi');
    Route::post('/jobfair/{id}/unverifikasi', [JobFairController::class, 'unverifikasi'])->name('jobfair.unverifikasi');
    Route::post('/jobfair/{id}/toggle-status', [JobFairController::class, 'toggleStatus'])->name('jobfair.toggle-status');
    Route::get('/jobfair-users', [JobFairController::class, 'getUsers'])->name('jobfair.users');

    Route::get('/jobfair-penyedia-kerja', [JobFairController::class, 'getPenyediaKerja'])->name('jobfair.penyedia-kerja');

    // Routes untuk Perusahaan Job Fair
    Route::get('/jobfair/{jobfair}/perusahaan', [JobFairController::class, 'perusahaan'])->name('jobfair.perusahaan');
    Route::post('/jobfair/{jobfair}/perusahaan', [JobFairController::class, 'storePerusahaan'])->name('jobfair.perusahaan.store');
    Route::get('/jobfair/{jobfair}/perusahaan/{id}', [JobFairController::class, 'showPerusahaan'])->name('jobfair.perusahaan.show');
    Route::put('/jobfair/{jobfair}/perusahaan/{id}', [JobFairController::class, 'updatePerusahaan'])->name('jobfair.perusahaan.update');
    Route::delete('/jobfair/{jobfair}/perusahaan/{id}', [JobFairController::class, 'destroyPerusahaan'])->name('jobfair.perusahaan.destroy');
    Route::post('/jobfair/{jobfair}/perusahaan/{id}/change-status', [JobFairController::class, 'changeStatusPerusahaan'])->name('jobfair.perusahaan.change-status');

    Route::get('jobfair/{jobfairId}/perusahaan/{userId}/lowongan', [JobFairController::class, 'lowongan'])->name('jobfair.lowongan');
    Route::post('jobfair/{jobfairId}/perusahaan/{userId}/lowongan', [JobFairController::class, 'storeLowongan'])->name('jobfair.lowongan.store');
    Route::get('jobfair/{jobfairId}/perusahaan/{userId}/lowongan/{id}', [JobFairController::class, 'showLowongan'])->name('jobfair.lowongan.show');
    Route::put('jobfair/{jobfairId}/perusahaan/{userId}/lowongan/{id}', [JobFairController::class, 'updateLowongan'])->name('jobfair.lowongan.update');
    Route::delete('jobfair/{jobfairId}/perusahaan/{userId}/lowongan/{id}', [JobFairController::class, 'destroyLowongan'])->name('jobfair.lowongan.destroy');

     // Helper Routes
    Route::get('jobfair/get-all-pendidikan', [JobFairController::class, 'getAllPendidikan'])->name('jobfair.get-all-pendidikan');
    Route::get('get-jurusan-bypendidikan/{id}', [JobFairController::class, 'getJurusanByPendidikan'])->name('get-jurusan-bypendidikan');

    Route::get('/api/get-penyedia-kerja', [JobFairController::class, 'getPenyediaKerjaList'])->name('api.penyedia-kerja');

    //route untuk admin
    Route::get('/dashboard', [BackController::class, 'index'])->name('dashboard');
    Route::get('/sample', [BackController::class, 'sample'])->name('sample');
    Route::prefix('setting')->middleware(CheckUserRole::class . ':super-admin, admin-provinsi')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');

        Route::get('/faqs', [EtamFaqController::class, 'index'])->name('faq.index');
        Route::post('/faq/add', [EtamFaqController::class, 'store'])->name('faq.add');
        Route::get('/faq/get/{id}', [EtamFaqController::class, 'getData'])->name('faq.detail');
        Route::delete('/faq/delete/{id}', [EtamFaqController::class, 'softdelete'])->name('faq.softdelete');
        Route::put('/faq/update/{id}', [EtamFaqController::class, 'update'])->name('faq.update');

        Route::get('/infografis', [EtamInfografisController::class, 'index'])->name('infografis.index');
        Route::post('/infografis/add', [EtamInfografisController::class, 'store'])->name('infografis.add');
        Route::get('/infografis/{id}', [EtamInfografisController::class, 'edit'])->name('infografis.edit');
        Route::put('/infografis/update/{id}', [EtamInfografisController::class, 'update'])->name('infografis.update');
        Route::delete('/infografis/delete/{id}', [EtamInfografisController::class, 'destroy'])->name('infografis.destroy');

        Route::get('/galeri', [EtamGaleriController::class, 'index'])->name('galeri.index');
        Route::post('/galeri/add', [EtamGaleriController::class, 'store'])->name('galeri.add');
        Route::get('/galeri/{id}', [EtamGaleriController::class, 'edit'])->name('galeri.edit');
        Route::put('/galeri/update/{id}', [EtamGaleriController::class, 'update'])->name('galeri.update');
        Route::delete('/galeri/delete/{id}', [EtamGaleriController::class, 'destroy'])->name('galeri.destroy');

        Route::get('/berita', [EtamBeritaController::class, 'index'])->name('berita.index');
        Route::post('/berita/add', [EtamBeritaController::class, 'store'])->name('berita.add');
        Route::get('/berita/{id}/edit', [EtamBeritaController::class, 'edit'])->name('berita.edit');
        Route::put('/berita/{id}', [EtamBeritaController::class, 'update'])->name('berita.update');
        Route::delete('/berita/delete/{id}', [EtamBeritaController::class, 'destroy'])->name('berita.destroy');
    });

    // Route::prefix('users')->group(function () {
    Route::prefix('users')->middleware(CheckUserRole::class . ':super-admin,admin-provinsi,admin-kabkota,admin-kabkota-officer')->group(function () {

        Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
        Route::post('/admin/add', [AdminController::class, 'store'])->name('admin.add');
        Route::get('/admin/get/{id}', [AdminController::class, 'getAdmin'])->name('admin.detail');
        Route::put('/admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('/admin/delete/{id}', [AdminController::class, 'softdelete'])->name('admin.softdelete');
        Route::put('/admin/reset/{id}', [AdminController::class, 'reset'])->name('admin.reset');

        Route::get('/pencari', [UserPencariController::class, 'index'])->name('userpencari.index');
        Route::delete('/pencari/delete/{id}', [UserPencariController::class, 'softdelete'])->name('userpencari.softdelete');
        Route::put('/pencari/reset/{id}', [UserPencariController::class, 'reset'])->name('userpencari.reset');

        Route::get('/penyedia', [UserPenyediaController::class, 'index'])->name('userpenyedia.index');
        Route::delete('/penyedia/delete/{id}', [UserPenyediaController::class, 'softdelete'])->name('userpenyedia.softdelete');
        Route::put('/penyedia/reset/{id}', [UserPenyediaController::class, 'reset'])->name('userpenyedia.reset');
    });


    Route::prefix('datas')->middleware(CheckUserRole::class . ':super-admin,admin-provinsi,admin-kabkota,admin-kabkota-officer')->group(function () {

        Route::get('/pencari', [UserPencariController::class, 'data'])->name('datapencari.index');
        Route::get('/datapencari/export-csv', [UserPencariController::class, 'exportCsv'])->name('datapencari.exportCsv');

        Route::get('/penyedia', [UserPenyediaController::class, 'data'])->name('datapenyedia.index');
        Route::get('/datapenyedia/export-csv', [UserPenyediaController::class, 'exportCsv'])->name('datapenyedia.exportCsv');
    });

    Route::prefix('penyedias')->middleware(CheckUserRole::class . ':super-admin,admin-provinsi,admin-kabkota,admin-kabkota-officer,penyedia-kerja')->group(function () {
        Route::get('/lowongan', [LowonganController::class, 'index'])->name('lowongan.index');
        Route::post('/lowongan/add', [LowonganController::class, 'store'])->name('lowongan.add');
        Route::get('/lowongan/detail/{id}', [LowonganController::class, 'show'])->name('lowongan.detail');
        Route::put('/lowongan/update/{id}', [LowonganController::class, 'update'])->name('lowongan.update');
        Route::get('/lowongan/pelamar/{id}', [LowonganController::class, 'pelamar'])->name('lowongan.pelamar');
        // Route::get('/lowongan/pelamar/{id}', [LowonganController::class, 'pelamar'])->name('lowongan.pelamar');
        Route::get('/lowongan/download-pelamar/{id}', [LowonganController::class, 'downloadPelamar'])->name('lowongan.download-pelamar');
        Route::get('/lowongan/detail_pelamar/{id}', [LowonganController::class, 'detailpelamar'])->name('lowongan.detailpelamar');

        // Route::post('/lowongan/bulk_updatepelamar', [LowonganController::class, 'bulkupdatepelamar'])->middleware('check.role:penyedia-kerja')->name('bulk.update.pelamar');
        Route::post('/lowongan/bulk_updatepelamar', [LowonganController::class, 'bulkupdatepelamar'])->name('bulk.update.pelamar');
        Route::delete('/lowongan/delete/{id}', [LowonganController::class, 'softdelete'])->name('lowongan.softdelete');

        Route::get('/profil', [ProfilPenyediaController::class, 'index'])->name('profil.penyedia.index');
        Route::put('/profil/update/{id}', [ProfilPenyediaController::class, 'update'])->name('profil.penyedia.update');
        Route::get('/bkk', [BkkPenyediaController::class, 'index'])->name('bkk.penyedia.index');
        Route::get('/pencari_diterima', [DiterimaPencariController::class, 'index'])->name('pencari_diterima.index');
    });


    Route::prefix('ak1')->group(function () {
        Route::post('/daftar-akun', [Ak1Controller::class, 'daftar_akun'])->name('daftar-akun-ak1');
        Route::get('/daftar-by-admin', [Ak1Controller::class, 'daftar_by_admin']); //with role
        Route::post('/cek-awal-akun', [Ak1Controller::class, 'cek_awal_akun'])->name('cek-awal-akun-ak1');
        Route::post('/cek-awal-otp', [Ak1Controller::class, 'cek_awal_otp'])->name('cek-awal-otp-ak1');
        Route::post('/akhir_daftar-akun', [Ak1Controller::class, 'akhir_daftar_akun'])->name('akhir-daftar-akun-ak1');

        Route::get('/data', [Ak1Controller::class, 'dataAk1'])->name('ak1.data');

        Route::get('/existing', [Ak1Controller::class, 'cetakExisting'])->name('ak1.existing');
        Route::get('/print/{id}', [Ak1Controller::class, 'printAk1'])->name('ak1.print');
        Route::put('/update/{id}', [Ak1Controller::class, 'updateUser'])->name('ak1.update');
    });

    Route::prefix('admins')->middleware(CheckUserRole::class . ':super-admin,admin-provinsi,admin-kabkota,admin-kabkota-officer')->group(function () {
        Route::get('/lowongan', [LowonganAdminController::class, 'index'])->name('lowongan.admin.index');
        Route::get('/lowongan/get/{id}', [LowonganAdminController::class, 'show'])->name('lowongan.admin.detail');
        Route::put('/lowongan/update/{id}', [LowonganAdminController::class, 'update'])->name('lowongan.admin.update');

        Route::get('/penempatan', [PenempatanController::class, 'index'])->name('penempatan.admin.index');
    });

    Route::prefix('pencaris')->group(function () {
        Route::get('/lowongan', [LowonganPencariController::class, 'index'])->name('lowongan.pencari.index');
        Route::get('/lowongan/get/{id}', [LowonganPencariController::class, 'show'])->name('lowongan.pencari.detail');
        Route::put('/lowongan/lamar/{id}', [LowonganPencariController::class, 'lamar'])->name('lowongan.pencari.lamar');

        Route::get('/profil', [ProfilPencariController::class, 'index'])->name('profil.pencari.index');
        Route::put('/profil/update/{id}', [ProfilPencariController::class, 'update'])->name('profil.pencari.update');
        Route::get('/profil/pendidikan_formal', [ProfilPencariController::class, 'pendidikanformal'])->name('profilpendidikanformal.pencari.index');
        Route::post('/profil/add_pendidikanformal', [ProfilPencariController::class, 'store_pendidikanformal'])->name('profilpendidikanformal.pencari.store');
        Route::delete('/profil/delete_pendidikanformal/{id}', [ProfilPencariController::class, 'delete_pendidikanformal'])->name('profilpendidikanformal.pencari.destroy');

        Route::get('/profil/keahlian', [ProfilPencariController::class, 'keahlian'])->name('profilkeahlian.pencari.index');
        Route::post('/profil/add_keahlian', [ProfilPencariController::class, 'store_keahlian'])->name('profilkeahlian.pencari.store');
        Route::delete('/profil/delete_keahlian/{id}', [ProfilPencariController::class, 'delete_keahlian'])->name('profilkeahlian.pencari.destroy');

        Route::get('/ak1', [Ak1PencariController::class, 'index'])->name('ak1.index');
        Route::get('/ak1/print', [Ak1PencariController::class, 'printAk1'])->name('ak1.pencari.print');

        Route::get('/history_lamaran', [HistoryLamaranPencariController::class, 'index'])->name('historylamaran.pencari.index');
    });

    Route::prefix('bkks')->group(function () {
        Route::get('/profil', [ProfilBkkController::class, 'index'])->name('profil.bkk.index');
        Route::put('/profil/update/{id}', [ProfilBkkController::class, 'update'])->name('profil.bkk.update');

        Route::get('/alumni', [AlumniController::class, 'index'])->name('alumni.index');
        Route::get('/alumni/detail/{id}', [AlumniController::class, 'show'])->name('alumni.detail');

        Route::get('/perusahaan', [PerusahaanController::class, 'index'])->name('perusahaan.index');
        Route::get('/daftar-perusahaan-by-bkk', [PerusahaanController::class, 'daftarperusahaan'])->name('daftarperusahaan.index');
        Route::post('/akhir_daftar-akun-perush', [PerusahaanController::class, 'akhir_daftar_akun_perush_bybkk'])->name('akhir-daftar-akun-perush-bybkk');
        Route::delete('/perusahaan/delete/{id}', [PerusahaanController::class, 'softdelete'])->name('perusahaan.softdelete');
        Route::get('/perusahaan_lowongan/{id}', [PerusahaanController::class, 'perusahaan_lowongan'])->name('perusahaan.lowongan');
        Route::post('/perusahaan_lowongan/add', [PerusahaanController::class, 'store_lowongan'])->name('perusahaanlowongan.add');
        Route::delete('/perusahaan_lowongan/delete/{id}', [PerusahaanController::class, 'softdelete_lowongan'])->name('perusahaanlowongan.softdelete');

        Route::get('/bkk', [BkkController::class, 'index'])->name('bkk.index');
        Route::get('/cekaja', [PerusahaanController::class, 'cekaja']);
    });

    //ubah password
    Route::post('/ubah-password', [BackController::class, 'ubahPassword'])->name('ubah-password');
});

Route::prefix('eksekutif')->group(function () {
    Route::get('dashboard-pimpinan', [DashboardPimpinanController::class, 'index'])->name('dashboard.pimpinan.index');
});


Route::middleware('guest')->get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/act_login', [AuthController::class, 'login'])->name('login.action');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/get-kecamatan', [BackController::class, 'getKecamatan']);

// Route::name('setting')->prefix('setting')->group(function () {
//     Route::get('/banner', [BackController::class, 'settingBanner'])->name('banner');
//   });

Route::get('ak1/cek/{unik_kode}', [Ak1Controller::class, 'viewAk1'])->name('ak1.view');
