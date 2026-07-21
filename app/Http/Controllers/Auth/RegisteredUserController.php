<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $kelases = Kelas::all();
        return view('auth.register', compact('kelases'));
    }
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'kelas_id' => ['required', 'exists:kelas,id'],
        ]);
        $kelas = Kelas::find($request->kelas_id);
        $namaKelas = $kelas->nama_kelas;
        $isSmp = str_starts_with($namaKelas, '7') || str_starts_with($namaKelas, '8') || str_starts_with($namaKelas, '9');
        $role = $isSmp ? 'siswa_smp' : 'siswa_sd';
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);
        Siswa::create([
            'user_id' => $user->id,
            'kelas_id' => $request->kelas_id,
            'nama' => $request->name,
            'nis' => date('Y') . mt_rand(1000, 9999),
            'jenis_kelamin' => 'L',
            'status' => 'aktif',
        ]);
        event(new Registered($user));
        Auth::login($user);
        return redirect(route('dashboard', absolute: false));
    }
}
