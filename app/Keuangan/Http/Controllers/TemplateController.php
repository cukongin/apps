<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

class TemplateController extends \App\Http\Controllers\Controller
{
    public function siswa()
    {
        return response()->streamDownload(function () {
            echo "NIS,Nama Lengkap,Level,Kelas,Jenis Kelamin (L/P),Nama Wali,No HP\n";
            echo "1001,Ahmad Santoso,MDT Ula,1 Ula,L,Bapak Budi,628123456789\n";
            echo "1002,Siti Aminah,TPQ,Juz Amma A,P,Ibu Ani,628987654321";
        }, 'template-import-siswa.csv');
    }

    public function kelas()
    {
        return response()->streamDownload(function () {
            echo "Nama Kelas,Level,Wali Kelas\n";
            echo "1 Ula,MDT Ula,Ust. Zainal\n";
            echo "Juz Amma A,TPQ,Ust. Ahmad";
        }, 'template-import-kelas.csv');
    }
}

