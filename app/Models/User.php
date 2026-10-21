<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'iduser';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'idrole',
    ];

    // Relasi: User ini memiliki satu Role tertentu
    public function role()
    {
        return $this->belongsTo(Role::class, 'idrole', 'idrole');
    }

    // Relasi: User mencatat banyak data Penjualan
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'iduser', 'iduser');
    }
}