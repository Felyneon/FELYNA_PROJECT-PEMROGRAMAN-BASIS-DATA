<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Pengecekan hak akses berdasarkan tabel role_izin (raw SQL).
 * Dipakai oleh middleware CekAkses, controller, dan Blade (@akses).
 */
class AksesKasir
{
    private static ?array $izin = null;

    /** Daftar izin milik role user yang sedang login (dibaca sekali per request) */
    public static function izin(): array
    {
        if (self::$izin === null) {
            $idrole = session('idrole');
            self::$izin = $idrole
                ? array_column(DB::select("SELECT izin FROM role_izin WHERE idrole = ?", [$idrole]), 'izin')
                : [];
        }
        return self::$izin;
    }

    public static function bisa(string $izin): bool
    {
        $daftar = self::izin();
        return in_array('*', $daftar, true) || in_array($izin, $daftar, true);
    }
}