@extends('layouts.app')

@section('content')
<h3>Laporan Penjualan per Kasir</h3>
<p class="text-muted">Data berasal dari Stored Procedure <code>sp_laporan_penjualan_berdasarkan_user</code>.</p>

<form method="GET" class="mb-3">
    <select name="iduser" onchange="this.form.submit()">
        <option value="">-- Pilih Kasir --</option>
        @foreach ($users as $u)
            <option value="{{ $u->iduser }}" @selected($iduser == $u->iduser)>{{ $u->username }}</option>
        @endforeach
    </select>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Kasir</th>
            <th>Subtotal</th>
            <th>PPN</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($data as $r)
        <tr>
            <td>{{ $r->idpenjualan }}</td>
            <td>{{ $r->tanggal_transaksi }}</td>
            <td>{{ $r->nama_kasir }}</td>
            <td>Rp {{ number_format($r->subtotal_nilai, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($r->ppn, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($r->total_nilai, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="6">Pilih kasir, atau kasir ini belum punya transaksi.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection