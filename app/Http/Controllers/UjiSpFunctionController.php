<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UjiSpFunctionController extends Controller
{
    public function index(Request $request)
    {
        // Parameter dari form (punya nilai default)
        $iduser   = (int) $request->query('iduser', 2);
        $idvendor = (int) $request->query('idvendor', 1);
        $idbarang = (int) $request->query('idbarang', 3);
        $idmargin = (int) $request->query('idmargin', 1);
        $batas    = (int) $request->query('batas', 10);

        $hasil = [

            // ============ FUNCTION (dipanggil dengan SELECT) ============
            [
                'jenis' => 'FUNCTION',
                'nama'  => 'fn_hitung_harga_jual',
                'info'  => 'Harga jual = harga dasar + margin (%) yang dipilih.',
                'sql'   => "SELECT fn_hitung_harga_jual($idbarang, $idmargin)",
                'rows'  => DB::select(
                    'SELECT b.nama AS nama_barang,
                            b.harga AS harga_dasar,
                            m.persen AS margin_persen,
                            fn_hitung_harga_jual(b.idbarang, m.idmargin_penjualan) AS harga_jual
                     FROM barang b
                     JOIN margin_penjualan m ON m.idmargin_penjualan = ?
                     WHERE b.idbarang = ?',
                    [$idmargin, $idbarang]
                ),
            ],
            [
                'jenis' => 'FUNCTION',
                'nama'  => 'fn_status_badan_hukum_vendor',
                'info'  => 'Mengubah kode badan hukum (P/C/U) menjadi teks lengkap.',
                'sql'   => 'SELECT nama_vendor, fn_status_badan_hukum_vendor(idvendor) FROM vendor',
                'rows'  => DB::select(
                    'SELECT idvendor, nama_vendor, badan_hukum AS kode,
                            fn_status_badan_hukum_vendor(idvendor) AS keterangan_badan_hukum
                     FROM vendor'
                ),
            ],
            [
                'jenis' => 'FUNCTION',
                'nama'  => 'fn_get_sisa_stok',
                'info'  => 'Sisa stok terkini per barang (dari baris kartu stok terakhir).',
                'sql'   => 'SELECT nama, fn_get_sisa_stok(idbarang) FROM barang',
                'rows'  => DB::select(
                    'SELECT idbarang, nama, fn_get_sisa_stok(idbarang) AS stok_terkini
                     FROM barang'
                ),
            ],
            [
                'jenis' => 'FUNCTION',
                'nama'  => 'fn_hitung_total_penerimaan',
                'info'  => 'Total nilai penerimaan = jumlah sub_total_terima di detail penerimaan.',
                'sql'   => 'SELECT idpenerimaan, fn_hitung_total_penerimaan(idpenerimaan) FROM penerimaan',
                'rows'  => DB::select(
                    'SELECT idpenerimaan, idpengadaan,
                            fn_hitung_total_penerimaan(idpenerimaan) AS total_nilai_penerimaan
                     FROM penerimaan'
                ),
            ],

            // ============ STORED PROCEDURE (dipanggil dengan CALL) ============
            [
                'jenis' => 'PROCEDURE',
                'nama'  => 'sp_stok_barang_rendah',
                'info'  => 'Barang yang stoknya kurang dari atau sama dengan batas.',
                'sql'   => "CALL sp_stok_barang_rendah($batas)",
                'rows'  => DB::select('CALL sp_stok_barang_rendah(?)', [$batas]),
            ],
            [
                'jenis' => 'PROCEDURE',
                'nama'  => 'sp_riwayat_kartu_stok_barang',
                'info'  => 'Riwayat mutasi kartu stok untuk satu barang.',
                'sql'   => "CALL sp_riwayat_kartu_stok_barang($idbarang)",
                'rows'  => DB::select('CALL sp_riwayat_kartu_stok_barang(?)', [$idbarang]),
            ],
            [
                'jenis' => 'PROCEDURE',
                'nama'  => 'sp_laporan_penjualan_berdasarkan_user',
                'info'  => 'Rekap penjualan yang dilayani satu kasir/user.',
                'sql'   => "CALL sp_laporan_penjualan_berdasarkan_user($iduser)",
                'rows'  => DB::select('CALL sp_laporan_penjualan_berdasarkan_user(?)', [$iduser]),
            ],
            [
                'jenis' => 'PROCEDURE',
                'nama'  => 'sp_laporan_pengadaan_vendor',
                'info'  => 'Rekap pengadaan berdasarkan satu vendor.',
                'sql'   => "CALL sp_laporan_pengadaan_vendor($idvendor)",
                'rows'  => DB::select('CALL sp_laporan_pengadaan_vendor(?)', [$idvendor]),
            ],
        ];

        // Data untuk dropdown form
        $users    = DB::table('user')->select('iduser', 'username')->orderBy('iduser')->get();
        $vendors  = DB::table('vendor')->select('idvendor', 'nama_vendor')->orderBy('idvendor')->get();
        $barangs  = DB::table('barang')->select('idbarang', 'nama')->orderBy('idbarang')->get();
        $margins  = DB::table('margin_penjualan')->select('idmargin_penjualan', 'persen')
                        ->where('status', 1)->orderBy('idmargin_penjualan')->get();

        return view('sp_function.uji_sp_function', compact(
            'hasil', 'users', 'vendors', 'barangs', 'margins',
            'iduser', 'idvendor', 'idbarang', 'idmargin', 'batas'
        ));
    }
}