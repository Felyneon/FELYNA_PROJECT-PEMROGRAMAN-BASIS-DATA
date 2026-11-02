@extends('layouts.app')
@section('content')
<h3>Riwayat Kartu Stok</h3>

<form method="GET" class="mb-3">
    <select name="idbarang" onchange="this.form.submit()">
        <option value="">-- Pilih Barang --</option>
        @foreach ($barang as $b)
            <option value="{{ $b->idbarang }}" {{ $idbarang == $b->idbarang ? 'selected' : '' }}>
                {{ $b->nama }}
            </option>
        @endforeach
    </select>
</form>

<table class="table table-bordered">
    <thead>
        <tr><th>ID</th><th>Barang</th><th>Keterangan</th><th>Masuk</th><th>Keluar</th><th>Stok Berjalan</th><th>Waktu</th><th>ID Transaksi</th></tr>
    </thead>
    <tbody>
    @forelse ($riwayat as $r)
        <tr>
            <td>{{ $r->idkartu_stok }}</td>
            <td>{{ $r->nama_barang }}</td>
            <td>{{ $r->ket_transaksi }}</td>
            <td>{{ $r->masuk }}</td>
            <td>{{ $r->keluar }}</td>
            <td>{{ $r->stok_berjalan }}</td>
            <td>{{ $r->waktu_mutasi }}</td>
            <td>{{ $r->idtransaksi }}</td>
        </tr>
    @empty
        <tr><td colspan="8">Pilih barang atau belum ada mutasi.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection