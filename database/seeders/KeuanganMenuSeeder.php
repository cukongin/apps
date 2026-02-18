<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DynamicMenu;
use App\Models\MenuRole;

class KeuanganMenuSeeder extends Seeder
{
    public function run()
    {
        $roles = ['bendahara', 'staf_keuangan', 'admin_utama'];

        // 0. Label KEUANGAN
        $label = DynamicMenu::firstOrCreate(
            ['title' => 'KEUANGAN', 'type' => 'label'],
            ['order' => 20, 'is_active' => true, 'location' => 'sidebar']
        );
        foreach ($roles as $role) {
            MenuRole::firstOrCreate(['menu_id' => $label->id, 'role' => $role]);
        }

        // 1. Dashboard Keuangan
        $dashboard = DynamicMenu::firstOrCreate(
            ['route' => 'keuangan.dashboard'],
            ['title' => 'Dashboard Keuangan', 'icon' => 'dashboard', 'order' => 21, 'is_active' => true, 'location' => 'sidebar']
        );
        foreach ($roles as $role) {
            MenuRole::firstOrCreate(['menu_id' => $dashboard->id, 'role' => $role]);
        }

        // 2. Data Santri & Tagihan
        $santri = DynamicMenu::firstOrCreate(
            ['route' => 'keuangan.santri.index'],
            ['title' => 'Data Santri & Tagihan', 'icon' => 'person', 'order' => 22, 'is_active' => true, 'location' => 'sidebar']
        );
        foreach ($roles as $role) {
            MenuRole::firstOrCreate(['menu_id' => $santri->id, 'role' => $role]);
        }

        // 3. Transaksi (Pembayaran)
        $transaksiParent = DynamicMenu::firstOrCreate(
            ['title' => 'Transaksi', 'icon' => 'payments'],
            ['order' => 23, 'is_active' => true, 'location' => 'sidebar', 'url' => '#']
        );
        foreach ($roles as $role) {
            MenuRole::firstOrCreate(['menu_id' => $transaksiParent->id, 'role' => $role]);
        }
            // Children
            $pembayaran = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.pembayaran.index', 'parent_id' => $transaksiParent->id],
                ['title' => 'Pembayaran SPP/Lain', 'icon' => 'point_of_sale', 'order' => 1, 'is_active' => true, 'location' => 'sidebar']
            );
            $history = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.transaksi.history', 'parent_id' => $transaksiParent->id],
                ['title' => 'Riwayat Transaksi', 'icon' => 'history', 'order' => 2, 'is_active' => true, 'location' => 'sidebar']
            );
            $tabungan = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.tabungan.index', 'parent_id' => $transaksiParent->id],
                ['title' => 'Tabungan Santri', 'icon' => 'savings', 'order' => 3, 'is_active' => true, 'location' => 'sidebar']
            );
            foreach ($roles as $role) {
                MenuRole::firstOrCreate(['menu_id' => $pembayaran->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $history->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $tabungan->id, 'role' => $role]);
            }

        // 4. Pemasukan & Pengeluaran
        $cashflowParent = DynamicMenu::firstOrCreate(
            ['title' => 'Arus Kas', 'icon' => 'account_balance_wallet'],
            ['order' => 24, 'is_active' => true, 'location' => 'sidebar', 'url' => '#']
        );
        foreach ($roles as $role) {
            MenuRole::firstOrCreate(['menu_id' => $cashflowParent->id, 'role' => $role]);
        }
            // Children
            $pengeluaran = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.pengeluaran.index', 'parent_id' => $cashflowParent->id],
                ['title' => 'Pengeluaran', 'icon' => 'trending_down', 'order' => 1, 'is_active' => true, 'location' => 'sidebar']
            );
            $pemasukan = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.pemasukan.index', 'parent_id' => $cashflowParent->id],
                ['title' => 'Pemasukan Lain', 'icon' => 'trending_up', 'order' => 2, 'is_active' => true, 'location' => 'sidebar']
            );
            foreach ($roles as $role) {
                MenuRole::firstOrCreate(['menu_id' => $pengeluaran->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $pemasukan->id, 'role' => $role]);
            }

        // 5. Laporan
        $laporanParent = DynamicMenu::firstOrCreate(
            ['title' => 'Laporan', 'icon' => 'bar_chart'],
            ['order' => 25, 'is_active' => true, 'location' => 'sidebar', 'url' => '#']
        );
        foreach ($roles as $role) {
            MenuRole::firstOrCreate(['menu_id' => $laporanParent->id, 'role' => $role]);
        }
            // Children
            $lapHarian = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.laporan.harian', 'parent_id' => $laporanParent->id],
                ['title' => 'Laporan Harian', 'icon' => 'today', 'order' => 1, 'is_active' => true, 'location' => 'sidebar']
            );
            $lapBulanan = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.laporan.index', 'parent_id' => $laporanParent->id],
                ['title' => 'Laporan Bulanan', 'icon' => 'calendar_month', 'order' => 2, 'is_active' => true, 'location' => 'sidebar']
            );
            $lapTunggakan = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.laporan.tunggakan', 'parent_id' => $laporanParent->id],
                ['title' => 'Laporan Tunggakan', 'icon' => 'pending_actions', 'order' => 3, 'is_active' => true, 'location' => 'sidebar']
            );
            $lapPengeluaran = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.laporan.pengeluaran', 'parent_id' => $laporanParent->id],
                ['title' => 'Laporan Pengeluaran', 'icon' => 'trending_down', 'order' => 4, 'is_active' => true, 'location' => 'sidebar']
            );
            $lapTahunan = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.laporan.tahunan', 'parent_id' => $laporanParent->id],
                ['title' => 'Laporan Tahunan', 'icon' => 'folder_special', 'order' => 5, 'is_active' => true, 'location' => 'sidebar']
            );

            foreach ($roles as $role) {
                MenuRole::firstOrCreate(['menu_id' => $lapHarian->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $lapBulanan->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $lapTunggakan->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $lapPengeluaran->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $lapTahunan->id, 'role' => $role]);
            }

        // 6. Master Data Keuangan
        $masterParent = DynamicMenu::firstOrCreate(
            ['title' => 'Master Keuangan', 'icon' => 'settings'],
            ['order' => 26, 'is_active' => true, 'location' => 'sidebar', 'url' => '#']
        );
        foreach ($roles as $role) {
            MenuRole::firstOrCreate(['menu_id' => $masterParent->id, 'role' => $role]);
        }
            // Children
            $posBayar = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.biaya-lain.index', 'parent_id' => $masterParent->id],
                ['title' => 'Jenis Biaya / Pos', 'icon' => 'category', 'order' => 1, 'is_active' => true, 'location' => 'sidebar']
            );
            $katKeluar = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.kategori-pengeluaran.index', 'parent_id' => $masterParent->id],
                ['title' => 'Kategori Pengeluaran', 'icon' => 'label', 'order' => 2, 'is_active' => true, 'location' => 'sidebar']
            );
             $katMasuk = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.kategori-pemasukan.index', 'parent_id' => $masterParent->id],
                ['title' => 'Kategori Pemasukan', 'icon' => 'label', 'order' => 3, 'is_active' => true, 'location' => 'sidebar']
            );
            $keringanan = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.keringanan.index', 'parent_id' => $masterParent->id],
                ['title' => 'Manajemen Keringanan', 'icon' => 'percent', 'order' => 4, 'is_active' => true, 'location' => 'sidebar']
            );
             $dataKelas = DynamicMenu::firstOrCreate(
                ['route' => 'keuangan.kelas.index', 'parent_id' => $masterParent->id],
                ['title' => 'Atur Tarif per Kelas', 'icon' => 'class', 'order' => 5, 'is_active' => true, 'location' => 'sidebar']
            );

            foreach ($roles as $role) {
                MenuRole::firstOrCreate(['menu_id' => $posBayar->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $katKeluar->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $katMasuk->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $keringanan->id, 'role' => $role]);
                MenuRole::firstOrCreate(['menu_id' => $dataKelas->id, 'role' => $role]);
            }

        $this->command->info('Keuangan Menu seeded successfully for roles: ' . implode(', ', $roles));
    }
}
