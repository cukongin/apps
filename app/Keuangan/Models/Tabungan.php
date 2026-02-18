<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;

class Tabungan extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}

