<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Pembayaran extends Model
{
    protected $table = 'pembayarans';
    protected $fillable = [
        'spp_id', 'orang_tua_id', 'tanggal_bayar', 'jumlah',
        'metode', 'bukti', 'status'
    ];
    public function spp()
    {
        return $this->belongsTo(Spp::class);
    }
    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class);
    }
}
