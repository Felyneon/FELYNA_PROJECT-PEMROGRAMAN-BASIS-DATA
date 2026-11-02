<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
        protected $table = 'detail_penjualan';
    protected $primaryKey = 'iddetail_penjualan';
    public $timestamps = false;

    protected $fillable = [
        'harga_satuan',
        'jumlah',
        'subtotal',
        'penjualan_idpenjualan',
        'idbarang'
    ];

    // Relasi: Detail penjualan merujuk ke satu Header Penjualan
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_idpenjualan', 'idpenjualan');
    }

    // Relasi: Detail penjualan merujuk ke satu Data Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'idbarang', 'idbarang');
    }
}
