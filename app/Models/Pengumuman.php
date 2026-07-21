<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Pengumuman extends Model
{
    protected $table = 'pengumuman';
    protected $fillable = ['judul', 'konten', 'created_by', 'target_role'];
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
