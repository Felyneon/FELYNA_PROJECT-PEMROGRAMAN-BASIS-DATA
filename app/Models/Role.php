<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'idrole';
    public $timestamps = false; // Karena tidak ada kolom created_at/updated_at di tabel role

    // Relasi: Satu Role bisa dimiliki oleh banyak User
    public function users()
    {
        return $this->hasMany(User::class, 'idrole', 'idrole');
    }
}