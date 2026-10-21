<?php

namespace App\Http\Controllers;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    /**
     * Menampilkan semua data penjualan beserta relasinya
     */
    public function index()
    {
        // Mengambil data penjualan, sekaligus memuat relasi:
        // 1. user (siapa kasir/user yang melayani)
        // 2. detailPenjualans (item apa saja yang dibeli dalam transaksi itu)
        // 3. detailPenjualans.barang (detail produk pakaian dan satuan barangnya)
        $penjualan = Penjualan::with(['user', 'detailPenjualans.barang.satuan'])->get();

        return response()->json([
            'message' => 'Berhasil mengambil data riwayat penjualan',
            'data' => $penjualan
        ]);
    }

    /**
     * Menampilkan detail satu transaksi berdasarkan ID Penjualan
     */
    public function show($id)
    {
        $penjualan = Penjualan::with(['user', 'detailPenjualans.barang.satuan'])
                              ->where('idpenjualan', $id)
                              ->firstOrFail();

        return response()->json([
            'message' => 'Detail transaksi ditemukan',
            'data' => $penjualan
        ]);
    }
    /**
     * Menyimpan transaksi penjualan baru
     */
    public function store(Request $request)
    {
        // 1. Validasi input yang dikirim dari form/frontend
        $request->validate([
            'iduser' => 'required|exists:user,iduser',
            'idmargin_penjualan' => 'required|exists:margin_penjualan,idmargin_penjualan',
            'items' => 'required|array|min:1',
            'items.*.idbarang' => 'required|exists:barang,idbarang',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        // Gunakan Database Transaction untuk keamanan integritas data
        DB::beginTransaction();

        try {
            $subtotal_nilai = 0;
            $arrDetailPenjualan = [];

            // 2. Loop item barang yang dibeli untuk menghitung subtotal
            foreach ($request->items as $item) {
                $barang = Barang::findOrFail($item['idbarang']);
                
                $harga_satuan = $barang->harga; // Ambil harga asli dari tabel barang
                $jumlah = $item['jumlah'];
                $sub_total = $harga_satuan * $jumlah;

                $subtotal_nilai += $sub_total;

                // Tampung data detail untuk disimpan nanti
                $arrDetailPenjualan[] = [
                    'idbarang' => $barang->idbarang,
                    'harga_satuan' => $harga_satuan,
                    'jumlah' => $jumlah,
                    'subtotal' => $sub_total,
                ];
            }

            // 3. Hitung PPN (misal: 11%) dan Total Nilai Keseluruhan
            $ppn = $subtotal_nilai * 0.11;
            $total_nilai = $subtotal_nilai + $ppn;

            // 4. Simpan data header ke tabel 'penjualan'
            $penjualan = Penjualan::create([
                'iduser' => $request->iduser,
                'idmargin_penjualan' => $request->idmargin_penjualan,
                'subtotal_nilai' => $subtotal_nilai,
                'ppn' => $ppn,
                'total_nilai' => $total_nilai,
            ]);

            // 5. Simpan setiap item ke tabel 'detail_penjualan'
            foreach ($arrDetailPenjualan as $detail) {
                DetailPenjualan::create([
                    'penjualan_idpenjualan' => $penjualan->idpenjualan,
                    'idbarang' => $detail['idbarang'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'jumlah' => $detail['jumlah'],
                    'subtotal' => $detail['subtotal'],
                ]);
            }

            // Jika semua proses sukses, commit transaksi ke database
            DB::commit();

            return response()->json([
                'message' => 'Transaksi penjualan berhasil disimpan!',
                'data' => $penjualan->load('detailPenjualans')
            ], 201);

        } catch (\Exception $e) {
            // Jika ada error, batalkan semua perubahan di database
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan transaksi penjualan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
