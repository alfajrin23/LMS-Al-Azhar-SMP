<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru', function (Blueprint $table) {
            $table->foreignId('mapel_id')->nullable()->constrained('mapel')->nullOnDelete();
        });

        Schema::table('materi', function (Blueprint $table) {
            $table->string('kategori', 80)->default('kompetensi_inti')->index();
            $table->string('kode', 80)->nullable();
            $table->text('isi')->nullable();
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();
            $table->unsignedInteger('versi')->default(1);
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamp('reviewed_at')->nullable()->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_reviewer')->nullable();

            $table->index(['guru_id', 'kategori', 'status']);
            $table->index(['kelas_id', 'mapel_id', 'status']);
        });

        DB::table('materi')
            ->whereNull('kategori')
            ->orWhere('kategori', '')
            ->update(['kategori' => 'kompetensi_inti']);

        Schema::create('materi_approval_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('materi_id')->constrained('materi')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->string('status_from', 50)->nullable();
            $table->string('status_to', 50);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['materi_id', 'created_at']);
            $table->index(['actor_id', 'action']);
        });

        Schema::table('nilai', function (Blueprint $table) {
            $table->string('tahun_ajaran', 20)->default('2025/2026')->index();
            $table->string('semester', 20)->default('Genap')->index();
            $table->string('jenis_rapor', 40)->default('akademik')->index();
            $table->string('lingkup_materi')->nullable();
            $table->text('tujuan_pembelajaran')->nullable();
            $table->json('tp_scores')->nullable();
            $table->json('tugas_scores')->nullable();
            $table->json('sumatif_scores')->nullable();
            $table->decimal('nilai_sumatif', 5, 2)->nullable();
            $table->text('capaian_kompetensi')->nullable();
            $table->text('kompetensi_dikuasai')->nullable();
            $table->text('kompetensi_perlu_ditingkatkan')->nullable();

            $table->index(['siswa_id', 'tahun_ajaran', 'semester', 'jenis_rapor'], 'nilai_period_idx');
        });

        Schema::create('rapors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->string('tahun_ajaran', 20)->index();
            $table->string('semester', 20)->index();
            $table->enum('jenis_rapor', ['akademik', 'english', 'quran'])->index();
            $table->enum('status', ['draft', 'published'])->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->json('snapshot')->nullable();
            $table->json('signature_metadata')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran', 'semester', 'jenis_rapor'], 'rapor_period_unique');
        });

        Schema::create('rapor_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapors')->cascadeOnDelete();
            $table->foreignId('mapel_id')->nullable()->constrained('mapel')->nullOnDelete();
            $table->string('kategori', 80)->nullable()->index();
            $table->string('komponen');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->string('predikat', 40)->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['rapor_id', 'kategori']);
        });

        Schema::table('laporan_mengajars', function (Blueprint $table) {
            $table->foreignId('mapel_id')->nullable()->constrained('mapel')->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('hari', 30)->nullable();
            $table->string('jam_ke', 50)->nullable();
            $table->text('bahasan_materi')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();

            $table->index(['guru_id', 'tahun_ajaran', 'semester', 'tipe']);
            $table->index(['kelas_id', 'tanggal']);
        });

        Schema::table('kehadiran', function (Blueprint $table) {
            $table->dropUnique('kehadiran_siswa_id_tanggal_unique');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('mapel_id')->nullable()->constrained('mapel')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->string('pertemuan', 50)->nullable();
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();

            $table->unique(['siswa_id', 'tanggal', 'mapel_id', 'pertemuan'], 'kehadiran_siswa_pertemuan_unique');
            $table->index(['kelas_id', 'tanggal', 'status']);
            $table->index(['guru_id', 'tanggal']);
        });

        Schema::table('pesan', function (Blueprint $table) {
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->nullOnDelete();
            $table->string('kategori', 80)->default('Lainnya')->index();
            $table->dateTime('tanggal')->nullable()->index();
            $table->foreignId('parent_message_id')->nullable()->constrained('pesan')->nullOnDelete();
            $table->string('thread_id', 80)->nullable()->index();
            $table->string('lampiran_path')->nullable();

            $table->index(['penerima_id', 'dibaca', 'created_at']);
            $table->index(['siswa_id', 'created_at']);
        });

        DB::table('pesan')->orderBy('id')->get()->each(function ($pesan) {
            DB::table('pesan')
                ->where('id', $pesan->id)
                ->update([
                    'tanggal' => $pesan->created_at,
                    'thread_id' => $pesan->thread_id ?: 'pesan-'.$pesan->id,
                    'kategori' => $pesan->kategori ?: 'Lainnya',
                ]);
        });

        Schema::table('tahfidz_progress', function (Blueprint $table) {
            $table->string('tingkat_ummi')->nullable();
            $table->string('posisi_tilawah')->nullable();
            $table->string('hafalan_terakhir')->nullable();
            $table->unsignedTinyInteger('kelancaran')->nullable();
            $table->unsignedTinyInteger('tajwid')->nullable();
            $table->unsignedTinyInteger('makhroj')->nullable();
            $table->unsignedTinyInteger('adab')->nullable();
            $table->string('predikat', 50)->nullable();
            $table->string('target_berikutnya')->nullable();
            $table->date('tanggal_pertemuan_berikutnya')->nullable();
        });

        Schema::table('tahfidz_setoran', function (Blueprint $table) {
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();
        });

        Schema::table('tahsin_setorans', function (Blueprint $table) {
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();
        });

        Schema::table('tugas', function (Blueprint $table) {
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();
        });

        Schema::table('workbooks', function (Blueprint $table) {
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();
        });

        Schema::create('jurnal_sikaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->date('tanggal')->index();
            $table->text('kejadian');
            $table->text('tindakan')->nullable();
            $table->string('paraf')->nullable();
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();
            $table->timestamps();

            $table->index(['kelas_id', 'tanggal']);
            $table->index(['guru_id', 'tanggal']);
        });

        Schema::create('program_pengayaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('mapel_id')->nullable()->constrained('mapel')->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('kompetensi_dasar')->nullable();
            $table->text('materi')->nullable();
            $table->text('bentuk_pengayaan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();
            $table->timestamps();

            $table->index(['guru_id', 'tahun_ajaran', 'semester']);
        });

        Schema::create('program_remedials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remedial_id')->nullable()->constrained('remedials')->nullOnDelete();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('mapel_id')->nullable()->constrained('mapel')->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->string('kompetensi_dasar')->nullable();
            $table->text('materi')->nullable();
            $table->decimal('nilai_sebelum', 5, 2)->nullable();
            $table->decimal('nilai_sesudah', 5, 2)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('paraf')->nullable();
            $table->enum('status', ['pending', 'selesai', 'tidak_lulus'])->default('pending')->index();
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();
            $table->timestamps();

            $table->index(['guru_id', 'tahun_ajaran', 'semester']);
            $table->index(['siswa_id', 'status']);
        });

        Schema::create('administrasi_guru_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->string('dokumen');
            $table->enum('status', ['belum_lengkap', 'lengkap', 'terverifikasi'])->default('belum_lengkap')->index();
            $table->date('tanggal_dilengkapi')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('catatan_reviewer')->nullable();
            $table->string('tahun_ajaran', 20)->default('2026/2027')->index();
            $table->string('semester', 20)->default('Ganjil')->index();
            $table->timestamps();

            $table->unique(['guru_id', 'dokumen', 'tahun_ajaran', 'semester'], 'administrasi_guru_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administrasi_guru_checklists');
        Schema::dropIfExists('program_remedials');
        Schema::dropIfExists('program_pengayaans');
        Schema::dropIfExists('jurnal_sikaps');

        Schema::table('workbooks', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran']);
            $table->dropIndex(['semester']);
            $table->dropColumn(['tahun_ajaran', 'semester']);
        });

        Schema::table('tugas', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran']);
            $table->dropIndex(['semester']);
            $table->dropColumn(['tahun_ajaran', 'semester']);
        });

        Schema::table('tahsin_setorans', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran']);
            $table->dropIndex(['semester']);
            $table->dropColumn(['tahun_ajaran', 'semester']);
        });

        Schema::table('tahfidz_setoran', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran']);
            $table->dropIndex(['semester']);
            $table->dropColumn(['tahun_ajaran', 'semester']);
        });

        Schema::table('tahfidz_progress', function (Blueprint $table) {
            $table->dropColumn([
                'tingkat_ummi',
                'posisi_tilawah',
                'hafalan_terakhir',
                'kelancaran',
                'tajwid',
                'makhroj',
                'adab',
                'predikat',
                'target_berikutnya',
                'tanggal_pertemuan_berikutnya',
            ]);
        });

        Schema::table('pesan', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['thread_id']);
            $table->dropIndex(['penerima_id', 'dibaca', 'created_at']);
            $table->dropIndex(['siswa_id', 'created_at']);
            $table->dropForeign(['siswa_id']);
            $table->dropForeign(['parent_message_id']);
            $table->dropColumn([
                'siswa_id',
                'kategori',
                'tanggal',
                'parent_message_id',
                'thread_id',
                'lampiran_path',
            ]);
        });

        Schema::table('kehadiran', function (Blueprint $table) {
            $table->dropUnique('kehadiran_siswa_pertemuan_unique');
            $table->dropIndex(['tahun_ajaran']);
            $table->dropIndex(['semester']);
            $table->dropIndex(['kelas_id', 'tanggal', 'status']);
            $table->dropIndex(['guru_id', 'tanggal']);
            $table->dropForeign(['kelas_id']);
            $table->dropForeign(['mapel_id']);
            $table->dropForeign(['guru_id']);
            $table->dropColumn([
                'kelas_id',
                'mapel_id',
                'guru_id',
                'pertemuan',
                'tahun_ajaran',
                'semester',
            ]);
            $table->unique(['siswa_id', 'tanggal']);
        });

        Schema::table('laporan_mengajars', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran']);
            $table->dropIndex(['semester']);
            $table->dropIndex(['guru_id', 'tahun_ajaran', 'semester', 'tipe']);
            $table->dropIndex(['kelas_id', 'tanggal']);
            $table->dropForeign(['mapel_id']);
            $table->dropForeign(['kelas_id']);
            $table->dropColumn([
                'mapel_id',
                'kelas_id',
                'hari',
                'jam_ke',
                'bahasan_materi',
                'keterangan',
                'tahun_ajaran',
                'semester',
            ]);
        });

        Schema::dropIfExists('rapor_items');
        Schema::dropIfExists('rapors');

        Schema::table('nilai', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran']);
            $table->dropIndex(['semester']);
            $table->dropIndex(['jenis_rapor']);
            $table->dropIndex('nilai_period_idx');
            $table->dropColumn([
                'tahun_ajaran',
                'semester',
                'jenis_rapor',
                'lingkup_materi',
                'tujuan_pembelajaran',
                'tp_scores',
                'tugas_scores',
                'sumatif_scores',
                'nilai_sumatif',
                'capaian_kompetensi',
                'kompetensi_dikuasai',
                'kompetensi_perlu_ditingkatkan',
            ]);
        });

        Schema::dropIfExists('materi_approval_histories');

        Schema::table('materi', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
            $table->dropIndex(['tahun_ajaran']);
            $table->dropIndex(['semester']);
            $table->dropIndex(['submitted_at']);
            $table->dropIndex(['reviewed_at']);
            $table->dropIndex(['guru_id', 'kategori', 'status']);
            $table->dropIndex(['kelas_id', 'mapel_id', 'status']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'kategori',
                'kode',
                'isi',
                'tahun_ajaran',
                'semester',
                'versi',
                'submitted_at',
                'reviewed_at',
                'reviewed_by',
                'catatan_reviewer',
            ]);
        });

        Schema::table('guru', function (Blueprint $table) {
            $table->dropForeign(['mapel_id']);
            $table->dropColumn('mapel_id');
        });
    }
};
