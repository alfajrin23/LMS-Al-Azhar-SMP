<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class KondisiKelas extends Model
{
    protected $table = 'kondisi_kelas';
    protected $fillable = [
        'siswa_id', 'kelas_id', 'hubungan_guru_siswa', 'siswa_nyaman', 'siswa_minta_bantuan', 'tanggal'
    ];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
