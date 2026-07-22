<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MateriApprovalHistory extends Model
{
    protected $fillable = [
        'materi_id',
        'actor_id',
        'action',
        'status_from',
        'status_to',
        'catatan',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
