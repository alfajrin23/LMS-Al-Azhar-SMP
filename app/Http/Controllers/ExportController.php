<?php
namespace App\Http\Controllers;
use App\Models\Guru;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
class ExportController extends Controller
{
    public function nilaiCsv(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }
        $guru = $this->currentGuru($request);
        $siswaIds = Siswa::whereIn('kelas_id', function ($q) use ($guru) {
            $q->select('kelas_id')->from('jadwal')->where('guru_id', $guru->id);
        })->pluck('id');
        $mapelIds = $guru->mapels()->pluck('mapel.id');
        $nilai = Nilai::whereIn('siswa_id', $siswaIds)
            ->whereIn('mapel_id', $mapelIds)
            ->with('siswa', 'siswa.kelas', 'mapel')
            ->get();
        $filename = 'nilai_' . ($guru->mapels()->first()?->kode ?? 'guru') . '_' . now()->format('Ymd') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        $callback = function () use ($nilai) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, ['No', 'NIS', 'Nama', 'Kelas', 'Mapel', 'Nilai']);
            foreach ($nilai as $i => $n) {
                fputcsv($handle, [
                    $i + 1,
                    $n->siswa->nis ?? '-',
                    $n->siswa->nama ?? '-',
                    $n->siswa->kelas->nama_kelas ?? '-',
                    $n->mapel->nama_mapel ?? '-',
                    $n->nilai,
                ]);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }
    public function kepalaSiswa(Request $request)
    {
        $this->abortUnlessKepalaSekolah($request);
        $rows = Siswa::with('user', 'kelas', 'kelasQuran', 'orangTua.user')
            ->orderBy('nama')
            ->get()
            ->map(fn ($siswa) => [
                'NIS' => $siswa->nis ?? '-',
                'Nama' => $siswa->nama ?? '-',
                'Role Akun' => $siswa->user->role ?? '-',
                'Email' => $siswa->user->email ?? '-',
                'Kelas Umum' => $siswa->kelas->nama_kelas ?? '-',
                'Kelas Quran' => $siswa->kelasQuran->nama_kelas ?? '-',
                'Jenis Kelamin' => $siswa->jenis_kelamin ?? '-',
                'Nama Ayah' => $siswa->nama_ayah ?? '-',
                'Nama Ibu' => $siswa->nama_ibu ?? '-',
                'Orang Tua Terhubung' => $siswa->orangTua->pluck('nama')->implode(', ') ?: '-',
                'Status' => $siswa->status ?? 'aktif',
            ]);
        return $this->excelTable('daftar_siswa_' . now()->format('Ymd') . '.xls', 'Daftar Siswa', $rows);
    }
    public function kepalaGuru(Request $request)
    {
        $this->abortUnlessKepalaSekolah($request);
        $rows = Guru::with('user', 'mapels', 'jadwal.kelas')
            ->orderBy('nama')
            ->get()
            ->map(fn ($guru) => [
                'NIP' => $guru->nip ?? '-',
                'Nama' => $guru->nama ?? '-',
                'Email' => $guru->user->email ?? '-',
                'Mapel' => $guru->mapels->pluck('nama_mapel')->unique()->implode(', ') ?: '-',
                'Kelas Diampu' => $guru->jadwal->pluck('kelas.nama_kelas')->filter()->unique()->implode(', ') ?: '-',
                'No. Telp' => $guru->no_telp ?? '-',
                'Alamat' => $guru->alamat ?? '-',
                'Status' => $guru->status ?? 'aktif',
            ]);
        return $this->excelTable('daftar_guru_' . now()->format('Ymd') . '.xls', 'Daftar Guru', $rows);
    }
    public function kepalaOrangTua(Request $request)
    {
        $this->abortUnlessKepalaSekolah($request);
        $rows = User::with('orangTua.siswa.kelas')
            ->where('role', 'orang_tua')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                $anak = $user->orangTua?->siswa ?? collect();
                return [
                    'Nama Akun' => $user->name ?? '-',
                    'Nama Orang Tua' => $user->orangTua->nama ?? '-',
                    'Email' => $user->email ?? '-',
                    'No. Telp' => $user->orangTua->no_telp ?? '-',
                    'Anak' => $anak->pluck('nama')->implode(', ') ?: '-',
                    'Kelas Anak' => $anak->pluck('kelas.nama_kelas')->filter()->unique()->implode(', ') ?: '-',
                    'Alamat' => $user->orangTua->alamat ?? '-',
                    'Status' => 'aktif',
                ];
            });
        return $this->excelTable('daftar_orang_tua_' . now()->format('Ymd') . '.xls', 'Daftar Orang Tua', $rows);
    }
    private function abortUnlessKepalaSekolah(Request $request): void
    {
        abort_unless($request->user()?->role === 'kepala_sekolah', 403);
    }
    private function excelTable(string $filename, string $title, $rows)
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        return response()->stream(function () use ($title, $rows) {
            echo "\xEF\xBB\xBF";
            echo '<html><head><meta charset="UTF-8"></head><body>';
            echo '<table border="1">';
            echo '<tr><th colspan="' . max(1, count($rows->first() ?? [])) . '">' . e($title) . '</th></tr>';
            if ($rows->isEmpty()) {
                echo '<tr><td>Tidak ada data</td></tr>';
            } else {
                echo '<tr>';
                foreach (array_keys($rows->first()) as $heading) {
                    echo '<th>' . e($heading) . '</th>';
                }
                echo '</tr>';
                foreach ($rows as $row) {
                    echo '<tr>';
                    foreach ($row as $value) {
                        echo '<td style="mso-number-format:\'\\@\';">' . e((string) $value) . '</td>';
                    }
                    echo '</tr>';
                }
            }
            echo '</table></body></html>';
        }, 200, $headers);
    }
}
