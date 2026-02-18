<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}

