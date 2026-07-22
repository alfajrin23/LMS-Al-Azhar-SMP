<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesan extends Model
{
    protected $table = 'pesan';
    protected $fillable = [
        'pengirim_id',
        'penerima_id',
        'siswa_id',
        'subjek',
        'kategori',
        'isi',
        'tanggal',
        'dibaca',
        'parent_message_id',
        'thread_id',
        'lampiran_path',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
        'tanggal' => 'datetime',
    ];

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function parent()
    {
        return $this->belongsTo(Pesan::class, 'parent_message_id');
    }

    public function replies()
    {
        return $this->hasMany(Pesan::class, 'parent_message_id');
    }
}
