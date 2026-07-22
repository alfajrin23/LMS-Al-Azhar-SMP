<?php

namespace App\Models;

use App\Models\KelasQuran;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $fillable = [
        'user_id',
        'nis',
        'nama',
        'kelas_id',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_ayah',
        'nama_ibu',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function orangTua()
    {
        return $this->belongsToMany(OrangTua::class, 'orang_tua_siswa');
    }

    public function tahfidzSetoran()
    {
        return $this->hasMany(TahfidzSetoran::class);
    }

    public function tahfidzProgress()
    {
        return $this->hasOne(TahfidzProgress::class);
    }

    public function tahsinSetoran()
    {
        return $this->hasMany(TahsinSetoran::class);
    }

    public function nilai()
    {
        return $this->hasMany(Nilai::class);
    }

    public function rapors()
    {
        return $this->hasMany(Rapor::class);
    }

    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class);
    }

    public function catatanWali()
    {
        return $this->hasMany(CatatanWali::class);
    }

    public function jurnalSikap()
    {
        return $this->hasMany(JurnalSikap::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'siswa_badge')
            ->withPivot('achieved_at');
    }

    public function cbtJawabans()
    {
        return $this->hasMany(CbtJawaban::class);
    }

    public function olympiadJawabans()
    {
        return $this->hasMany(OlympiadJawaban::class);
    }

    public function workbookJawabans()
    {
        return $this->hasMany(WorkbookJawaban::class);
    }

    public function spps()
    {
        return $this->hasMany(Spp::class);
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    public function ktiBimbingan()
    {
        return $this->hasMany(KtiBimbingan::class);
    }

    public function kelas_quran()
    {
        return $this->belongsTo(KelasQuran::class);
    }

    public function kelasQuran()
    {
        return $this->belongsTo(KelasQuran::class);
    }
}
