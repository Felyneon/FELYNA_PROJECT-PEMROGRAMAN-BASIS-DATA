@extends('layouts.app')

@section('content')
<h3>Laporan Pengadaan per Vendor</h3>
<p class="text-muted">Data berasal dari Stored Procedure <code>sp_laporan_pengadaan_vendor</code>.</p>

<form method="GET" class="mb-3">
    <select name="idvendor" onchange="this.form.submit()">
        <option value="">-- Pilih Vendor --</option>
        @foreach ($vendors as $v)
            <option value="{{ $v->idvendor }}" @selected($idvendor == $v->idvendor)>{{ $v->nama_vendor }}</option>
        @endforeach
    </select>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tanggal</th>
            <th>Vendor</th>
            <th>Staf Pembuat</th>
            <th>Subtotal</th>
            <th>PPN</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
    @forelse ($data as $r)
        <tr>
            <td>{{ $r->idpengadaan }}</td>
            <td>{{ $r->tanggal_pengadaan }}</td>
            <td>{{ $r->nama_vendor }}</td>
            <td>{{ $r->nama_staff_pembuat }}</td>
            <td>Rp {{ number_format($r->subtotal_nilai, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($r->ppn, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($r->total_nilai, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr><td colspan="7">Pilih vendor, atau vendor ini belum punya pengadaan.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection