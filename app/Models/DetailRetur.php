<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailRetur extends Model
{
    protected $table = 'detail_retur';
    protected $primaryKey = 'iddetail_retur';
    public $timestamps = false;

    protected $fillable = [
        'jumlah',
        'alasan',
        'idretur',
        'iddetail_penerimaan',
    ];
}
