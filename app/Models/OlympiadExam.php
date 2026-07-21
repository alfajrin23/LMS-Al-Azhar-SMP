<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OlympiadExam extends Model
{
    protected $fillable = [
        'judul', 'deskripsi', 'mapel_id', 'kelas_id', 'guru_id',
        'tingkat', 'durasi', 'jumlah_soal', 'status',
        'approved_by', 'approved_at', 'catatan_reject'
    ];
    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
    public function soals()
    {
        return $this->hasMany(OlympiadSoal::class);
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
