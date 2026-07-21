<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NilaiKti extends Model
{
    protected $table = 'nilai_ktis';
    protected $fillable = [
        'siswa_id', 'judul_kti', 'current_bab', 'nilai_proses', 'nilai_tulisan', 'nilai_sidang', 'jadwal_sidang', 'nilai_akhir', 'catatan'
    ];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
