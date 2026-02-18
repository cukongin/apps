<?php

namespace App\Keuangan\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengeluaran_id',
        'nama_barang',
        'jumlah',
        'satuan',
        'harga_satuan',
        'subtotal'
    ];

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class);
    }
}

