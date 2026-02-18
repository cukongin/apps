<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $guarded = ['id'];

    public function wali_kelas()
    {
        return $this->belongsTo(User::class, 'id_wali_kelas');
    }

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang');
    }

    public function tahun_ajaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function anggota_kelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'id_kelas');
    }

    public function pengajar_mapel()
    {
        return $this->hasMany(PengajarMapel::class, 'id_kelas');
    }

    public function mapel_diajar()
    {
        return $this->pengajar_mapel();
    }

    // ==========================================
    // Keuangan Compatibility
    // ==========================================
    public function getNamaAttribute()
    {
        return $this->nama_kelas;
    }

    public function getLevelIdAttribute()
    {
        return $this->tingkat_kelas;
    }

    public function level()
    {
        return $this->jenjang();
    }
}
