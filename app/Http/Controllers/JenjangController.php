<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jenjang;
use App\Models\GlobalSetting;
use App\Models\TahunAjaran;
use App\Models\BobotPenilaian;
use App\Models\PredikatNilai;
use App\Models\KkmMapel;
use App\Models\Mapel;
use Illuminate\Support\Facades\DB;

class JenjangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenjangs = Jenjang::orderBy('id')->get();
        return view('settings.jenjang.index', compact('jenjangs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:jenjang,kode',
            'nama' => 'required|string|max:50',
            'has_rapor' => 'boolean'
        ]);

        Jenjang::create([
            'kode' => strtoupper($request->kode),
            'nama' => $request->nama,
            'has_rapor' => $request->has('has_rapor') ? 1 : 0
        ]);

        return back()->with('success', 'Jenjang baru berhasil ditambahkan.');
    }

    /**
     * Show the settings for a specific Jenjang.
     */
    public function settings($id)
    {
        $jenjang = Jenjang::findOrFail($id);
        $activeYear = TahunAjaran::where('status', 'aktif')->first();

        // Lock check (Copied from SettingsController)
        $latestYear = TahunAjaran::orderBy('id', 'desc')->first();
        $isLocked = false;
        if ($activeYear && $latestYear && $activeYear->id !== $latestYear->id) {
             if (!GlobalSetting::val('allow_edit_past_data', 0)) {
                 $isLocked = true;
             }
        }

        // 1. Data for Settings
        if (!$activeYear) {
            return back()->with('error', 'Tidak ada Tahun Ajaran aktif.');
        }

        $jenjangKode = $jenjang->kode;

        // Weights
        $bobot = BobotPenilaian::firstOrCreate(
            ['id_tahun_ajaran' => $activeYear->id, 'jenjang' => $jenjangKode],
            ['bobot_harian' => 0, 'bobot_uts_cawu' => 0, 'bobot_uas' => 0]
        );

        // Predicates
        $predicates = PredikatNilai::where('id_tahun_ajaran', $activeYear->id)
            ->where('jenjang', $jenjangKode)
            ->orderBy('grade')
            ->get();

        if ($predicates->isEmpty()) {
            // Defaults
            $defaults = [
                ['grade' => 'A', 'min' => 90, 'max' => 100, 'desk' => 'Sangat Baik'],
                ['grade' => 'B', 'min' => 80, 'max' => 89, 'desk' => 'Baik'],
                ['grade' => 'C', 'min' => 70, 'max' => 79, 'desk' => 'Cukup'],
                ['grade' => 'D', 'min' => 0,  'max' => 69,  'desk' => 'Kurang'],
            ];
            foreach ($defaults as $d) {
                // Determine description based on jenjang if needed
                $predicates[] = (object) [
                    'grade' => $d['grade'],
                    'min_score' => $d['min'],
                    'max_score' => $d['max'],
                    'deskripsi' => $d['desk']
                ];
            }
        }

        // KKM
        $mapels = Mapel::where(function($q) use ($jenjangKode) {
                $q->whereNull('target_jenjang')
                  ->orWhere('target_jenjang', 'SEMUA')
                  ->orWhere('target_jenjang', $jenjangKode);
            })
            ->orderBy('nama_mapel')
            ->get();
        $kkms = [];
        $rawKkms = KkmMapel::where('id_tahun_ajaran', $activeYear->id)
            ->where('jenjang_target', $jenjangKode)
            ->get()
            ->keyBy('id_mapel');

        // Global Settings specific to this Jenjang
        $keys = [
            'kkm_default_' . strtolower($jenjangKode), // Specific/Fallback
            'kkm_default', // Fallback
            // Academic
            'total_effective_days', // Usually global, but let's check if we want per-jenjang override? No, likely shared.
            // But user asked for per-jenjang. Let's try to look for specific first.
            'total_effective_days_' . strtolower($jenjangKode),

            // Promotion
            'promotion_max_kkm_failure_' . strtolower($jenjangKode),
            'promotion_min_attendance_' . strtolower($jenjangKode),
            'promotion_min_attitude_' . strtolower($jenjangKode),
            'promotion_requires_all_periods_' . strtolower($jenjangKode),

            // Rapor Dates
            'titimangsa_' . strtolower($jenjangKode),
            'titimangsa_hijriyah_' . strtolower($jenjangKode),
            'titimangsa_tempat_' . strtolower($jenjangKode),
            'titimangsa_2_' . strtolower($jenjangKode),

            // Transkrip Dates
            'titimangsa_transkrip_' . strtolower($jenjangKode),
            'titimangsa_transkrip_hijriyah_' . strtolower($jenjangKode),
            'titimangsa_transkrip_tempat_' . strtolower($jenjangKode),
            'titimangsa_transkrip_2_' . strtolower($jenjangKode),

            // Graduation
            'final_grade_' . strtolower($jenjangKode),
            'ijazah_range_' . strtolower($jenjangKode),

            // DKN / Ijazah
            'ijazah_min_lulus_' . strtolower($jenjangKode),
            'ijazah_bobot_rapor_' . strtolower($jenjangKode),
            'ijazah_bobot_ujian_' . strtolower($jenjangKode),
            'ijazah_period_label_' . strtolower($jenjangKode),
            'ijazah_period_count_' . strtolower($jenjangKode),
            'hm_name_' . strtolower($jenjangKode),
            'hm_name_' . strtolower($jenjangKode),
            'hm_nip_' . strtolower($jenjangKode),
            'ijazah_mapels_' . strtolower($jenjangKode),
        ];

        // Fetch values
        $settings = [];
        foreach($keys as $key) {
            $settings[$key] = GlobalSetting::val($key);
        }

        // Fallbacks (If specific key not found, use global defaults from SettingsController logic if applicable)
        // Actually, for new architecture, we should prefer explicit specific keys.
        // If not found, show empty or default hardcoded.

        // 5. Identities (School & Headmaster)
        $identity = \App\Models\IdentitasSekolah::firstOrCreate(
            ['jenjang' => $jenjangKode],
            ['nama_sekolah' => 'Madrasah ' . $jenjang->nama]
        );

        return view('settings.jenjang.settings', compact(
            'jenjang', 'activeYear', 'isLocked',
            'bobot', 'predicates', 'mapels', 'rawKkms', 'settings', 'identity'
        ));
    }

    /**
     * Update the settings for a specific Jenjang.
     */
    public function updateSettings(Request $request, $id)
    {
        $jenjang = Jenjang::findOrFail($id);
        $activeYear = TahunAjaran::where('status', 'aktif')->firstOrFail();
        $jk = $jenjang->kode;
        $jkl = strtolower($jk);

        // 1. Update Bobot
        BobotPenilaian::updateOrCreate(
            ['id_tahun_ajaran' => $activeYear->id, 'jenjang' => $jk],
            [
                'bobot_harian' => $request->bobot_harian ?? 0,
                'bobot_uts_cawu' => $request->bobot_uts_cawu ?? 0,
                'bobot_uas' => $request->bobot_uas ?? 0
            ]
        );

        // 2. Update Predicates
        if ($request->has('predikat') && is_array($request->predikat)) {
            PredikatNilai::where('id_tahun_ajaran', $activeYear->id)
                ->where('jenjang', $jk)
                ->delete();

            foreach ($request->predikat as $grade => $data) {
                PredikatNilai::create([
                    'id_tahun_ajaran' => $activeYear->id,
                    'jenjang' => $jk,
                    'grade' => $grade,
                    'min_score' => $data['min'] ?? 0,
                    'max_score' => $data['max'] ?? 0,
                    'deskripsi' => $data['deskripsi'] ?? ''
                ]);
            }
        }

        // 3. Update KKM
        if ($request->has('kkm') && is_array($request->kkm)) {
            foreach ($request->kkm as $mapelId => $nilai) {
                if ($nilai !== null) {
                    KkmMapel::updateOrCreate(
                        [
                            'id_tahun_ajaran' => $activeYear->id,
                            'id_mapel' => $mapelId,
                            'jenjang_target' => $jk
                        ],
                        ['nilai_kkm' => $nilai]
                    );
                }
            }
        }

        // 4. Update Global Settings (Specific Keys)
        $inputs = [
            'kkm_default_' . $jkl => $request->kkm_default,

            // Academic
            'total_effective_days_' . $jkl => $request->total_effective_days,

            // Promotion
            'promotion_max_kkm_failure_' . $jkl => $request->promotion_max_kkm_failure,
            'promotion_min_attendance_' . $jkl => $request->promotion_min_attendance,
            'promotion_min_attitude_' . $jkl => $request->promotion_min_attitude,
            'promotion_requires_all_periods_' . $jkl => $request->has('promotion_requires_all_periods') ? 1 : 0,

            // Rapor Dates
            'titimangsa_' . $jkl => $request->titimangsa,
            'titimangsa_hijriyah_' . $jkl => $request->titimangsa_hijriyah,
            'titimangsa_tempat_' . $jkl => $request->titimangsa_tempat,
            'titimangsa_2_' . $jkl => $request->titimangsa_2,

            // Transkrip Dates
            'titimangsa_transkrip_' . $jkl => $request->titimangsa_transkrip,
            'titimangsa_transkrip_hijriyah_' . $jkl => $request->titimangsa_transkrip_hijriyah,
            'titimangsa_transkrip_tempat_' . $jkl => $request->titimangsa_transkrip_tempat,
            'titimangsa_transkrip_2_' . $jkl => $request->titimangsa_transkrip_2,

            // Graduation
            'final_grade_' . $jkl => $request->final_grade,
            'ijazah_range_' . $jkl => $request->ijazah_range,

            // DKN / Ijazah
            'ijazah_min_lulus_' . $jkl => $request->ijazah_min_lulus,
            'ijazah_bobot_rapor_' . $jkl => $request->ijazah_bobot_rapor,
            'ijazah_bobot_ujian_' . $jkl => $request->ijazah_bobot_ujian,

            // Period Config
            'ijazah_period_label_' . $jkl => $request->ijazah_period_label,
            'ijazah_period_count_' . $jkl => $request->ijazah_period_count,
            'ijazah_mapels_' . $jkl => $request->has('ijazah_mapels') ? implode(',', $request->ijazah_mapels) : '',
        ];

        foreach ($inputs as $key => $val) {
            GlobalSetting::updateOrCreate(['key' => $key], ['value' => $val]);
        }

        // 5. Update Identitas Sekolah (Headmaster, etc)
        $identity = \App\Models\IdentitasSekolah::updateOrCreate(
            ['jenjang' => $jk],
            [
                'nama_sekolah' => $request->nama_sekolah,
                'npsn' => $request->npsn,
                'nsm' => $request->nsm,
                'alamat' => $request->alamat,
                'kepala_madrasah' => $request->kepala_madrasah,
                'nip_kepala' => $request->nip_kepala,
            ]
        );

        // Handle Logo Upload if present (Optional)
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('uploads', 'public');
            $identity->update(['logo' => $path]);
        }

        return back()->with('success', 'Pengaturan Jenjang ' . $jk . ' berhasil disimpan!');
    }

    public function destroy($id)
    {
        $jenjang = Jenjang::findOrFail($id);
        // Check if used by Classes
        if (\App\Models\Kelas::where('id_jenjang', $id)->exists()) {
            return back()->with('error', 'Gagal hapus: Jenjang ini masih digunakan oleh data Kelas.');
        }

        $jenjang->delete();
        return back()->with('success', 'Jenjang berhasil dihapus.');
    }
}
