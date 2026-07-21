<?php
namespace App\Http\Controllers;
use App\Models\Guru;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
class GuruTugasController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'guru') {
            return redirect()->back()->with('error', 'Hanya guru yang bisa membuat tugas');
        }
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'mapel_id' => 'required|exists:mapel,id',
            'kelas_id' => 'required|exists:kelas,id',
            'tipe' => 'required|in:tugas,ulangan',
            'tanggal_deadline' => 'required|date',
            'deskripsi' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png|max:10240',
        ]);
        $guru = $this->currentGuru($request);
        if (!$this->guruTeaches($guru, $data['kelas_id'], $data['mapel_id'])) {
            return redirect()->back()->with('error', 'Kelas atau mapel tidak sesuai dengan jadwal mengajar Anda.');
        }
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('tugas/' . $guru->id, 'public');
        }
        Tugas::create([
            'judul' => $data['judul'],
            'mapel_id' => $data['mapel_id'],
            'kelas_id' => $data['kelas_id'],
            'guru_id' => $guru->id,
            'tipe' => $data['tipe'],
            'tanggal_deadline' => $data['tanggal_deadline'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'file_path' => $filePath,
        ]);
        return redirect()->back()->with('success', 'Tugas/ulangan berhasil dibuat!');
    }
    public function download(Request $request, Tugas $tugas)
    {
        $guru = $this->currentGuru($request);
        if ($tugas->guru_id !== $guru->id) {
            abort(403);
        }
        $path = storage_path('app/public/' . $tugas->file_path);
        if (!file_exists($path)) {
            return abort(404, 'File tidak ditemukan.');
        }
        return response()->download($path);
    }
    public function downloadPengumpulan(Request $request, PengumpulanTugas $pengumpulan)
    {
        $guru = $this->currentGuru($request);
        if ($pengumpulan->tugas?->guru_id !== $guru->id) {
            abort(403);
        }
        $path = storage_path('app/public/' . $pengumpulan->file_path);
        if (!file_exists($path)) {
            return abort(404, 'File tidak ditemukan.');
        }
        return response()->download($path);
    }
}
