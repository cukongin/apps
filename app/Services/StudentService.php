<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\TahunAjaran;

class StudentService
{
    protected $academicService;

    public function __construct(AcademicService $academicService)
    {
        $this->academicService = $academicService;
    }

    public function getActiveStudentCounts($yearId = null)
    {
        if (!$yearId) {
            $year = $this->academicService->activeYear();
            $yearId = $year ? $year->id : null;
        }

        if (!$yearId) return ['total' => 0, 'mi' => 0, 'mts' => 0];

        $total = Siswa::where('status_siswa', 'aktif')->count();

        // MI
        $mi = Siswa::where('status_siswa', 'aktif')
            ->whereHas('anggota_kelas.kelas', function($q) use ($yearId) {
                $q->where('id_tahun_ajaran', $yearId)
                  ->whereHas('jenjang', fn($sq) => $sq->where('kode', 'MI'));
            })->count();

        // MTs
        $mts = Siswa::where('status_siswa', 'aktif')
            ->whereHas('anggota_kelas.kelas', function($q) use ($yearId) {
                $q->where('id_tahun_ajaran', $yearId)
                  ->whereHas('jenjang', fn($sq) => $sq->where('kode', 'MTs')->orWhere('kode', 'MTS'));
            })->count();

        return [
            'total' => $total,
            'mi' => $mi,
            'mts' => $mts
        ];
    }
}
