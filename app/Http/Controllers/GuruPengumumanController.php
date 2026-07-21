<?php
namespace App\Http\Controllers;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
class GuruPengumumanController extends Controller
{
    public function store(Request $request)
    {
        if (!str_starts_with($request->user()->role, 'guru')) {
            return redirect()->back()->with('error', 'Akses ditolak');
        }
        $data = $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string|max:5000',
            'target_role' => 'nullable|string|max:50',
        ]);
        Pengumuman::create([
            'judul' => $data['judul'],
            'konten' => $data['konten'],
            'target_role' => $data['target_role'] ?? null,
            'created_by' => $request->user()->id,
        ]);
        return redirect()->back()->with('success', 'Pengumuman berhasil dibuat!');
    }
}
