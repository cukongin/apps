<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'deskripsi',
        'jumlah',
        'tanggal_pengeluaran',
        'kategori',
        'bukti_foto',
        'user_id'
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'jumlah' => 'decimal:2'
    ];

    public function details()
    {
        return $this->hasMany(PengeluaranDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

