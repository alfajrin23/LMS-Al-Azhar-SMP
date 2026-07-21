<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\NilaiKti;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class KtiGradingTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function test_guru_can_grade_kti_specifically_for_class_9_students()
    {
        $guruUser = User::where('role', 'guru')->first();
        $kelas9 = Kelas::where('nama_kelas', '9A')->first();
        $userSiswa9 = User::create([
            'name' => 'Rian Hidayat',
            'email' => 'rian.hidayat@alazharjayaindonesia.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa_smp'
        ]);
        $siswa9 = Siswa::create([
            'user_id' => $userSiswa9->id,
            'nama' => 'Rian Hidayat',
            'nis' => '2022001',
            'kelas_id' => $kelas9->id,
            'jenis_kelamin' => 'L',
            'status' => 'aktif'
        ]);
        $response = $this
            ->actingAs($guruUser)
            ->post('/guru/kti', [
                'siswa_id' => $siswa9->id,
                'judul_kti' => 'Pengaruh Gadget terhadap Konsentrasi Belajar',
                'nilai_proses' => 80.00,
                'nilai_tulisan' => 90.00,
                'nilai_sidang' => 85.00,
                'catatan' => 'Sangat bagus, pertahankan!',
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('nilai_ktis', [
            'siswa_id' => $siswa9->id,
            'judul_kti' => 'Pengaruh Gadget terhadap Konsentrasi Belajar',
            'nilai_proses' => 80.00,
            'nilai_tulisan' => 90.00,
            'nilai_sidang' => 85.00,
            'nilai_akhir' => 85.50,
            'catatan' => 'Sangat bagus, pertahankan!',
        ]);
        $responseSiswa9 = $this
            ->actingAs($userSiswa9)
            ->get('/dashboard');
        $responseSiswa9->assertOk();
        $responseSiswa9->assertViewHas('isKelas9', true);
        $responseSiswa9->assertViewHas('nilaiKti');
        $userSiswa7 = User::where('email', 'ahmad.rizky@alazharjayaindonesia.sch.id')->first();
        $responseSiswa7 = $this
            ->actingAs($userSiswa7)
            ->get('/dashboard');
        $responseSiswa7->assertOk();
        $responseSiswa7->assertViewHas('isKelas9', false);
        $responseSiswa7->assertViewHas('nilaiKti', null);
    }
    public function test_siswa_can_submit_kti_bimbingan_draft()
    {
        $kelas9 = Kelas::where('nama_kelas', '9A')->first();
        $userSiswa9 = User::create([
            'name' => 'Rian Hidayat 2',
            'email' => 'rian.hidayat2@alazharjayaindonesia.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa_smp'
        ]);
        $siswa9 = Siswa::create([
            'user_id' => $userSiswa9->id,
            'nama' => 'Rian Hidayat 2',
            'nis' => '2022002',
            'kelas_id' => $kelas9->id,
            'jenis_kelamin' => 'L',
            'status' => 'aktif'
        ]);
        $response = $this
            ->actingAs($userSiswa9)
            ->post('/siswa/kti/bimbingan', [
                'bab' => 'Bab 1',
                'file_draft' => 'https://docs.google.com/document/d/example-link',
                'catatan_siswa' => 'Mohon ditinjau Bab 1 saya Ustadz.',
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('kti_bimbingans', [
            'siswa_id' => $siswa9->id,
            'bab' => 'Bab 1',
            'file_draft' => 'https://docs.google.com/document/d/example-link',
            'status' => 'pending',
        ]);
    }
    public function test_guru_can_approve_kti_bimbingan_draft_and_advance_bab()
    {
        $guruUser = User::where('role', 'guru')->first();
        $kelas9 = Kelas::where('nama_kelas', '9A')->first();
        $userSiswa9 = User::create([
            'name' => 'Rian Hidayat 3',
            'email' => 'rian.hidayat3@alazharjayaindonesia.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'siswa_smp'
        ]);
        $siswa9 = Siswa::create([
            'user_id' => $userSiswa9->id,
            'nama' => 'Rian Hidayat 3',
            'nis' => '2022003',
            'kelas_id' => $kelas9->id,
            'jenis_kelamin' => 'L',
            'status' => 'aktif'
        ]);
        $kti = NilaiKti::create([
            'siswa_id' => $siswa9->id,
            'judul_kti' => 'Judul KTI Keren',
            'current_bab' => 'Bab 1',
            'nilai_proses' => 0,
            'nilai_tulisan' => 0,
            'nilai_sidang' => 0,
            'nilai_akhir' => 0,
        ]);
        $bimbingan = \App\Models\KtiBimbingan::create([
            'siswa_id' => $siswa9->id,
            'bab' => 'Bab 1',
            'file_draft' => 'https://docs.google.com/document/d/example-link-3',
            'status' => 'pending',
        ]);
        $response = $this
            ->actingAs($guruUser)
            ->post("/guru/kti/bimbingan/{$bimbingan->id}/proses", [
                'status' => 'disetujui',
                'catatan_guru' => 'Bab 1 ACC, silahkan lanjut ke Bab 2.',
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('kti_bimbingans', [
            'id' => $bimbingan->id,
            'status' => 'disetujui',
            'catatan_guru' => 'Bab 1 ACC, silahkan lanjut ke Bab 2.',
        ]);
        $this->assertDatabaseHas('nilai_ktis', [
            'siswa_id' => $siswa9->id,
            'current_bab' => 'Bab 2',
        ]);
    }
}
