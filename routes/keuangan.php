<?php

use Illuminate\Support\Facades\Route;
use App\Keuangan\Http\Controllers\DashboardController;
use App\Keuangan\Http\Controllers\ProfileController;
use App\Keuangan\Http\Controllers\SantriController;
use App\Keuangan\Http\Controllers\SantriKeuanganController;
use App\Keuangan\Http\Controllers\TransaksiController;
use App\Keuangan\Http\Controllers\SppController;
use App\Keuangan\Http\Controllers\PengeluaranController;
use App\Keuangan\Http\Controllers\PemasukanController;
use App\Keuangan\Http\Controllers\KategoriPengeluaranController;
use App\Keuangan\Http\Controllers\KategoriPemasukanController;
use App\Keuangan\Http\Controllers\TagihanController;
use App\Keuangan\Http\Controllers\BiayaLainController;
use App\Keuangan\Http\Controllers\KeringananController;
use App\Keuangan\Http\Controllers\TemplateController;
use App\Keuangan\Http\Controllers\LaporanController;
use App\Keuangan\Http\Controllers\LaporanHarianController;
use App\Keuangan\Http\Controllers\TabunganController;
use App\Keuangan\Http\Controllers\KenaikanKelasController;

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\BackupController as MainBackupController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Profil User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // --- SPP & ACADEMIC ---
    Route::middleware(['role:admin_utama,bendahara,kepala,kepala_madrasah'])->group(function () {
        // Santri (Read-Only / Financial Profile)
        Route::get('/santri', [SantriController::class, 'index'])->name('santri.index');
        Route::get('/santri/export', [SantriController::class, 'export'])->name('santri.export');
        Route::get('/santri/{id}', [SantriController::class, 'show'])->name('santri.show');
        Route::get('/santri/{id}/generate-bills', [SantriController::class, 'generateBills'])->name('santri.generate-bills');

        // Auxiliary Financial Views
        Route::get('/santri/{id}/keuangan', [SantriKeuanganController::class, 'index'])->name('santri.keuangan.index');
        Route::get('/santri/{id}/keuangan/history', [SantriKeuanganController::class, 'history'])->name('santri.keuangan.history');

        // Transaksi
        Route::get('/pembayaran', [TransaksiController::class, 'index'])->name('pembayaran.index');
        Route::get('/pembayaran/{id}/proses', [TransaksiController::class, 'create'])->name('pembayaran.create');
        Route::post('/pembayaran/{id}/simpan', [TransaksiController::class, 'store'])->name('pembayaran.store');
        Route::get('/transaksi/{id}/edit', [TransaksiController::class, 'edit'])->name('pembayaran.edit');
        Route::put('/transaksi/{id}', [TransaksiController::class, 'update'])->name('pembayaran.update');
        Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('pembayaran.destroy');

        Route::get('/spp', [SppController::class, 'index'])->name('spp.index');
        Route::get('/spp/receipt', [SppController::class, 'receipt'])->name('spp.receipt');
        Route::get('/transaksi/riwayat', [TransaksiController::class, 'history'])->name('transaksi.history');
        Route::get('/transaksi/{id}/receipt', [TransaksiController::class, 'printReceipt'])->name('transaksi.receipt');
        Route::get('/transaksi/{id}/print-thermal', [TransaksiController::class, 'printThermal'])->name('transaksi.print-thermal');

        // Pengeluaran & Pemasukan
        Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
        Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
        Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

        Route::get('/pemasukan', [PemasukanController::class, 'index'])->name('pemasukan.index');
        Route::post('/pemasukan', [PemasukanController::class, 'store'])->name('pemasukan.store');
        Route::delete('/pemasukan/{id}', [PemasukanController::class, 'destroy'])->name('pemasukan.destroy');

        Route::resource('kategori-pengeluaran', KategoriPengeluaranController::class);
        Route::resource('kategori-pemasukan', KategoriPemasukanController::class);

        Route::resource('tagihan', TagihanController::class)->only(['edit', 'update', 'destroy']);
        Route::post('/tagihan/{id}/waive', [TagihanController::class, 'waive'])->name('tagihan.waive');
        Route::post('/tagihan/generate-future/{id}', [TagihanController::class, 'generateFuture'])->name('tagihan.generate-future');
        Route::post('/tagihan/reset/{id}', [TagihanController::class, 'resetBills'])->name('tagihan.reset');

        Route::post('/biaya-lain/{id}/toggle-status', [BiayaLainController::class, 'toggleStatus'])->name('biaya-lain.toggle-status');
        Route::resource('biaya-lain', BiayaLainController::class);

        Route::post('keringanan/{keringanan}/members', [KeringananController::class, 'addMember'])->name('keringanan.members.add');
        Route::delete('keringanan/{keringanan}/members/{santri}', [KeringananController::class, 'removeMember'])->name('keringanan.members.remove');
        Route::resource('keringanan', KeringananController::class);

        // Templates
        Route::get('/template/santri', [TemplateController::class, 'santri'])->name('template.santri');
        Route::get('/template/kelas', [TemplateController::class, 'kelas'])->name('template.kelas');
    });

    // --- REPORTS ---
    Route::middleware(['role:admin_utama,bendahara,staf_keuangan,kepala_madrasah'])->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/santri', [LaporanController::class, 'santri'])->name('laporan.santri');
        Route::get('/laporan/pengeluaran', [LaporanController::class, 'pengeluaran'])->name('laporan.pengeluaran');
        Route::get('/laporan/tunggakan', [LaporanController::class, 'tunggakan'])->name('laporan.tunggakan');
        Route::get('/laporan/harian', [LaporanHarianController::class, 'index'])->name('laporan.harian');
        Route::get('/laporan/tahunan', [LaporanController::class, 'tahunan'])->name('laporan.tahunan');
    });

    // --- SAVINGS ---
    Route::middleware(['role:admin_utama,teller_tabungan,bendahara,staf_keuangan'])->group(function () {
        Route::get('/tabungan', [TabunganController::class, 'index'])->name('tabungan.index');
        Route::get('/tabungan/{id}', [TabunganController::class, 'show'])->name('tabungan.show');
        Route::post('/tabungan/{id}', [TabunganController::class, 'store'])->name('tabungan.store');
    });

    // --- ADMIN ONLY ---
    Route::middleware(['role:admin_utama'])->group(function () {
        // Kenaikan Kelas
        Route::get('/kenaikan-kelas', [KenaikanKelasController::class, 'index'])->name('kenaikan-kelas.index');
        Route::post('/kenaikan-kelas', [KenaikanKelasController::class, 'process'])->name('kenaikan-kelas.process');
        Route::post('/kenaikan-kelas/magic', [KenaikanKelasController::class, 'magicProcess'])->name('kenaikan-kelas.magic');
        Route::post('/kenaikan-kelas/magic/execute', [KenaikanKelasController::class, 'executeMagicProcess'])->name('kenaikan-kelas.magic.execute');
        Route::delete('/kenaikan-kelas/history/{id}', [KenaikanKelasController::class, 'undoBatch'])->name('kenaikan-kelas.undo');

        // Pengaturan (Unified to Main Controllers)
        Route::get('/pengaturan/fix-storage', [SettingsController::class, 'fixStorageLink'])->name('pengaturan.storage.fix');
        Route::get('/pengaturan/logs', [SettingsController::class, 'logs'])->name('pengaturan.logs');
        Route::get('/pengaturan', [SettingsController::class, 'financeIndex'])->name('pengaturan.index');
        Route::post('/pengaturan/identity', [SettingsController::class, 'updateIdentity'])->name('pengaturan.identity.update');
        Route::post('/pengaturan/user', [SettingsController::class, 'storeUser'])->name('pengaturan.user.store');
        Route::put('/pengaturan/user/{id}', [SettingsController::class, 'updateUser'])->name('pengaturan.user.update');
        Route::delete('/pengaturan/user/{id}', [SettingsController::class, 'destroyUser'])->name('pengaturan.user.destroy');

        // Backup & Restore (Unified)
        Route::get('/pengaturan/backup', [MainBackupController::class, 'download'])->name('pengaturan.backup');
        Route::post('/pengaturan/restore', [MainBackupController::class, 'restore'])->name('pengaturan.restore');
        Route::post('/pengaturan/reset', [MaintenanceController::class, 'resetSystem'])->name('pengaturan.reset'); // Uses Main Reset System

        // WhatsApp (Unified)
        Route::get('/pengaturan/whatsapp', [SettingsController::class, 'whatsapp'])->name('pengaturan.whatsapp');
        Route::post('/pengaturan/whatsapp', [SettingsController::class, 'updateWhatsapp'])->name('pengaturan.whatsapp.update');
        Route::post('/pengaturan/whatsapp/test', [SettingsController::class, 'testWhatsapp'])->name('pengaturan.whatsapp.test');
    });
});
