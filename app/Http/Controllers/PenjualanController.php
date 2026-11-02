<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Barang;
use App\Models\MarginPenjualan;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualans = Penjualan::with(['user', 'marginPenjualan'])->get();
        return view('transaksi.penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $barangs = Barang::all();
        $margins = MarginPenjualan::where('status', 1)->get();
        return view('transaksi.penjualan.create', compact('barangs', 'margins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idbarang' => 'required|array',
            'jumlah' => 'required|array',
            'idmargin_penjualan' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Header dibuat dengan nilai 0. Subtotal, PPN, dan total diisi otomatis oleh trigger.
                $penjualan = Penjualan::create([
                    'subtotal_nilai' => 0,
                    'ppn' => 0,
                    'total_nilai' => 0,
                    'iduser' => session('iduser') ?? 1,
                    'idmargin_penjualan' => $request->idmargin_penjualan,
                ]);

                foreach ($request->idbarang as $index => $idbarang) {
                    // TRIGGER trg_before_detail_penjualan : validasi stok, harga_satuan 0 diisi
                    //                                       fn_hitung_harga_jual, hitung subtotal
                    // TRIGGER trg_after_detail_penjualan  : update total header + kartu stok keluar (K)
                    DetailPenjualan::create([
                        'penjualan_idpenjualan' => $penjualan->idpenjualan,
                        'idbarang' => $idbarang,
                        'jumlah' => $request->jumlah[$index],
                        'harga_satuan' => 0,
                        'subtotal' => 0,
                    ]);
                }
            });
        } catch (QueryException $e) {
            return back()->withInput()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/penjualan')->with('success', 'Transaksi penjualan berhasil dicatat!');
    }

    // STORED PROCEDURE sp_laporan_penjualan_berdasarkan_user
    public function laporan(Request $request)
    {
        $users  = DB::table('user')->select('iduser', 'username')->orderBy('iduser')->get();
        $iduser = $request->query('iduser');
        $data   = $iduser
            ? DB::select('CALL sp_laporan_penjualan_berdasarkan_user(?)', [$iduser])
            : [];

        return view('sp_function.laporan', compact('users', 'iduser', 'data'));
    }
}