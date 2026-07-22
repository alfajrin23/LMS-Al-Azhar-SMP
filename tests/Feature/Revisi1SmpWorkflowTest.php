<?php

namespace Tests\Feature;

use App\Models\AdministrasiGuruChecklist;
use App\Models\Guru;
use App\Models\JurnalSikap;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Materi;
use App\Models\MateriApprovalHistory;
use App\Models\Pesan;
use App\Models\ProgramPengayaan;
use App\Models\ProgramRemedial;
use App\Models\Rapor;
use App\Models\RaporItem;
use App\Models\Siswa;
use App\Models\TahfidzProgress;
use App\Models\TahfidzSetoran;
use App\Models\TahsinSetoran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Revisi1SmpWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_demo_seed_data_covers_smpit_revision_requirements(): void
    {
        $siswa = $this->demoSiswa();

        foreach ([
            'guru.demo@alazharjayaindonesia.sch.id' => 'guru',
            'siswa.demo@alazharjayaindonesia.sch.id' => 'siswa_smp',
            'ortu.demo@alazharjayaindonesia.sch.id' => 'orang_tua',
        ] as $email => $role) {
            $this->assertDatabaseHas('users', ['email' => $email, 'role' => $role]);
        }

        $this->assertDatabaseHas('guru', ['nama' => 'Guru Demo SMPIT']);
        $this->assertDatabaseMissing('guru', ['nama' => 'Nu Rahma, S.Pd']);
        $this->assertDatabaseMissing('guru', ['nama' => 'Mardiyani, S.Pd']);
        $julyAttendance = Kehadiran::where('siswa_id', $siswa->id)
            ->whereDate('tanggal', '>=', '2026-07-01')
            ->whereDate('tanggal', '<=', '2026-07-24');
        $this->assertSame(18, (clone $julyAttendance)->count());
        $this->assertSame(14, (clone $julyAttendance)->where('status', 'hadir')->count());
        $this->assertSame(2, (clone $julyAttendance)->where('status', 'alpha')->count());

        foreach (['akademik', 'english', 'quran'] as $jenis) {
            $this->assertDatabaseHas('rapors', ['siswa_id' => $siswa->id, 'jenis_rapor' => $jenis, 'status' => 'published']);
        }

        $quranRapor = Rapor::where('siswa_id', $siswa->id)->where('jenis_rapor', 'quran')->firstOrFail();
        $this->assertSame(12, RaporItem::where('rapor_id', $quranRapor->id)->where('kategori', 'quran_reading')->count());
        $this->assertSame(38, RaporItem::where('rapor_id', $quranRapor->id)->where('kategori', 'quran_surah')->count());
        $this->assertNotNull(TahfidzProgress::where('siswa_id', $siswa->id)->first());
        $this->assertNotNull(TahsinSetoran::where('siswa_id', $siswa->id)->first());
        $this->assertGreaterThanOrEqual(2, TahfidzSetoran::where('siswa_id', $siswa->id)->count());
    }

    public function test_bahan_ajar_headmaster_review_cycle(): void
    {
        [$guruUser, $guru, $siswa, $kelas, $mapel] = $this->demoTeachingContext();
        $kepala = User::where('role', 'kepala_sekolah')->firstOrFail();
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($guruUser)->post('/guru/materi', [
            'judul' => 'ATP Revisi 1 Test',
            'kategori' => 'alur_tahapan_pembelajaran',
            'kode' => 'ATP-TEST-01',
            'deskripsi' => 'Deskripsi lengkap ATP test untuk proses approval kepala sekolah.',
            'isi' => 'Isi ATP test',
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'submit_action' => 'request_approval',
            'file' => UploadedFile::fake()->create('atp-test.pdf', 120),
        ])->assertRedirect();

        $materi = Materi::where('kode', 'ATP-TEST-01')->firstOrFail();
        $this->assertSame('pending', $materi->status);

        $this->actingAs($kepala)->post("/kepala-sekolah/materi/{$materi->id}/request-revision", [
            'catatan_reviewer' => 'Tambahkan diferensiasi aktivitas.',
        ])->assertRedirect();
        $this->assertDatabaseHas('materi', ['id' => $materi->id, 'status' => 'revision_requested']);

        $this->actingAs($guruUser)->post("/guru/materi/{$materi->id}/update", [
            'judul' => 'ATP Revisi 1 Test Final',
            'kategori' => 'alur_tahapan_pembelajaran',
            'kode' => 'ATP-TEST-01',
            'deskripsi' => 'Deskripsi lengkap ATP test dengan diferensiasi aktivitas.',
            'isi' => 'Isi ATP test revisi',
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'submit_action' => 'request_approval',
        ])->assertRedirect();

        $this->actingAs($admin)->post("/admin/materi/{$materi->id}/approve", [
            'skor_kauniyah' => 4,
            'skor_bilingual' => 4,
            'skor_ai' => 5,
        ])->assertRedirect();

        $this->assertDatabaseHas('materi', ['id' => $materi->id, 'status' => 'approved']);
        $this->assertGreaterThanOrEqual(4, MateriApprovalHistory::where('materi_id', $materi->id)->count());
        $this->assertSame($guru->id, $materi->fresh()->guru_id);
        $this->assertSame($siswa->kelas_id, $materi->fresh()->kelas_id);
    }

    public function test_bulk_attendance_only_records_students_from_taught_class(): void
    {
        [$guruUser, $guru, $siswa, $kelas, $mapel] = $this->demoTeachingContext();
        $outsideSiswa = Siswa::where('kelas_id', '!=', $kelas->id)->firstOrFail();

        $this->actingAs($guruUser)->post('/guru/absensi', [
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'tanggal' => '2026-07-25',
            'pertemuan' => 'Revisi1Test',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'Ganjil',
            'absensi' => [
                $siswa->id => ['status' => 'izin', 'keterangan' => 'Izin keluarga'],
                $outsideSiswa->id => ['status' => 'alpha', 'keterangan' => 'Tidak boleh tersimpan'],
            ],
        ])->assertRedirect();

        $this->assertTrue(Kehadiran::where('siswa_id', $siswa->id)
            ->where('kelas_id', $kelas->id)
            ->where('guru_id', $guru->id)
            ->where('mapel_id', $mapel->id)
            ->whereDate('tanggal', '2026-07-25')
            ->where('pertemuan', 'Revisi1Test')
            ->where('status', 'izin')
            ->exists());
        $this->assertFalse(Kehadiran::where('siswa_id', $outsideSiswa->id)
            ->whereDate('tanggal', '2026-07-25')
            ->where('pertemuan', 'Revisi1Test')
            ->exists());
    }

    public function test_jurnal_mengajar_administration_forms_store_records(): void
    {
        [$guruUser, $guru, $siswa, $kelas, $mapel] = $this->demoTeachingContext();

        $this->actingAs($guruUser)->post('/guru/jurnal-mengajar/harian', [
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'hari' => 'Senin',
            'tanggal' => '2026-07-27',
            'jam_ke' => '1-2',
            'bahasan_materi' => 'Operasi hitung bilangan',
            'keterangan' => 'Pembelajaran tuntas',
        ])->assertRedirect();

        $this->actingAs($guruUser)->post('/guru/jurnal-mengajar/sikap', [
            'siswa_id' => $siswa->id,
            'tanggal' => '2026-07-27',
            'kejadian' => 'Membantu teman menyelesaikan latihan.',
            'tindakan' => 'Apresiasi lisan.',
            'paraf' => 'GD',
        ])->assertRedirect();

        $this->actingAs($guruUser)->post('/guru/jurnal-mengajar/pengayaan', [
            'mapel_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'materi' => 'Soal cerita lanjutan',
            'bentuk_pengayaan' => 'Proyek mini',
        ])->assertRedirect();

        $this->actingAs($guruUser)->post('/guru/jurnal-mengajar/remedial', [
            'siswa_id' => $siswa->id,
            'mapel_id' => $mapel->id,
            'materi' => 'Remedial operasi hitung',
            'nilai_sebelum' => 62,
            'nilai_sesudah' => 74,
            'status' => 'selesai',
        ])->assertRedirect();

        $this->actingAs($guruUser)->post('/guru/jurnal-mengajar/administrasi', [
            'dokumen' => 'Program Semester Test',
            'status' => 'lengkap',
            'tanggal_dilengkapi' => '2026-07-27',
        ])->assertRedirect();

        $this->assertDatabaseHas('laporan_mengajars', ['guru_id' => $guru->id, 'tipe' => 'jurnal_harian', 'bahasan_materi' => 'Operasi hitung bilangan']);
        $this->assertTrue(JurnalSikap::where('siswa_id', $siswa->id)->where('kejadian', 'Membantu teman menyelesaikan latihan.')->exists());
        $this->assertTrue(ProgramPengayaan::where('guru_id', $guru->id)->where('materi', 'Soal cerita lanjutan')->exists());
        $this->assertTrue(ProgramRemedial::where('guru_id', $guru->id)->where('materi', 'Remedial operasi hitung')->exists());
        $this->assertTrue(AdministrasiGuruChecklist::where('guru_id', $guru->id)->where('dokumen', 'Program Semester Test')->exists());
    }

    public function test_buku_penghubung_teacher_parent_threaded_reply_works(): void
    {
        [$guruUser, , $siswa] = $this->demoTeachingContext();
        $ortuUser = User::where('email', 'ortu.demo@alazharjayaindonesia.sch.id')->firstOrFail();

        $this->actingAs($guruUser)->post('/guru/pesan', [
            'penerima_id' => $ortuUser->id,
            'siswa_id' => $siswa->id,
            'subjek' => 'Kehadiran Revisi 1',
            'kategori' => 'Kehadiran',
            'isi' => 'Siswa perlu hadir lebih awal.',
        ])->assertRedirect();

        $parent = Pesan::where('subjek', 'Kehadiran Revisi 1')->where('isi', 'Siswa perlu hadir lebih awal.')->firstOrFail();

        $this->actingAs($ortuUser)->post('/ortu/pesan', [
            'penerima_id' => $guruUser->id,
            'siswa_id' => $siswa->id,
            'parent_message_id' => $parent->id,
            'isi' => 'Baik, kami dampingi dari rumah.',
        ])->assertRedirect();

        $reply = Pesan::where('isi', 'Baik, kami dampingi dari rumah.')->firstOrFail();
        $this->assertSame($parent->thread_id, $reply->thread_id);
        $this->assertSame($parent->id, $reply->parent_message_id);
    }

    public function test_report_and_parent_quran_views_render_real_data(): void
    {
        $siswa = $this->demoSiswa();
        $siswaUser = User::where('email', 'siswa.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $ortuUser = User::where('email', 'ortu.demo@alazharjayaindonesia.sch.id')->firstOrFail();

        foreach (['akademik', 'english', 'quran'] as $jenis) {
            $this->actingAs($siswaUser)->get("/rapor/{$jenis}")
                ->assertOk()
                ->assertSee('RAPOR')
                ->assertDontSee('Belum '.'Tersedia');
        }

        $this->actingAs($ortuUser)->get('/dashboard?tab=tahfidz')
            ->assertOk()
            ->assertSee('Progress Quran')
            ->assertSee('Al-Falaq')
            ->assertDontSee('Belum '.'Tersedia');

        $this->actingAs($ortuUser)->get('/rapor/quran?siswa_id='.$siswa->id)
            ->assertOk()
            ->assertSee('RAPOR QURAN')
            ->assertSee('Hafalan Surat Juz 30');
    }

    private function demoTeachingContext(): array
    {
        $guruUser = User::where('email', 'guru.demo@alazharjayaindonesia.sch.id')->firstOrFail();
        $guru = Guru::where('user_id', $guruUser->id)->firstOrFail();
        $siswa = $this->demoSiswa();
        $kelas = Kelas::where('kode_kelas', '7-DEMO')->firstOrFail();
        $mapel = Mapel::where('kode', 'MTK')->firstOrFail();

        return [$guruUser, $guru, $siswa, $kelas, $mapel];
    }

    private function demoSiswa(): Siswa
    {
        return Siswa::where('nis', 'SISWA-DEMO-001')->firstOrFail();
    }
}
