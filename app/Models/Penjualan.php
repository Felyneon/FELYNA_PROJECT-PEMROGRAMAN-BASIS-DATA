<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'idpenjualan';
    public $timestamps = false; // Atur true jika ingin memanfaatkan created_at otomatis Laravel

    protected $fillable = [
    'subtotal_nilai', 'ppn', 'total_nilai', 'iduser', 'idmargin_penjualan',
];
    // Relasi: Transaksi dicatat oleh satu User
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }

    // Relasi: Transaksi memiliki banyak Item Detail Penjualan
    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_idpenjualan', 'idpenjualan');
    }
    public function marginPenjualan()
{
    return $this->belongsTo(MarginPenjualan::class, 'idmargin_penjualan', 'idmargin_penjualan');
}
}