<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

class LaporanHarianController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        return view('keuangan.laporan.harian');
    }
}

