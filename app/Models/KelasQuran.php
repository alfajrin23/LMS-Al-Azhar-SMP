<?php
namespace App\Models;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
class KelasQuran extends Model
{
    protected $table = "kelas_quran";
    protected $fillable = [
        'nama_kelas',
        'jenjang',
        'kategori',
        'tingkat'
    ];
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
}
