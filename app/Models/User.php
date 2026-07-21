<?php
namespace App\Models;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }
    public function guru()
    {
        return $this->hasOne(Guru::class);
    }
    public function orangTua()
    {
        return $this->hasOne(OrangTua::class);
    }
    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'created_by');
    }
    public function pesanDikirim()
    {
        return $this->hasMany(Pesan::class, 'pengirim_id');
    }
    public function pesanDiterima()
    {
        return $this->hasMany(Pesan::class, 'penerima_id');
    }
}
