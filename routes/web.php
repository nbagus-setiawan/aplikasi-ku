<?php

use App\Http\Controllers\Admin\NotifikasiController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HalamanController;
use App\Http\Controllers\KategoriSuratController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PengurusController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\SantriImportController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\TandaTanganPublikController;
use App\Http\Controllers\VerifikasiPublikController;
use Illuminate\Support\Facades\Route;

// Halaman Publik (Fase 5)
Route::get('/', [PublicController::class, 'beranda'])->name('home');
Route::get('/pengumuman', [PublicController::class, 'pengumumanIndex'])->name('publik.pengumuman.index');
Route::get('/pengumuman/{slug}', [PublicController::class, 'pengumumanShow'])->name('publik.pengumuman.show');
Route::get('/p/{identifier}', [PublicController::class, 'pengumumanShortShow'])->name('publik.pengumuman.short');
Route::post('/pengumuman/{slug}/like', [PublicController::class, 'likePengumuman'])->middleware('throttle:30,1')->name('publik.pengumuman.like');
Route::post('/pengumuman/{slug}/komentar', [PublicController::class, 'komentarPengumuman'])->middleware('throttle:15,1')->name('publik.pengumuman.komentar');
Route::post('/pengumuman/{slug}/share', [PublicController::class, 'sharePengumuman'])->middleware('throttle:30,1')->name('publik.pengumuman.share');
Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('publik.sitemap');

// Rute Publik: Layanan Konsultasi & Verifikasi Surat & Tanda Tangan Jarak Jauh
Route::get('/konsultasi', [PublicController::class, 'konsultasi'])->name('publik.konsultasi');
Route::get('/verifikasi', [VerifikasiPublikController::class, 'index'])->name('publik.verifikasi.index');
Route::get('/verifikasi/{kode}', [VerifikasiPublikController::class, 'show'])->middleware('throttle:60,1')->name('publik.verifikasi');
Route::get('/verifikasi/{kode}/unduh', [VerifikasiPublikController::class, 'unduh'])->middleware('throttle:30,1')->name('publik.verifikasi.unduh');
Route::get('/tanda-tangan/{token}', [TandaTanganPublikController::class, 'show'])->middleware('throttle:30,1')->name('publik.tanda-tangan.show');
Route::post('/tanda-tangan/{token}', [TandaTanganPublikController::class, 'sign'])->middleware('throttle:15,1')->name('publik.tanda-tangan.sign');

