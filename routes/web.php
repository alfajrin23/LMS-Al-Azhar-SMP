<?php
use App\Http\Controllers\GuruTahfidzController;
use App\Http\Controllers\GuruTugasController;
use App\Http\Controllers\GuruNilaiController;
use App\Http\Controllers\GuruAbsensiController;
use App\Http\Controllers\GuruCatatanController;
use App\Http\Controllers\SiswaKondisiKelasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');
Route::post('/dashboard/pesan-kinerja', [DashboardController::class, 'kirimPesanKinerja'])
    ->middleware(['auth', 'verified', 'role:kepala_sekolah'])->name('dashboard.pesan-kinerja');
Route::post('/dashboard/pesan-wali', [DashboardController::class, 'kirimPesanWali'])
    ->middleware(['auth', 'verified', 'role:kepala_sekolah'])->name('dashboard.pesan-wali');
Route::middleware(['auth', 'verified', 'role:kepala_sekolah'])->group(function () {
    Route::get('/kepala-sekolah/export/siswa', [\App\Http\Controllers\ExportController::class, 'kepalaSiswa'])->name('kepala.export.siswa');
    Route::get('/kepala-sekolah/export/guru', [\App\Http\Controllers\ExportController::class, 'kepalaGuru'])->name('kepala.export.guru');
    Route::get('/kepala-sekolah/export/orang-tua', [\App\Http\Controllers\ExportController::class, 'kepalaOrangTua'])->name('kepala.export.orang-tua');
    Route::post('/kepala-sekolah/tahfidz-progress', [\App\Http\Controllers\KepalaTahfidzProgressController::class, 'store'])->name('kepala.tahfidz-progress.store');
});
Route::middleware('auth')->group(function () {
    Route::middleware('role:guru')->group(function () {
        Route::post('/guru/tahfidz', [GuruTahfidzController::class, 'store'])->name('guru.tahfidz.store');
        Route::post('/guru/tahfidz/{tahfidzSetoran}/nilai-pembanding', [GuruTahfidzController::class, 'storePembanding'])->name('guru.tahfidz.store-pembanding');
        Route::post('/guru/tugas', [GuruTugasController::class, 'store'])->name('guru.tugas.store');
        Route::get('/guru/tugas/{tugas}/download', [GuruTugasController::class, 'download'])->name('guru.tugas.download');
        Route::get('/guru/tugas/pengumpulan/{pengumpulan}/download', [GuruTugasController::class, 'downloadPengumpulan'])->name('guru.tugas.pengumpulan.download');
        Route::post('/guru/nilai', [GuruNilaiController::class, 'store'])->name('guru.nilai.store');
        Route::post('/guru/kti', [\App\Http\Controllers\GuruKtiController::class, 'store'])->name('guru.kti.store');
        Route::post('/guru/laporan', [\App\Http\Controllers\GuruLaporanController::class, 'store'])->name('guru.laporan.store');
        Route::post('/guru/absensi', [GuruAbsensiController::class, 'store'])->name('guru.absensi.store');
        Route::post('/guru/catatan', [GuruCatatanController::class, 'store'])->name('guru.catatan.store');
        Route::get('/guru/export/nilai', [\App\Http\Controllers\ExportController::class, 'nilaiCsv'])->name('guru.export.nilai');
        Route::post('/guru/workbook', [\App\Http\Controllers\GuruWorkbookController::class, 'store'])->name('guru.workbook.store');
        Route::post('/guru/workbook/{workbook}/soal', [\App\Http\Controllers\GuruWorkbookController::class, 'storeSoal'])->name('guru.workbook.soal');
        Route::post('/guru/materi', [\App\Http\Controllers\GuruMateriController::class, 'store'])->name('guru.materi.store');
        Route::post('/guru/materi/{materi}/update', [\App\Http\Controllers\GuruMateriController::class, 'update'])->name('guru.materi.update');
        Route::post('/guru/materi/{materi}/delete', [\App\Http\Controllers\GuruMateriController::class, 'destroy'])->name('guru.materi.delete');
        Route::post('/guru/pengumuman', [\App\Http\Controllers\GuruPengumumanController::class, 'store'])->name('guru.pengumuman.store');
        Route::post('/guru/kelas', [\App\Http\Controllers\GuruKelasController::class, 'store'])->name('guru.kelas.store');
        Route::get('/guru/kelas/siswa/{id}/detail', [\App\Http\Controllers\GuruKelasController::class, 'getSiswaDetail'])->name('guru.siswa.detail');
        Route::post('/guru/nilai-tugas', [\App\Http\Controllers\NilaiTugasController::class, 'store'])->name('guru.nilai-tugas.store');
        Route::prefix('guru/cbt')->name('guru.cbt.')->group(function () {
            Route::get('/', [\App\Http\Controllers\GuruCbtController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\GuruCbtController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\GuruCbtController::class, 'store'])->name('store');
            Route::get('/{cbtExam}/add-soal', [\App\Http\Controllers\GuruCbtController::class, 'addSoal'])->name('add-soal');
            Route::post('/{cbtExam}/soal', [\App\Http\Controllers\GuruCbtController::class, 'storeSoal'])->name('store-soal');
            Route::delete('/{cbtExam}/soal/{cbtSoal}', [\App\Http\Controllers\GuruCbtController::class, 'deleteSoal'])->name('delete-soal');
            Route::post('/{cbtExam}/ajukan', [\App\Http\Controllers\GuruCbtController::class, 'ajukan'])->name('ajukan');
            Route::get('/{cbtExam}/print', [\App\Http\Controllers\GuruCbtController::class, 'printExam'])->name('print');
        });
        Route::post('/guru/banding/{banding}/proses', [\App\Http\Controllers\GuruBandingController::class, 'proses'])->name('guru.banding.proses');
        Route::post('/guru/kti/bimbingan/{bimbingan}/proses', [\App\Http\Controllers\GuruKtiController::class, 'prosesBimbingan'])->name('guru.kti.proses-bimbingan');
        Route::post('/guru/kti/{kti}/jadwal', [\App\Http\Controllers\GuruKtiController::class, 'jadwalSidang'])->name('guru.kti.jadwal');
        Route::post('/guru/kehadiran', [\App\Http\Controllers\GuruKehadiranController::class, 'store'])->name('guru.kehadiran.store');
    });
    Route::middleware('role:siswa_sd,siswa_smp')->group(function () {
        Route::post('/siswa/kondisi-kelas', [SiswaKondisiKelasController::class, 'store'])->name('siswa.kondisi-kelas.store');
        Route::post('/siswa/pesan', [\App\Http\Controllers\SiswaPesanController::class, 'store'])->name('siswa.pesan.store');
        Route::post('/siswa/tugas/kumpul', [\App\Http\Controllers\SiswaTugasController::class, 'kumpul'])->name('siswa.tugas.kumpul');
        Route::post('/siswa/banding', [\App\Http\Controllers\SiswaBandingController::class, 'store'])->name('siswa.banding.store');
        Route::post('/siswa/kti/bimbingan', [\App\Http\Controllers\SiswaKtiController::class, 'store'])->name('siswa.kti.bimbingan');
        Route::prefix('siswa')->name('siswa.')->group(function () {
            Route::get('/workbook', [\App\Http\Controllers\SiswaWorkbookController::class, 'index'])->name('workbook.index');
            Route::get('/workbook/{workbook}/kerjakan', [\App\Http\Controllers\SiswaWorkbookController::class, 'kerjakan'])->name('workbook.kerjakan');
            Route::post('/workbook/{workbook}/submit', [\App\Http\Controllers\SiswaWorkbookController::class, 'submit'])->name('workbook.submit');
            Route::get('/workbook/{workbook}/hasil', [\App\Http\Controllers\SiswaWorkbookController::class, 'hasil'])->name('workbook.hasil');
        });
        Route::prefix('siswa/cbt')->name('siswa.cbt.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SiswaCbtController::class, 'index'])->name('index');
            Route::get('/{cbtExam}/kerjakan', [\App\Http\Controllers\SiswaCbtController::class, 'kerjakan'])->name('kerjakan');
            Route::post('/{cbtExam}/submit', [\App\Http\Controllers\SiswaCbtController::class, 'submit'])->name('submit');
            Route::get('/{cbtExam}/hasil', [\App\Http\Controllers\SiswaCbtController::class, 'hasil'])->name('hasil');
        });
    });
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/materi/{materi}/approve', [\App\Http\Controllers\GuruMateriController::class, 'approve'])->name('admin.materi.approve');
        Route::post('/admin/materi/{materi}/reject', [\App\Http\Controllers\GuruMateriController::class, 'reject'])->name('admin.materi.reject');
        Route::prefix('admin/cbt')->name('admin.cbt.')->group(function () {
            Route::get('/', [\App\Http\Controllers\AdminCbtController::class, 'index'])->name('index');
            Route::post('/{cbtExam}/approve', [\App\Http\Controllers\AdminCbtController::class, 'approve'])->name('approve');
            Route::post('/{cbtExam}/reject', [\App\Http\Controllers\AdminCbtController::class, 'reject'])->name('reject');
        });
        Route::post('/admin/siswa', [DashboardController::class, 'storeSiswa'])->name('admin.siswa.store');
        Route::post('/admin/guru', [DashboardController::class, 'storeGuru'])->name('admin.guru.store');
        Route::post('/admin/ortu', [DashboardController::class, 'storeOrtu'])->name('admin.ortu.store');
        Route::post('/admin/kelas', [DashboardController::class, 'storeKelas'])->name('admin.kelas.store');
        Route::put('/admin/siswa/{siswa}', [DashboardController::class, 'updateSiswa'])->name('admin.siswa.update');
        Route::put('/admin/guru/{guru}', [DashboardController::class, 'updateGuru'])->name('admin.guru.update');
        Route::put('/admin/ortu/{user}', [DashboardController::class, 'updateOrtu'])->name('admin.ortu.update');
        Route::put('/admin/kelas/{kelas}', [DashboardController::class, 'updateKelas'])->name('admin.kelas.update');
    });
    Route::middleware('role:orang_tua')->group(function () {
        Route::post('/ortu/bayar', [\App\Http\Controllers\OrtuBayarController::class, 'store'])->name('ortu.bayar.store');
        Route::post('/ortu/pesan', [\App\Http\Controllers\OrtuPesanController::class, 'store'])->name('ortu.pesan.store');
    });
    Route::get('/materi/{materi}/download', [\App\Http\Controllers\GuruMateriController::class, 'download'])->name('materi.download');
    Route::get('/rapor/pdf', [\App\Http\Controllers\RaporController::class, 'pdf'])
        ->middleware('role:siswa_sd,siswa_smp,orang_tua')->name('rapor.pdf');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/search', [\App\Http\Controllers\SearchController::class, 'search'])->name('search');
});
require __DIR__.'/auth.php';
