<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisBiaya extends Model
{
    protected $guarded = [];

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }
}

