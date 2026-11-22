@extends('layouts.app')

@section('title', 'Data Penjualan Kasir')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h3 class="fw-bold">Data Penjualan Kasir (POS)</h3>
                <p class="text-muted mb-0">
                    @akses('penjualan.lihat_semua') Seluruh transaksi penjualan. @else Transaksi penjualan milik akun Anda. @endakses
                </p>
            </div>
            @akses('penjualan.create')
                <a href="{{ url('/transaksi/penjualan/create') }}" class="btn btn-primary align-self-start">+ Transaksi Baru (POS)</a>
            @endakses
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr><th>No</th><th>ID Penjualan</th><th>Tanggal Transaksi</th><th>Kasir</th><th>Status</th>
                    <th class="text-end">Subtotal</th><th class="text-end">PPN (11%)</th><th class="text-end">Total Nilai</th><th class="text-center">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($penjualans as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>#{{ $p->idpenjualan }}</td>
                    <td>{{ $p->tanggal_transaksi }}</td>
                    <td>({{ $p->nama_kasir }})</td>
                    <td>
                        @if($p->status_bayar === 'D') <span class="badge bg-warning text-dark">Draft (belum dibayar)</span>
                        @else <span class="badge bg-success">Dibayar</span> @endif
                    </td>
                    <td class="text-end">Rp {{ number_format($p->subtotal_nilai, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($p->ppn, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold text-success">Rp {{ number_format($p->total_nilai, 0, ',', '.') }}</td>
                    <td class="text-center text-nowrap">
                        <a href="{{ url('/transaksi/penjualan/'.$p->idpenjualan) }}" class="btn btn-sm btn-info text-white">Detail</a>
                        @if($p->status_bayar === 'D')
                            @akses('penjualan.update')
                                <a href="{{ url('/transaksi/penjualan/'.$p->idpenjualan.'/edit') }}" class="btn btn-sm btn-warning">Ubah</a>
                            @endakses
                            @akses('penjualan.delete')
                                <form action="{{ url('/transaksi/penjualan/'.$p->idpenjualan) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus draft transaksi ini? Stok akan dikembalikan.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            @endakses
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted">Belum ada transaksi penjualan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
