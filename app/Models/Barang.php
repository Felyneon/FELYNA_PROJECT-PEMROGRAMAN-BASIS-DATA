<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'idbarang';
    public $timestamps = false;
    protected $fillable = [
        'jenis', 
        'nama', 
        'idsatuan', 
        'status', 
        'harga', 
        'deskripsi', 
        'ukuran', 
        'gambar'
    ];

    // Relasi: Barang memiliki satu Satuan
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'idsatuan', 'idsatuan');
    }

    // Relasi: Barang dapat masuk ke banyak Detail Penjualan
    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class, 'idbarang', 'idbarang');
    }
}