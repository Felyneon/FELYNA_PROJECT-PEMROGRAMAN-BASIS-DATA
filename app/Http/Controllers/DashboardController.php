<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;

class DashboardController extends Controller
{
    public function index() {
        if (!session()->has('iduser')) {
            return redirect('/login');
        }
        $barangs = Barang::with('satuan')->get();
        return view('dashboard.index', compact('barangs'));
    }
}