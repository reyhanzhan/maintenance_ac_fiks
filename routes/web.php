<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeknisiController;
use Illuminate\Support\Facades\Route;

// Sitemap
Route::get('/sitemap.xml', function () {
    $urls = [
        ['loc' => url('/'), 'changefreq' => 'monthly', 'priority' => '1.0'],
    ];
    return response()->view('sitemap', compact('urls'))
        ->header('Content-Type', 'application/xml');
});

// Auth
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// API for dynamic dropdown
Route::get('/api/ruangan/{rumahSakit}', [TeknisiController::class, 'getRuangan'])->middleware('auth');

// Teknisi
Route::middleware(['auth', 'role:teknisi'])->prefix('teknisi')->group(function () {
    Route::get('/', [TeknisiController::class, 'index'])->name('teknisi.index');
    Route::get('/create', [TeknisiController::class, 'create'])->name('teknisi.create');
    Route::post('/store', [TeknisiController::class, 'store'])->name('teknisi.store');
    Route::get('/report/{report}', [TeknisiController::class, 'show'])->name('teknisi.show');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/{report}', [AdminController::class, 'showReport'])->name('admin.reports.show');
    Route::get('/reports/{report}/pdf', [AdminController::class, 'exportReportPdf'])->name('admin.reports.pdf');

    // Surat Jalan
    Route::get('/surat-jalan', [AdminController::class, 'suratJalanIndex'])->name('admin.surat-jalan.index');
    Route::get('/surat-jalan/create', [AdminController::class, 'suratJalanCreate'])->name('admin.surat-jalan.create');
    Route::post('/surat-jalan', [AdminController::class, 'suratJalanStore'])->name('admin.surat-jalan.store');
    Route::get('/surat-jalan/{suratJalan}', [AdminController::class, 'suratJalanShow'])->name('admin.surat-jalan.show');
    Route::get('/surat-jalan/{suratJalan}/pdf', [AdminController::class, 'suratJalanExportPdf'])->name('admin.surat-jalan.pdf');

    // Master Data - Rumah Sakit
    Route::get('/rumah-sakit', [AdminController::class, 'rumahSakitIndex'])->name('admin.rumah-sakit.index');
    Route::post('/rumah-sakit', [AdminController::class, 'rumahSakitStore'])->name('admin.rumah-sakit.store');
    Route::put('/rumah-sakit/{rumahSakit}', [AdminController::class, 'rumahSakitUpdate'])->name('admin.rumah-sakit.update');
    Route::delete('/rumah-sakit/{rumahSakit}', [AdminController::class, 'rumahSakitDestroy'])->name('admin.rumah-sakit.destroy');
    Route::get('/rumah-sakit/{rumahSakit}/ruangan', [AdminController::class, 'ruanganIndex'])->name('admin.ruangan.index');
    Route::post('/rumah-sakit/{rumahSakit}/ruangan', [AdminController::class, 'ruanganStore'])->name('admin.ruangan.store');
    Route::put('/ruangan/{ruangan}', [AdminController::class, 'ruanganUpdate'])->name('admin.ruangan.update');
    Route::delete('/ruangan/{ruangan}', [AdminController::class, 'ruanganDestroy'])->name('admin.ruangan.destroy');
    Route::get('/rumah-sakit/{rumahSakit}/ac-unit', [AdminController::class, 'acUnitIndex'])->name('admin.ac-unit.index');
    Route::post('/rumah-sakit/{rumahSakit}/ac-unit', [AdminController::class, 'acUnitStore'])->name('admin.ac-unit.store');
    Route::put('/ac-unit/{acUnit}', [AdminController::class, 'acUnitUpdate'])->name('admin.ac-unit.update');
    Route::delete('/ac-unit/{acUnit}', [AdminController::class, 'acUnitDestroy'])->name('admin.ac-unit.destroy');

    // Master Data - Koordinator Lapangan RS
    Route::get('/koordinator-rs', [AdminController::class, 'koordinatorRsIndex'])->name('admin.koordinator-rs.index');
    Route::put('/koordinator-rs/{rumahSakit}', [AdminController::class, 'koordinatorRsUpdate'])->name('admin.koordinator-rs.update');
    Route::get('/koordinator-surat-jalan', [AdminController::class, 'koordinatorSuratJalanIndex'])->name('admin.koordinator-surat-jalan.index');
    Route::put('/koordinator-surat-jalan/{rumahSakit}', [AdminController::class, 'koordinatorSuratJalanUpdate'])->name('admin.koordinator-surat-jalan.update');

    // Master Data - Teknisi
    Route::get('/teknisi', [AdminController::class, 'teknisiIndex'])->name('admin.teknisi.index');
    Route::post('/teknisi', [AdminController::class, 'teknisiStore'])->name('admin.teknisi.store');
    Route::put('/teknisi/{user}', [AdminController::class, 'teknisiUpdate'])->name('admin.teknisi.update');
    Route::delete('/teknisi/{user}', [AdminController::class, 'teknisiDestroy'])->name('admin.teknisi.destroy');
    Route::post('/teknisi/{user}/signature', [AdminController::class, 'teknisiUpdateSignature'])->name('admin.teknisi.signature');

    // Backup Data
    Route::get('/backup', [AdminController::class, 'backupIndex'])->name('admin.backup.index');
    Route::get('/backup/download', [AdminController::class, 'backupDownload'])->name('admin.backup.download');
});
