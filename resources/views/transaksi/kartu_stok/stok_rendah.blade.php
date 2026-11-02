@extends('layouts.app')
@section('content')
<h3>Barang dengan Stok ≤ {{ $batas }}</h3>

<form method="GET" class="mb-3">
    <input type="number" name="batas" value="{{ $batas }}" min="0">
    <button type="submit">Filter</button>
</form>

<table class="table table-bordered">
    <thead>
        <tr><th>ID</th><th>Nama Barang</th><th>Jenis</th><th>Satuan</th><th>Sisa Stok</th></tr>
    </thead>
    <tbody>
    @forelse ($data as $row)
        <tr>
            <td>{{ $row->idbarang }}</td>
            <td>{{ $row->nama_barang }}</td>
            <td>{{ $row->jenis }}</td>
            <td>{{ $row->nama_satuan }}</td>
            <td>{{ $row->sisa_stok }}</td>
        </tr>
    @empty
        <tr><td colspan="5">Tidak ada barang dengan stok rendah.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection