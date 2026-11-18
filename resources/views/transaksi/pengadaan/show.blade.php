@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:850px">
    @if(session('success')) <div class="alert alert-success d-print-none">{{ session('success') }}</div> @endif
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-0">NOTA PENGADAAN BARANG</h4>
            <small class="text-muted">Felyneon Store</small>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <div><span class="text-muted">No. Pengadaan</span><br><strong>#{{ $pengadaan->idpengadaan }}</strong></div>
                <div class="mt-2"><span class="text-muted">Tanggal</span><br><strong>{{ $pengadaan->tanggal_pengadaan }}</strong></div>
                <div class="mt-2"><span class="text-muted">Dibuat oleh</span><br><strong>{{ $pengadaan->nama_pembuat }}</strong></div>
            </div>
            <div class="col-6">
                <div><span class="text-muted">Vendor</span><br><strong>{{ $pengadaan->nama_vendor }}</strong><br><small>{{ $pengadaan->badan_hukum }}</small></div>
                <div class="mt-2"><span class="text-muted">Status</span><br>
                    @if($pengadaan->status == 'S') <span class="badge bg-success">Selesai (sudah diterima)</span>
                    @else <span class="badge bg-warning text-dark">Pending (menunggu penerimaan)</span> @endif
                </div>
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
                    <td class="text-end">Rp {{ number_format($d->sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end">Rp {{ number_format($pengadaan->subtotal_nilai, 0, ',', '.') }}</td></tr>
                <tr><td colspan="4" class="text-end">PPN (11%)</td><td class="text-end">Rp {{ number_format($pengadaan->ppn, 0, ',', '.') }}</td></tr>
                <tr class="fw-bold"><td colspan="4" class="text-end">TOTAL</td><td class="text-end">Rp {{ number_format($pengadaan->total_nilai, 0, ',', '.') }}</td></tr>
            </tfoot>
        </table>

        <div class="d-print-none">
            <a href="{{ url('/transaksi/pengadaan') }}" class="btn btn-light">Kembali</a>
            <button onclick="window.print()" class="btn btn-primary">Cetak</button>
        </div>
    </div></div>
</div>
@endsection
