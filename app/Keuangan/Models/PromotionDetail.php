<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionDetail extends Model
{
    protected $guarded = [];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function oldKelas()
    {
        return $this->belongsTo(Kelas::class, 'old_kelas_id');
    }

    public function newKelas()
    {
        return $this->belongsTo(Kelas::class, 'new_kelas_id');
    }
}