// Rute Admin dengan Middleware auth dan Awalan /admin
Route::middleware('auth')->prefix('admin')->group(function () {
    // Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Modul Santri (Fase 2)
    Route::get('/santri/cetak', [SantriController::class, 'cetakDaftar'])->name('admin.santri.cetak');
    Route::get('/santri/{santri}/cetak', [SantriController::class, 'cetakBiodata'])->name('admin.santri.biodata');
    Route::get('/santri-impor/template', [SantriImportController::class, 'template'])->name('admin.santri.template');
    Route::post('/santri-impor', [SantriImportController::class, 'import'])->name('admin.santri.impor');
    Route::resource('santri', SantriController::class)->names('admin.santri');

    // Modul Pengurus (Fase 2)
    Route::resource('pengurus', PengurusController::class)
        ->parameters(['pengurus' => 'pengurus'])
        ->names('admin.pengurus');

    // Modul Surat Masuk (Fase 3)
    Route::post('/surat-masuk/parse-docx', [SuratMasukController::class, 'parseDocx'])->name('admin.surat-masuk.parse-docx');
    Route::patch('/surat-masuk/{suratMasuk}/disposisi', [SuratMasukController::class, 'updateDisposisi'])->name('admin.surat-masuk.disposisi');
    Route::resource('surat-masuk', SuratMasukController::class)->names('admin.surat-masuk');

    // Modul Surat Keluar (Fase 3)
    Route::post('/surat-keluar/parse-docx', [SuratKeluarController::class, 'parseDocx'])->name('admin.surat-keluar.parse-docx');
    Route::post('/surat-keluar/template', [SuratKeluarController::class, 'storeTemplate'])->name('admin.surat-keluar.template.store');
    Route::delete('/surat-keluar/template/{templateSurat}', [SuratKeluarController::class, 'destroyTemplate'])->name('admin.surat-keluar.template.destroy');
    Route::get('/surat-keluar/{suratKeluar}/cetak', [SuratKeluarController::class, 'cetak'])->name('admin.surat-keluar.cetak');
    Route::post('/surat-keluar/{suratKeluar}/tautan-ttd', [SuratKeluarController::class, 'buatTautanTtd'])->name('admin.surat-keluar.tautan-ttd');
    Route::post('/surat-keluar/{suratKeluar}/ttd-digital', [SuratKeluarController::class, 'finalisasiDigital'])->name('admin.surat-keluar.ttd-digital');
    Route::post('/surat-keluar/{suratKeluar}/ttd-basah', [SuratKeluarController::class, 'finalisasiBasah'])->name('admin.surat-keluar.ttd-basah');
    Route::patch('/surat-keluar/{suratKeluar}/status', [SuratKeluarController::class, 'updateStatus'])->name('admin.surat-keluar.status');
    Route::resource('surat-keluar', SuratKeluarController::class)->names('admin.surat-keluar');

    // Modul Agenda Kegiatan (Fase 4)
    Route::resource('agenda', AgendaController::class)->names('admin.agenda');

    // Modul Arsip Dokumen (Fase 4)
    Route::get('/arsip/{arsip}/unduh', [ArsipController::class, 'unduh'])->name('admin.arsip.unduh');
    Route::resource('arsip', ArsipController::class)->names('admin.arsip');

    // Modul Editor Beranda & Halaman (Fase 5)
    Route::get('/halaman/beranda', [HalamanController::class, 'editBeranda'])->name('admin.halaman.beranda');
    Route::post('/halaman/beranda', [HalamanController::class, 'updateBeranda'])->name('admin.halaman.beranda.update');
    Route::post('/halaman/{halaman}/revisi/{revisi}/restore', [HalamanController::class, 'restoreRevisi'])->name('admin.halaman.revisi.restore');

    // Modul Warta Pengumuman (Fase 5)
    Route::resource('pengumuman', PengumumanController::class)->names('admin.pengumuman');

    // Modul Rekapitulasi & Laporan (Fase 6)
    Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('admin.laporan.index');
    Route::get('/laporan/cetak/surat-masuk', [\App\Http\Controllers\LaporanController::class, 'cetakSuratMasuk'])->name('admin.laporan.cetak.surat-masuk');
    Route::get('/laporan/cetak/surat-keluar', [\App\Http\Controllers\LaporanController::class, 'cetakSuratKeluar'])->name('admin.laporan.cetak.surat-keluar');
    Route::get('/laporan/cetak/santri', [\App\Http\Controllers\LaporanController::class, 'cetakSantri'])->name('admin.laporan.cetak.santri');
    Route::get('/laporan/cetak/agenda', [\App\Http\Controllers\LaporanController::class, 'cetakAgenda'])->name('admin.laporan.cetak.agenda');

    // Pengaturan Situs & Lembaga (Fase 1)
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('admin.pengaturan.index');
    Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('admin.pengaturan.update');
    Route::post('/pengaturan/backup', [PengaturanController::class, 'backupNow'])->name('admin.pengaturan.backup');
    Route::get('/pengaturan/backup/download', [PengaturanController::class, 'downloadBackup'])->name('admin.pengaturan.backup.download');
    Route::post('/pengaturan/restore', [PengaturanController::class, 'restoreBackup'])->name('admin.pengaturan.restore');

    // CRUD Kategori Surat Keluar (Fase 1)
    Route::post('/kategori-surat', [KategoriSuratController::class, 'store'])->name('admin.kategori-surat.store');
    Route::put('/kategori-surat/{kategoriSurat}', [KategoriSuratController::class, 'update'])->name('admin.kategori-surat.update');
    Route::delete('/kategori-surat/{kategoriSurat}', [KategoriSuratController::class, 'destroy'])->name('admin.kategori-surat.destroy');

    // Notifikasi Panel Sekretaris (Lonceng Navbar Admin)
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('admin.notifikasi.index');
    Route::patch('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'markAsRead'])->name('admin.notifikasi.baca');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'markAllRead'])->name('admin.notifikasi.baca-semua');
    Route::delete('/notifikasi/{notifikasi}', [NotifikasiController::class, 'destroy'])->name('admin.notifikasi.destroy');
    Route::post('/notifikasi/hapus-banyak', [NotifikasiController::class, 'destroyBatch'])->name('admin.notifikasi.hapus-banyak');
    Route::post('/notifikasi/hapus-semua', [NotifikasiController::class, 'destroyAll'])->name('admin.notifikasi.hapus-semua');
});

// Rute Profil & Dashboard Legacy Redirect
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
