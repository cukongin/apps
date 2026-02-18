<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $fillable = [
        'sumber',
        'jumlah',
        'keterangan',
        'tanggal_pemasukan',
        'kategori',
        'user_id'
    ];

    protected $casts = [
        'tanggal_pemasukan' => 'date',
        'jumlah' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

