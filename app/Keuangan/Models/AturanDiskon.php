<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AturanDiskon extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function kategoriKeringanan()
    {
        return $this->belongsTo(KategoriKeringanan::class);
    }

    public function jenisBiaya()
    {
        return $this->belongsTo(JenisBiaya::class);
    }
}

