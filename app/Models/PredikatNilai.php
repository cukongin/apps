<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredikatNilai extends Model
{
    use HasFactory;

    protected $table = 'predikat_nilai';
    protected $guarded = ['id'];

    public function tahun_ajaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran');
    }

    public static function removeDuplicates()
    {
        $duplicates = \Illuminate\Support\Facades\DB::select("
            SELECT id_tahun_ajaran, jenjang, grade, COUNT(*) as count
            FROM predikat_nilai
            GROUP BY id_tahun_ajaran, jenjang, grade
            HAVING count > 1
        ");

        foreach ($duplicates as $dup) {
            if (!$dup->id_tahun_ajaran) continue;

            $records = self::where('id_tahun_ajaran', $dup->id_tahun_ajaran)
                ->where('jenjang', $dup->jenjang)
                ->where('grade', $dup->grade)
                ->orderBy('id', 'asc')
                ->get();

            $keep = $records->pop(); // Keep the last one

            foreach ($records as $r) {
                $r->delete();
            }
        }
    }
}
