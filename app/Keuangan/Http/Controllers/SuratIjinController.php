<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

class SuratIjinController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        return view('keuangan.surat_ijin.index');
    }
}

