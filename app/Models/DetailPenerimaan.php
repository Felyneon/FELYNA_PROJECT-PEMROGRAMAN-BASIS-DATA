<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPenerimaan extends Model
{
    protected $table = 'detail_penerimaan';
    protected $primaryKey = 'iddetail_penerimaan';
    public $timestamps = false;

    protected $fillable = [
        'idpenerimaan',
        'barang_idbarang',
        'jumlah_terima',
        'harga_satuan_terima',
        'sub_total_terima',
    ];
}