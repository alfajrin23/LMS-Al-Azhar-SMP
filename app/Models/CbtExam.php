<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CbtExam extends Model
{
    protected $fillable = [
        'judul', 'tipe', 'deskripsi', 'mapel_id', 'kelas_id', 'guru_id',
        'durasi', 'jumlah_soal', 'status',
        'approved_by', 'approved_at', 'catatan_reject', 'metode'
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
        return $this->hasMany(CbtSoal::class);
    }
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
