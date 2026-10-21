<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $table = 'satuan';
    protected $primaryKey = 'idsatuan';
    public $timestamps = false;

    // Relasi: Satu Satuan bisa digunakan oleh banyak Barang
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'idsatuan', 'idsatuan');
    }
}