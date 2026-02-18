<?php

namespace App\Keuangan\Http\Controllers;

use Illuminate\Http\Request;

class SppController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        return view('keuangan.spp.payment');
    }

    public function receipt()
    {
        return view('keuangan.spp.receipt');
    }
}

