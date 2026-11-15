<?php

use App\Http\Controllers\Admin\DaftarUlangController as AdminDaftarUlangController;
use App\Http\Controllers\Admin\InformasiPpdbController;
use App\Http\Controllers\Admin\JadwalPpdbController;
use App\Http\Controllers\Admin\JenisDokumenController;
use App\Http\Controllers\Admin\KuotaController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PencarianController;
use App\Http\Controllers\Admin\PendaftarController;
use App\Http\Controllers\Admin\PengumumanController;
use App\Http\Controllers\Admin\PersyaratanController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\VerifikasiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Calon\CetakBuktiController;
use App\Http\Controllers\Calon\DaftarUlangController;
use App\Http\Controllers\Calon\DashboardController;
use App\Http\Controllers\Calon\DokumenController;
use App\Http\Controllers\Calon\PendaftaranController;
use App\Http\Controllers\Calon\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PpdbController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Halaman Publik (Bisa diakses tanpa login)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/informasi', [PpdbController::class, 'informasi'])->name('informasi');
Route::get('/jadwal', [PpdbController::class, 'jadwal'])->name('jadwal');
Route::get('/persyaratan', [PpdbController::class, 'persyaratan'])->name('persyaratan');
Route::get('/alur', [PpdbController::class, 'alur'])->name('alur');
Route::get('/jurusan', [PpdbController::class, 'jurusan'])->name('jurusan');
Route::get('/pengumuman', [PpdbController::class, 'pengumuman'])->name('pengumuman');
Route::post('/pengumuman/cek', [PpdbController::class, 'cekPengumuman'])->name('pengumuman.cek');

// 2. Auth Routes (Guest: Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout Route (Harus Login)
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// 3. Area Pendaftar
Route::middleware(['auth', 'role:pendaftar'])->group(function () {
    // Redirect URL lama pasca penghapusan jalur
    Route::redirect('/calon/pendaftaran/jalur-program', '/calon/pendaftaran/program', 301);
    Route::redirect('/calon/formulir', '/calon/pendaftaran/data-diri', 301);
    Route::redirect('/calon/pendaftaran/jurusan', '/calon/pendaftaran/program', 301);

    Route::get('/calon/dashboard', [DashboardController::class, 'index'])->name('calon.dashboard');

    Route::get('/calon/formulir', [PendaftaranController::class, 'create'])->name('calon.formulir');
    Route::get('/calon/pendaftaran', [PendaftaranController::class, 'create'])->name('calon.pendaftaran.index');

    Route::get('/calon/pendaftaran/data-diri', [PendaftaranController::class, 'dataDiri'])->name('calon.pendaftaran.data-diri');
    Route::post('/calon/pendaftaran/data-diri', [PendaftaranController::class, 'saveDataDiri'])->name('calon.pendaftaran.data-diri.store');

    Route::get('/calon/pendaftaran/orang-tua', [PendaftaranController::class, 'orangTua'])->name('calon.pendaftaran.orang-tua');
    Route::post('/calon/pendaftaran/orang-tua', [PendaftaranController::class, 'saveOrangTua'])->name('calon.pendaftaran.orang-tua.store');

    Route::get('/calon/pendaftaran/program', [PendaftaranController::class, 'program'])->name('calon.pendaftaran.program');
    Route::post('/calon/pendaftaran/program', [PendaftaranController::class, 'saveProgram'])->name('calon.pendaftaran.program.store');

    Route::get('/calon/pendaftaran/dokumen', [DokumenController::class, 'index'])->name('calon.pendaftaran.dokumen');
    Route::post('/calon/pendaftaran/dokumen', [DokumenController::class, 'store'])->name('calon.dokumen.store');
    Route::get('/calon/pendaftaran/review', [PendaftaranController::class, 'review'])->name('calon.pendaftaran.review');
    Route::post('/calon/pendaftaran/submit', [PendaftaranController::class, 'submit'])->name('calon.pendaftaran.submit');
    Route::get('/calon/pendaftaran/{pendaftaran}', [PendaftaranController::class, 'show'])
        ->whereNumber('pendaftaran')
        ->name('calon.pendaftaran.show');

    Route::get('/calon/dokumen', fn () => redirect()->route('calon.pendaftaran.dokumen'))->name('calon.dokumen');
    Route::get('/calon/dokumen/{dokumen}/preview', [DokumenController::class, 'preview'])
        ->whereNumber('dokumen')
        ->name('calon.dokumen.preview');
    Route::delete('/calon/dokumen/{dokumen}', [DokumenController::class, 'destroy'])
        ->whereNumber('dokumen')
        ->name('calon.dokumen.destroy');

    Route::get('/calon/hasil', [PendaftaranController::class, 'hasilSeleksi'])->name('calon.hasil');
    Route::get('/calon/status', [PendaftaranController::class, 'status'])->name('calon.status');

    Route::get('/calon/pendaftaran/cetak', [PendaftaranController::class, 'cetakBukti'])->name('calon.pendaftaran.cetak');

    Route::get('/calon/daftar-ulang', [DaftarUlangController::class, 'index'])->name('calon.daftar-ulang');
    Route::post('/calon/daftar-ulang', [DaftarUlangController::class, 'store'])->name('calon.daftar-ulang.store');

    Route::get('/calon/pendaftaran/pdf', [CetakBuktiController::class, 'index'])->name('calon.pendaftaran.pdf');
    Route::get('/calon/pendaftaran/cetak-pdf', [CetakBuktiController::class, 'cetak'])->name('calon.pendaftaran.cetak-pdf');

    Route::get('/calon/profil', [ProfileController::class, 'edit'])->name('calon.profil.edit');
    Route::put('/calon/profil', [ProfileController::class, 'update'])->name('calon.profil.update');
});

