<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriKeringanan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function aturanDiskons()
    {
        return $this->hasMany(AturanDiskon::class);
    }
    
    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }
}

