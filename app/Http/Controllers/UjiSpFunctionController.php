<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UjiSpFunctionController extends Controller
{
    public function index(Request $request)
    {
        $iduser   = (int) $request->query('iduser', 2);
        $idvendor = (int) $request->query('idvendor', 1);
        $idbarang = (int) $request->query('idbarang', 3);
        $idmargin = (int) $request->query('idmargin', 1);
        $jumlah   = max(1, (int) $request->query('jumlah', 3));

        $db = DB::selectOne('SELECT DATABASE() AS nama')->nama;

        $hasil = [

            // ============ FUNCTION (dipanggil dengan SELECT) ============
            [
                'jenis' => 'FUNCTION',
                'nama'  => 'fn_hitung_harga_jual',
                'info'  => 'Harga jual = harga dasar + margin (%) yang dipilih.',
                'sql'   => "SELECT fn_hitung_harga_jual($idbarang, $idmargin)",
                'rows'  => DB::select(
                    'SELECT b.nama AS nama_barang, b.harga AS harga_dasar, m.persen AS margin_persen,
                            fn_hitung_harga_jual(b.idbarang, m.idmargin_penjualan) AS harga_jual
                     FROM barang b
                     JOIN margin_penjualan m ON m.idmargin_penjualan = ?
                     WHERE b.idbarang = ?',
                    [$idmargin, $idbarang]
                ),
            ],
            [
                'jenis' => 'FUNCTION',
                'nama'  => 'fn_hitung_subtotal',
                'info'  => 'Subtotal baris detail = harga satuan x jumlah (dipakai trigger detail pengadaan, penerimaan, penjualan).',
                'sql'   => "SELECT fn_hitung_subtotal(harga, $jumlah) FROM barang",
                'rows'  => DB::select(
                    'SELECT nama AS nama_barang, harga AS harga_dasar, ? AS jumlah,
                            fn_hitung_subtotal(harga, ?) AS subtotal
                     FROM barang WHERE idbarang = ?',
                    [$jumlah, $jumlah, $idbarang]
                ),
            ],
            [
                'jenis' => 'FUNCTION',
                'nama'  => 'fn_hitung_ppn',
                'info'  => 'PPN 11% dari subtotal (dipakai SP sp_update_total_pengadaan dan sp_update_total_penjualan).',
                'sql'   => "SELECT fn_hitung_ppn(fn_hitung_subtotal(harga, $jumlah)) FROM barang",
                'rows'  => DB::select(
                    'SELECT nama AS nama_barang,
                            fn_hitung_subtotal(harga, ?) AS subtotal,
                            fn_hitung_ppn(fn_hitung_subtotal(harga, ?)) AS ppn
                     FROM barang WHERE idbarang = ?',
                    [$jumlah, $jumlah, $idbarang]
                ),
            ],
            [
                'jenis' => 'FUNCTION',
                'nama'  => 'fn_get_margin_aktif',
                'info'  => 'Id margin yang sedang aktif (hanya boleh 1). Dipakai penjualan agar margin terisi otomatis.',
                'sql'   => 'SELECT fn_get_margin_aktif()',
                'rows'  => DB::select(
                    'SELECT fn_get_margin_aktif() AS idmargin_aktif,
                            (SELECT persen FROM margin_penjualan WHERE idmargin_penjualan = fn_get_margin_aktif()) AS persen'
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
                    'SELECT idbarang, nama, fn_get_sisa_stok(idbarang) AS stok_terkini FROM barang'
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
            // sp_stok_barang_rendah DIHAPUS: notifikasi stok rendah sekarang di frontend.
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

        // Dropdown form (raw SQL)
        $users   = DB::select('SELECT iduser, username FROM `user` ORDER BY iduser');
        $vendors = DB::select('SELECT idvendor, nama_vendor FROM vendor ORDER BY idvendor');
        $barangs = DB::select('SELECT idbarang, nama FROM barang ORDER BY idbarang');
        // Semua margin (aktif & nonaktif) boleh dipakai untuk menguji function
        $margins = DB::select('SELECT idmargin_penjualan, persen, status FROM margin_penjualan ORDER BY idmargin_penjualan');

        return view('sp_function.uji_sp_function', compact(
            'db', 'hasil', 'users', 'vendors', 'barangs', 'margins',
            'iduser', 'idvendor', 'idbarang', 'idmargin', 'jumlah'
        ));
    }
}