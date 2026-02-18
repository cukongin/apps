<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\DynamicMenu;

class SidebarMenuSeeder extends Seeder
{
    public function run()
    {
        // 1. Truncate Tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('dynamic_menus')->truncate();
        DB::table('menu_roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // --- AKADEMIK SECTION ---
        $this->createMenu(null, 'AKADEMIK', null, null, null, 1, 'label', ['admin', 'teacher', 'student', 'walikelas', 'staff_tu']);

        $this->createMenu(null, 'Dashboard', 'home', 'dashboard', null, 2, 'item', ['admin', 'teacher', 'student', 'walikelas', 'staff_tu']);

        $this->createMenu(null, 'Data Siswa', 'person_search', 'master.students.index', null, 3, 'item', ['admin', 'staff_tu']);
        $this->createMenu(null, 'Data Guru', 'supervisor_account', 'master.teachers.index', null, 4, 'item', ['admin', 'staff_tu']);
        $this->createMenu(null, 'Data Kelas', 'meeting_room', 'classes.index', null, 5, 'item', ['admin', 'staff_tu']);
        $this->createMenu(null, 'Mata Pelajaran', 'menu_book', 'master.mapel.index', null, 6, 'item', ['admin', 'staff_tu']);


        // --- RAPOR & KBM SECTION ---
        $this->createMenu(null, 'RAPOR & KBM', null, null, null, 10, 'label', ['admin', 'teacher', 'walikelas', 'staff_tu']);

        // Wali Kelas Group
        $wali = $this->createMenu(null, 'Manajemen Wali Kelas', 'supervisor_account', null, null, 11, 'item', ['walikelas', 'admin']);
            $this->createMenu($wali->id, 'Dashboard Wali', 'dashboard', 'walikelas.dashboard', null, 1, 'item', ['walikelas', 'admin']);
            $this->createMenu($wali->id, 'Absensi & Kepribadian', 'fact_check', 'walikelas.absensi', null, 2, 'item', ['walikelas', 'admin']);
            $this->createMenu($wali->id, 'Ekstrakurikuler', 'sports_soccer', 'ekskul.index', null, 3, 'item', ['walikelas', 'admin']);
            $this->createMenu($wali->id, 'Catatan Siswa', 'rate_review', 'walikelas.catatan.index', null, 4, 'item', ['walikelas', 'admin']);
            $this->createMenu($wali->id, 'Monitoring Nilai', 'analytics', 'walikelas.monitoring', null, 5, 'item', ['walikelas', 'admin']);
            $this->createMenu($wali->id, 'Leger Nilai', 'table_view', 'walikelas.leger', null, 6, 'item', ['walikelas', 'admin']);
            $this->createMenu($wali->id, 'Kenaikan Kelas', 'upgrade', 'walikelas.kenaikan.index', null, 7, 'item', ['walikelas', 'admin']);
            $this->createMenu($wali->id, 'Cetak Rapor', 'print', 'reports.index', null, 8, 'item', ['walikelas', 'admin']);

        // Ijazah (Separate checking usually handled in code, but we add menu here for structure)
        $this->createMenu(null, 'Nilai Ujian (Ijazah)', 'school', 'ijazah.index', null, 12, 'item', ['walikelas']); // Logic for final year filtered in controller/middleware or kept in view

        // Guru (Teacher) Group
        $guru = $this->createMenu(null, 'Menu Guru', 'school', null, null, 13, 'item', ['teacher']);
            $this->createMenu($guru->id, 'Dashboard Guru', 'dashboard', 'teacher.dashboard', null, 1, 'item', ['teacher']);


        // --- MODUL KEUANGAN SECTION ---
        $this->createMenu(null, 'MODUL KEUANGAN', null, null, null, 30, 'label', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'staf_administrasi', 'teller_tabungan', 'kepala', 'kepala_madrasah']);

        $this->createMenu(null, 'Dashboard Keuangan', 'dashboard', 'keuangan.dashboard', null, 31, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'staf_administrasi', 'teller_tabungan', 'kepala', 'kepala_madrasah']);

        $this->createMenu(null, 'Data Santri', 'groups', 'keuangan.santri.index', null, 32, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'staf_administrasi']);
        $this->createMenu(null, 'Pembayaran Siswa', 'payments', 'keuangan.pembayaran.index', null, 33, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan']);

        // Menu Keuangan Dropdown
        $keuangan = $this->createMenu(null, 'Menu Keuangan', 'account_balance_wallet', null, null, 34, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'teller_tabungan', 'kepala', 'kepala_madrasah']);
            $this->createMenu($keuangan->id, 'Tabungan', 'savings', 'keuangan.tabungan.index', null, 1, 'item', ['admin', 'admin_utama', 'teller_tabungan']);
            $this->createMenu($keuangan->id, 'Master Biaya', 'settings_account_box', 'keuangan.biaya-lain.index', null, 2, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan']);
            $this->createMenu($keuangan->id, 'Master Beasiswa', 'volunteer_activism', 'keuangan.keringanan.index', null, 3, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan']);
            $this->createMenu($keuangan->id, 'Pengeluaran Ops', 'outbound', 'keuangan.pengeluaran.index', null, 4, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'kepala', 'kepala_madrasah']);
            $this->createMenu($keuangan->id, 'Pemasukan Lain', 'input_circle', 'keuangan.pemasukan.index', null, 5, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'kepala', 'kepala_madrasah']);

        // Laporan Keuangan Dropdown
        $laporan = $this->createMenu(null, 'Laporan Keuangan', 'bar_chart', null, null, 35, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'kepala_madrasah']);
            $this->createMenu($laporan->id, 'Kas Umum', 'summarize', 'keuangan.laporan.index', null, 1, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'kepala_madrasah']);
            $this->createMenu($laporan->id, 'Laporan Pembayaran', 'school', 'keuangan.laporan.santri', null, 2, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'kepala_madrasah']);
            $this->createMenu($laporan->id, 'Laporan Pengeluaran', 'receipt_long', 'keuangan.laporan.pengeluaran', null, 3, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'kepala_madrasah']);
            $this->createMenu($laporan->id, 'Laporan Tunggakan', 'warning', 'keuangan.laporan.tunggakan', null, 4, 'item', ['admin', 'admin_utama', 'bendahara', 'staf_keuangan', 'kepala_madrasah']);


        // --- PENGATURAN SYSTEM SECTION ---
        $this->createMenu(null, 'PENGATURAN SYSTEM', null, null, null, 50, 'label', ['admin']);

        $this->createMenu(null, 'Pengaturan Umum', 'settings', 'settings.index', null, 51, 'item', ['admin']);
        $this->createMenu(null, 'Manajemen User', 'manage_accounts', 'settings.users.index', null, 52, 'item', ['admin']);
        $this->createMenu(null, 'Menu Manager', 'list', 'settings.menus.index', null, 53, 'item', ['admin']);
    }

    private function createMenu($parentId, $title, $icon, $route, $url, $order, $type, $roles)
    {
        $menu = DynamicMenu::create([
            'parent_id' => $parentId,
            'title' => $title,
            'icon' => $icon,
            'route' => $route,
            'url' => $url,
            'order' => $order,
            'type' => $type,
            'is_active' => 1,
            'location' => 'sidebar',
        ]);

        foreach ($roles as $role) {
            \App\Models\MenuRole::create([
                'menu_id' => $menu->id,
                'role' => $role
            ]);
        }

        return $menu;
    }
}
