<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $guarded = [];

    public function siswa()
    {
        return $this->belongsTo(\App\Models\Siswa::class);
    }

    public function jenisBiaya()
    {
        return $this->belongsTo(JenisBiaya::class);
    }

    public function transaksis()
    {
        return $this->hasMany(\App\Keuangan\Models\Transaksi::class);
    }
}

