<?php

namespace App\Http\Middleware;

use App\Support\AksesKasir;
use Closure;
use Illuminate\Http\Request;

/**
 * Pemakaian di route:
 *   ->middleware('akses')                  hanya cek sudah login
 *   ->middleware('akses:penjualan.read')   cek login + izin tertentu
 */
class CekAkses
{
    public function handle(Request $request, Closure $next, ?string $izin = null)
    {
        if (!session()->has('iduser')) {
            return redirect('/login');
        }
            \Log::info('CEK HAK AKSES', [
    'iduser' => session('iduser'),
    'idrole' => session('idrole'),
    'daftar_izin' => \App\Support\AksesKasir::izin(),
    'database' => \DB::connection()->getDatabaseName(),
    'url' => $request->path(),
]);
        if ($izin !== null && !AksesKasir::bisa($izin)) {
            abort(403, 'Role Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}