// 4. Area Administrator
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Redirect URL lama pasca penghapusan jalur
    Route::redirect('/admin/jalur', '/admin/dashboard', 301);

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/pencarian', [PencarianController::class, 'index'])->name('admin.pencarian');

    Route::get('/admin/verifikasi', [VerifikasiController::class, 'index'])->name('admin.verifikasi');
    Route::get('/admin/verifikasi/{id}', [VerifikasiController::class, 'show'])->name('admin.verifikasi.show');
    Route::post('/admin/verifikasi/{id}', [VerifikasiController::class, 'update'])->name('admin.verifikasi.update');

    Route::get('/admin/dokumen/{dokumen}/preview', [DokumenController::class, 'preview'])
        ->whereNumber('dokumen')
        ->name('admin.dokumen.preview');

    Route::get('/admin/kuota', [KuotaController::class, 'index'])->name('admin.kuota.index');
    Route::post('/admin/kuota/{program}', [KuotaController::class, 'update'])->name('admin.kuota.update');

    Route::get('/admin/pendaftar', [PendaftarController::class, 'index'])->name('admin.pendaftar.index');
    Route::get('/admin/pendaftar/{pendaftaran}', [PendaftarController::class, 'show'])
        ->whereNumber('pendaftaran')
        ->name('admin.pendaftar.show');

    Route::get('/admin/pengumuman', [PengumumanController::class, 'index'])->name('admin.pengumuman.index');

    Route::get('/admin/daftar-ulang', [AdminDaftarUlangController::class, 'index'])->name('admin.daftar-ulang');
    Route::post('/admin/daftar-ulang/{id}', [AdminDaftarUlangController::class, 'update'])->name('admin.daftar-ulang.update');

    Route::get('/admin/daftar-ulang/cek-layak/{user}', [AdminDaftarUlangController::class, 'cekLayak'])->name('admin.daftar-ulang.cek-layak');

    Route::resource('admin/program', ProgramController::class)->names([
        'index' => 'admin.program.index',
        'create' => 'admin.program.create',
        'store' => 'admin.program.store',
        'edit' => 'admin.program.edit',
        'update' => 'admin.program.update',
        'destroy' => 'admin.program.destroy',
    ]);

    Route::resource('admin/jenis-dokumen', JenisDokumenController::class)->names([
        'index' => 'admin.jenis-dokumen.index',
        'create' => 'admin.jenis-dokumen.create',
        'store' => 'admin.jenis-dokumen.store',
        'edit' => 'admin.jenis-dokumen.edit',
        'update' => 'admin.jenis-dokumen.update',
        'destroy' => 'admin.jenis-dokumen.destroy',
    ]);

    Route::resource('admin/informasi', InformasiPpdbController::class)->names([
        'index' => 'admin.informasi.index',
        'create' => 'admin.informasi.create',
        'store' => 'admin.informasi.store',
        'edit' => 'admin.informasi.edit',
        'update' => 'admin.informasi.update',
        'destroy' => 'admin.informasi.destroy',
    ]);

    Route::resource('admin/jadwal', JadwalPpdbController::class)->names([
        'index' => 'admin.jadwal.index',
        'create' => 'admin.jadwal.create',
        'store' => 'admin.jadwal.store',
        'edit' => 'admin.jadwal.edit',
        'update' => 'admin.jadwal.update',
        'destroy' => 'admin.jadwal.destroy',
    ]);

    Route::resource('admin/persyaratan', PersyaratanController::class)->names([
        'index' => 'admin.persyaratan.index',
        'create' => 'admin.persyaratan.create',
        'store' => 'admin.persyaratan.store',
        'edit' => 'admin.persyaratan.edit',
        'update' => 'admin.persyaratan.update',
        'destroy' => 'admin.persyaratan.destroy',
    ]);

    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan');

    Route::get('/admin/laporan-detail', fn () => redirect()->route('admin.laporan'))->name('admin.laporan-detail');

    Route::get('/admin/laporan/export-csv', [LaporanController::class, 'exportCsv'])->name('admin.laporan.export-csv');

    Route::get('/admin/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('admin.laporan.export-pdf');
});