<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Remedial extends Model
{
    protected $table = 'remedials';
    protected $fillable = [
        'siswa_id', 'mapel_id', 'nilai_id', 'nilai_asal', 'deadline', 'status'
    ];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }
    public function nilai()
    {
        return $this->belongsTo(Nilai::class);
    }
}
