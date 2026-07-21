<?php
namespace App\Http\Controllers;
use App\Models\Guru;
use App\Models\LaporanMengajar;
use Illuminate\Http\Request;
class GuruLaporanController extends Controller
{
    public function store(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Hanya guru yang bisa input laporan mengajar');
        }
        $guru = Guru::query()->where('user_id', $request->user()->id)->firstOrFail();
        $data = $request->validate([
            'tipe' => 'required|in:harian,mingguan,bulanan',
            'tanggal' => 'required|date',
            'isi' => 'required',
        ]);
        $isiData = $data['isi'];
        if (is_string($isiData)) {
            $decoded = json_decode($isiData, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $isiData = $decoded;
            } else {
                if ($data['tipe'] === 'harian') {
                    $isiData = [
                        'checklists' => [],
                        'kendala' => [
                            ['bidang' => 'Akademik', 'deskripsi' => $data['isi'], 'dampak' => '-', 'solusi' => '-', 'tindak_lanjut' => '-']
                        ],
                        'pemetaan_masalah_siswa' => [],
                        'refleksi' => [
                            'materi_tersampaikan' => '-',
                            'target_tercapai' => '-',
                            'kendala_terbesar' => $data['isi'],
                            'strategi_perbaikan' => '-',
                            'rencana_pertemuan' => '-'
                        ]
                    ];
                } elseif ($data['tipe'] === 'mingguan') {
                    $isiData = [
                        'rekap_pembelajaran' => [
                            ['hari' => '-', 'materi' => $data['isi'], 'kehadiran' => '-', 'ketuntasan' => '-', 'hots' => '-', 'catatan' => '-']
                        ],
                        'evaluasi_akademik' => [],
                        'analisis_kendala' => [],
                        'pemetaan_siswa' => [],
                        'tindak_lanjut' => []
                    ];
                } else {
                    $isiData = [
                        'capaian_belajar_bulanan' => [
                            ['elemen_cp' => 'Umum', 'target' => '-', 'capaian' => '-', 'persentase' => '-', 'keterangan' => $data['isi']]
                        ],
                        'evaluasi_dan_kendala' => [],
                        'analisis_siswa' => [],
                        'pemetaan_masalah_jangka_pendek' => [],
                        'pemetaan_masalah_jangka_menengah' => [],
                        'monitoring_kinerja_guru' => [],
                        'rekomendasi_supervisor' => []
                    ];
                }
            }
        }
        LaporanMengajar::updateOrCreate(
            [
                'guru_id' => $guru->id,
                'tipe' => $data['tipe'],
                'tanggal' => $data['tanggal'],
            ],
            [
                'isi' => $isiData,
            ]
        );
        return redirect()->back()->with('success', 'Laporan mengajar berhasil dikirim!');
    }
}
