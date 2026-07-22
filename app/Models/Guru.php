<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    protected $table = 'guru';
    protected $fillable = [
        'user_id', 'nip', 'nama', 'mapel_id',
        'alamat', 'no_telp', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function laporanMengajar()
    {
        return $this->hasMany(LaporanMengajar::class, 'guru_id');
    }

    public function mapels()
    {
        return $this->belongsToMany(Mapel::class, 'guru_mapel', 'guru_id', 'mapel_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function tahfidzSetoran()
    {
        return $this->hasMany(TahfidzSetoran::class);
    }

    public function tahsinSetoran()
    {
        return $this->hasMany(TahsinSetoran::class);
    }

    public function cbtExams()
    {
        return $this->hasMany(CbtExam::class);
    }

    public function olympiadExams()
    {
        return $this->hasMany(OlympiadExam::class);
    }

    public function workbooks()
    {
        return $this->hasMany(Workbook::class);
    }

    public function catatanWali()
    {
        return $this->hasMany(CatatanWali::class, 'created_by');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class);
    }

    public function jurnalSikap()
    {
        return $this->hasMany(JurnalSikap::class);
    }

    public function programPengayaan()
    {
        return $this->hasMany(ProgramPengayaan::class);
    }

    public function programRemedial()
    {
        return $this->hasMany(ProgramRemedial::class);
    }

    public function administrasiChecklists()
    {
        return $this->hasMany(AdministrasiGuruChecklist::class);
    }
}
