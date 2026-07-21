<?php
namespace App\Http\Controllers;
use App\Models\Pesan;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
class OrtuPesanController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->role !== 'orang_tua') {
            return redirect()->back()->with('error', 'Akses ditolak');
        }
        $data = $request->validate([
            'penerima_id' => 'required|exists:users,id',
            'isi' => 'required|string|max:2000',
        ]);
        $ortu = $this->currentOrangTua($request);
        $guru = Guru::where('user_id', $data['penerima_id'])->first();
        $kelasIds = $ortu->siswa()->whereNotNull('kelas_id')->pluck('siswa.kelas_id');
        $canMessage = $guru && \App\Models\Jadwal::where('guru_id', $guru->id)
            ->whereIn('kelas_id', $kelasIds)
            ->exists();
        if (!$canMessage) {
            return redirect()->back()->with('error', 'Tujuan pesan tidak terkait dengan kelas anak Anda.');
        }
        Pesan::create([
            'pengirim_id' => $request->user()->id,
            'penerima_id' => $data['penerima_id'],
            'isi' => $data['isi'],
        ]);
        return redirect()->back()->with('success', 'Pesan berhasil dikirim!');
    }
}
