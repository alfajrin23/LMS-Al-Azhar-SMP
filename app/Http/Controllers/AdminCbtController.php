<?php
namespace App\Http\Controllers;
use App\Models\CbtExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AdminCbtController extends Controller
{
    public function index()
    {
        $exams = CbtExam::whereIn('status', ['pending', 'approved', 'rejected'])
            ->with('mapel', 'kelas', 'guru.user')
            ->latest()
            ->get();
        return view('dashboard.admin-sections.cbt-approval', compact('exams'));
    }
    public function approve(CbtExam $cbtExam)
    {
        $cbtExam->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_reject' => null,
        ]);
        return redirect()->back()->with('success', 'Ujian disetujui!');
    }
    public function reject(Request $request, CbtExam $cbtExam)
    {
        $data = $request->validate(['catatan_reject' => 'required|string|max:1000']);
        $cbtExam->update([
            'status' => 'rejected',
            'catatan_reject' => $data['catatan_reject'],
        ]);
        return redirect()->back()->with('success', 'Ujian ditolak');
    }
}
