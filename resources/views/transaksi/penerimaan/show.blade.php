@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:850px">
    @if(session('success')) <div class="alert alert-success d-print-none">{{ session('success') }}</div> @endif
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-0">NOTA PENERIMAAN BARANG</h4>
            <small class="text-muted">Felyneon Store</small>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <div><span class="text-muted">No. Penerimaan</span><br><strong>#{{ $penerimaan->idpenerimaan }}</strong></div>
                <div class="mt-2"><span class="text-muted">Tanggal diterima</span><br><strong>{{ $penerimaan->tanggal_penerimaan }}</strong></div>
                <div class="mt-2"><span class="text-muted">Staff penerima</span><br><strong>{{ $penerimaan->nama_staff_penerima }}</strong></div>
            </div>
            <div class="col-6">
                <div><span class="text-muted">Vendor</span><br><strong>{{ $penerimaan->nama_vendor }}</strong></div>
                <div class="mt-2"><span class="text-muted">Dari pengadaan</span><br>
                    <a href="{{ url('/transaksi/pengadaan/'.$penerimaan->idpengadaan) }}"><strong>#{{ $penerimaan->idpengadaan }}</strong></a></div>
            </div>
        </div>

        <table class="table table-bordered align-middle">
            <thead class="table-dark"><tr><th>No</th><th>Nama Barang</th><th class="text-end">Qty Diterima</th><th class="text-end">Harga Satuan</th><th class="text-end">Subtotal</th></tr></thead>
            <tbody>
            @foreach($details as $i => $d)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $d->nama_barang }}</td>
                    <td class="text-end">{{ $d->jumlah_terima }} {{ $d->nama_satuan }}</td>
                    <td class="text-end">Rp {{ number_format($d->harga_satuan_terima, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($d->sub_total_terima, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold"><td colspan="4" class="text-end">TOTAL NILAI PENERIMAAN</td>
                    <td class="text-end">Rp {{ number_format($penerimaan->total_nilai_penerimaan, 0, ',', '.') }}</td></tr>
            </tfoot>
        </table>

        <div class="d-print-none">
            <a href="{{ url('/transaksi/penerimaan') }}" class="btn btn-light">Kembali</a>
            <button onclick="window.print()" class="btn btn-primary">Cetak</button>
        </div>
    </div></div>
</div>
@endsection
