<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
}

