<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $guarded = ['id'];

    public function jenjang()
    {
        return $this->belongsTo(Jenjang::class, 'id_jenjang');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kelas_saat_ini()
    {
        // Get Active Year ID (Cached if possible, but safe here)
        $activeYearId = \App\Models\TahunAjaran::where('status', 'aktif')->value('id');

        return $this->hasOne(AnggotaKelas::class, 'id_siswa')
            ->where('status', 'aktif')
            ->whereHas('kelas', function($q) use ($activeYearId) {
                $q->where('id_tahun_ajaran', $activeYearId);
            })
            ->latest();
    }

    public function anggota_kelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'id_siswa');
    }

    public function riwayat_kelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'id_siswa')->orderBy('id', 'desc');
    }

    public function riwayat_absensi()
    {
        return $this->hasMany(RiwayatAbsensi::class, 'id_siswa');
    }

    public function catatan_wali_kelas()
    {
        return $this->hasMany(CatatanWaliKelas::class, 'id_siswa');
    }

    public function nilai_ekskul()
    {
        return $this->hasMany(NilaiEkstrakurikuler::class, 'id_siswa');
    }
    public function nilai_siswa()
    {
        return $this->hasMany(NilaiSiswa::class, 'id_siswa');
    }

    public function getNamaAttribute()
    {
        return $this->nama_lengkap;
    }

    /**
     * Alias 'status' to 'status_siswa' for Finance compatibility.
     */
    public function getStatusAttribute($value)
    {
        return $this->attributes['status_siswa'] ?? $value;
    }

    /**
     * Alias 'nis' to 'nis_lokal' for Finance compatibility.
     */
    public function getNisAttribute($value)
    {
        return $this->attributes['nis_lokal'] ?? $value;
    }

    // ==========================================
    // Keuangan Compatibility & Relationships
    // ==========================================
    public function tagihans()
    {
        return $this->hasMany(\App\Keuangan\Models\Tagihan::class, 'siswa_id');
    }
    public function transaksis()
    {
        return $this->hasManyThrough(
            \App\Keuangan\Models\Transaksi::class,
            \App\Keuangan\Models\Tagihan::class,
            'siswa_id', // Foreign key on tagihans table...
            'tagihan_id', // Foreign key on transaksis table...
            'id', // Local key on siswas table...
            'id' // Local key on tagihans table...
        );
    }
    public function tabungans()
    {
        return $this->hasMany(\App\Keuangan\Models\Tabungan::class, 'siswa_id');
    }

    public function kategoriKeringanan()
    {
        return $this->belongsTo(\App\Keuangan\Models\KategoriKeringanan::class, 'kategori_keringanan_id');
    }

    /**
     * Relationship to Kelas via kelas_id.
     * Kept for backward compatibility if code uses query builder on 'kelas_id'.
     */
    public function kelas()
    {
        // Legacy Finance Direct Column
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Accessor 'kelas_awal' to get class via 'kelas_id' or 'kelas_saat_ini'.
     * Since 'kelas' is a relationship method name, we CANNOT name the accessor 'getKelasAttribute'
     * without overriding the relationship method call itself in some contexts or causing recursion if not careful.
     *
     * However, Laravel allows getFooAttribute for $model->foo.
     * If public function foo() exists, $model->foo returns the relationship results.
     *
     * If we want $model->kelas to return the Academic Class object even if kelas_id is null:
     * We should probably use a custom attribute like 'kelas_biaya' or ensure 'kelas_id' is filled.
     *
     * BUT: The user wants "SINKRON".
     * Valid Solution: Sync logic should fill 'kelas_id'.
     * Fallback Solution: Helper to get class.
     *
     * Let's stick to the ACCESSOR override but implemented carefully.
     * Actually, if we define getKelasAttribute, it intercepts $model->kelas.
     *
     */
    public function getKelasAttribute()
    {
        // 1. If 'kelas' relation is already loaded and not null, return it.
        if ($this->relationLoaded('kelas')) {
             $val = $this->getRelationValue('kelas');
             if ($val) return $val;
        }

        // 2. If 'kelas_id' is present, let standard relation resolve it (if not loaded).
        // BUT we are in an accessor for 'kelas'. Calling $this->kelas here loops.
        // We can only check attributes.
        if (!empty($this->attributes['kelas_id'])) {
             // If we really want to return the relation result:
             // We can use the query builder if relation not loaded?
             // Or rely on the fact that if we are here, $this->kelas was accessed.

             // Ideally we shouldn't override 'kelas' attribute if we want standard behavior.
             // But valid use case: $siswa->kelas returns null because kelas_id is null.
             // We want it to fallback to 'kelas_saat_ini'.
        }

        // 3. Try Academic 'kelas_saat_ini'
        if ($this->relationLoaded('kelas_saat_ini')) {
            return $this->getRelationValue('kelas_saat_ini')->kelas ?? null;
        }

        // 4. Fallback: Lazy load 'kelas_saat_ini'
        // Only if kelas_id is NULL. If kelas_id is NOT null, we should have returned in step 1 or 2?
        // Actually, if kelas_id is set, $this->kelas (relation) would return a model.
        // The accessor INTERCEPTS $this->kelas.

        $academicClass = $this->kelas_saat_ini->kelas ?? null;

        if ($academicClass) return $academicClass;

        // Final fallback: If finance ID exists but relation check failed (orphaned?)
        if (!empty($this->attributes['kelas_id'])) {
             return Kelas::find($this->attributes['kelas_id']);
        }

        return null;
    }
}
