@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:760px">
    @if(session('success')) <div class="alert alert-success d-print-none">{{ session('success') }}</div> @endif
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-0">STRUK PENJUALAN</h4>
            <small class="text-muted">Felyneon Store</small>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <div><span class="text-muted">No. Transaksi</span><br><strong>#{{ $penjualan->idpenjualan }}</strong></div>
                <div class="mt-2"><span class="text-muted">Tanggal</span><br><strong>{{ $penjualan->tanggal_transaksi }}</strong></div>
            </div>
            <div class="col-6">
                <div><span class="text-muted">Kasir</span><br><strong>{{ $penjualan->nama_lengkap_kasir ?? $penjualan->nama_kasir }}</strong> <small>({{ $penjualan->nama_kasir }})</small></div>
                <div class="mt-2"><span class="text-muted">Margin</span><br><strong>{{ rtrim(rtrim(number_format($penjualan->persen_margin, 2, '.', ''), '0'), '.') }}%</strong></div>
            </div>
        </div>

        <table class="table table-bordered align-middle">
            <thead class="table-dark"><tr><th>No</th><th>Nama Barang</th><th class="text-end">Qty</th><th class="text-end">Harga Satuan</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach($details as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $d->nama_barang }}</td>
                    <td class="text-end">{{ $d->jumlah }} {{ $d->nama_satuan }}</td>
                    <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end">Rp {{ number_format($penjualan->subtotal_nilai, 0, ',', '.') }}</td></tr>
                <tr><td colspan="4" class="text-end">PPN (11%)</td><td class="text-end">Rp {{ number_format($penjualan->ppn, 0, ',', '.') }}</td></tr>
                <tr class="fw-bold"><td colspan="4" class="text-end">TOTAL BAYAR</td><td class="text-end">Rp {{ number_format($penjualan->total_nilai, 0, ',', '.') }}</td></tr>
            </tfoot>
        </table>

        <div class="d-print-none">
            <a href="{{ url('/transaksi/penjualan') }}" class="btn btn-light">Kembali</a>
            <button onclick="window.print()" class="btn btn-primary">Cetak</button>
        </div>
    </div></div>
</div>
@endsection
