<?php
namespace App\Http\Controllers;
use App\Models\CatatanWali;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
class GuruCatatanController extends Controller
{
    public function store(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Hanya guru yang bisa input catatan');
        }
        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'catatan' => 'required|string|max:2000',
            'semester' => 'required|string|max:50',
        ]);
        $guru = $this->currentGuru($request);
        $siswa = Siswa::findOrFail($data['siswa_id']);
        if (!$this->guruCanAccessSiswa($guru, $siswa)) {
            return redirect()->back()->with('error', 'Siswa tidak berada pada kelas yang Anda ampu.');
        }
        CatatanWali::updateOrCreate(
            ['siswa_id' => $data['siswa_id'], 'semester' => $data['semester']],
            ['catatan' => $data['catatan'], 'created_by' => $guru->id]
        );
        return redirect()->back()->with('success', 'Catatan wali kelas berhasil disimpan!');
    }
}
