@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body">
        <h3 class="fw-bold mb-1">Buat Pengadaan Baru</h3>
        <p class="text-muted">Pilih vendor, lalu tambahkan barang (boleh lebih dari satu). Harga mengikuti harga barang; subtotal, PPN, dan total dihitung otomatis oleh database.</p>

        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

        <form method="POST" action="{{ url('/transaksi/pengadaan') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Vendor</label>
                <select name="vendor_idvendor" class="form-select" required>
                    <option value="">-- Pilih vendor --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->idvendor }}" @selected(old('vendor_idvendor') == $v->idvendor)>
                            {{ $v->nama_vendor }} ({{ $v->keterangan_badan_hukum }})
                        </option>
                    @endforeach
                </select>
            </div>

            <table class="table align-middle" id="tbl">
                <thead class="table-dark"><tr><th>Barang</th><th width="120">Jumlah</th><th width="160">Harga (otomatis)</th><th width="170">Estimasi Subtotal</th><th width="60"></th></tr></thead>
                <tbody></tbody>
            </table>
            <button type="button" id="addRow" class="btn btn-outline-primary mb-3">+ Tambah Barang</button>

            <div class="text-end">
                <div>Estimasi subtotal: <strong id="estSub">Rp 0</strong></div>
                <small class="text-muted">PPN & total final dihitung otomatis</small>
            </div>
            <div class="mt-3"><button class="btn btn-success">Simpan Pengadaan</button>
                <a href="{{ url('/transaksi/pengadaan') }}" class="btn btn-light">Batal</a></div>
        </form>
    </div></div>
</div>

<template id="rowTpl">
    <tr>
        <td>
            <select name="idbarang[]" class="form-select brg" required>
                <option value="">-- Pilih barang --</option>
                @foreach($barangs as $b)
                    <option value="{{ $b->idbarang }}" data-harga="{{ $b->harga_dasar }}">{{ $b->nama_barang }} ({{ $b->nama_satuan }})</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="jumlah[]" class="form-control qty" min="1" value="1" required></td>
        <td class="harga">Rp 0</td>
        <td class="sub">Rp 0</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger del">&times;</button></td>
    </tr>
</template>

<script>
const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
const body = document.querySelector('#tbl tbody');
function hitung() {
    let total = 0;
    body.querySelectorAll('tr').forEach(tr => {
        const h = Number(tr.querySelector('.brg').selectedOptions[0]?.dataset.harga || 0);
        const q = Number(tr.querySelector('.qty').value || 0);
        tr.querySelector('.harga').textContent = rp(h);
        tr.querySelector('.sub').textContent = rp(h * q);
        total += h * q;
    });
    document.getElementById('estSub').textContent = rp(total);
}
function tambah() { body.appendChild(document.getElementById('rowTpl').content.cloneNode(true)); hitung(); }
document.getElementById('addRow').onclick = tambah;
body.addEventListener('input', hitung);
body.addEventListener('click', e => { if (e.target.classList.contains('del') && body.rows.length > 1) { e.target.closest('tr').remove(); hitung(); } });
tambah();
</script>
@endsection
