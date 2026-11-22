@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:760px">
    @if(session('success')) <div class="alert alert-success d-print-none">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger d-print-none">{{ session('error') }}</div> @endif

    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-0">STRUK PENJUALAN</h4>
            <small class="text-muted">Felyneon Store</small>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <div><span class="text-muted">No. Transaksi</span><br><strong>#{{ $penjualan->idpenjualan }}</strong></div>
                <div class="mt-2"><span class="text-muted">Tanggal</span><br><strong>{{ $penjualan->tanggal_transaksi }}</strong></div>
                <div class="mt-2"><span class="text-muted">Status</span><br>
                    @if($penjualan->status_bayar === 'D') <span class="badge bg-warning text-dark">Draft (belum dibayar)</span>
                    @else <span class="badge bg-success">Dibayar</span> <small class="text-muted">{{ $penjualan->dibayar_at }}</small> @endif
                </div>
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

        <div class="d-print-none d-flex flex-wrap gap-2">
            <a href="{{ url('/transaksi/penjualan') }}" class="btn btn-light">Kembali</a>

            @if($penjualan->status_bayar === 'D')
                @akses('penjualan.bayar')
                    <form action="{{ url('/transaksi/penjualan/'.$penjualan->idpenjualan.'/bayar') }}" method="POST"
                          onsubmit="return confirm('Proses pembayaran? Setelah dibayar, transaksi tidak dapat diubah atau dihapus.')">
                        @csrf <button class="btn btn-success">Bayar</button>
                    </form>
                @endakses
                @akses('penjualan.update')
                    <a href="{{ url('/transaksi/penjualan/'.$penjualan->idpenjualan.'/edit') }}" class="btn btn-warning">Ubah</a>
                @endakses
                @akses('penjualan.delete')
                    <form action="{{ url('/transaksi/penjualan/'.$penjualan->idpenjualan) }}" method="POST"
                          onsubmit="return confirm('Hapus draft ini? Stok akan dikembalikan.')">
                        @csrf @method('DELETE') <button class="btn btn-danger">Hapus Draft</button>
                    </form>
                @endakses
            @else
                <span class="text-muted align-self-center small">Transaksi sudah dibayar: tidak dapat diubah atau dihapus.</span>
            @endif

            <button onclick="window.print()" class="btn btn-primary ms-auto">Cetak</button>
        </div>
    </div></div>
</div>
@endsection
