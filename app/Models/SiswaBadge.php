<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SiswaBadge extends Model
{
    protected $table = 'siswa_badge';
    public $timestamps = false;
    protected $fillable = ['siswa_id', 'badge_id', 'achieved_at'];
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
