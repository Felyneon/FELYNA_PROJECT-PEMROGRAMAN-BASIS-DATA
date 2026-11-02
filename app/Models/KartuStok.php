<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    protected $table = 'kartu_stok';
    protected $primaryKey = 'idkartu_stok';
    public $timestamps = false;

    protected $fillable = [
        'jenis_transaksi',
        'masuk',
        'keluar',
        'stock',
        'created_at',
        'idtransaksi',
        'idbarang'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'idbarang', 'idbarang');
    }
}