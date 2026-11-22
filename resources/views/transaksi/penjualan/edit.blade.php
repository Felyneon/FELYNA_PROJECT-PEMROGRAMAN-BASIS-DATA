@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width:850px">
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body">
        <h3 class="fw-bold mb-1">Ubah Draft Transaksi #{{ $penjualan->idpenjualan }}</h3>
        <p class="text-muted">Ubah jumlah barang. Isi <strong>0</strong> untuk mengeluarkan barang dari transaksi. Hanya bisa selama transaksi belum dibayar.</p>

        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

        <form method="POST" action="{{ url('/transaksi/penjualan/'.$penjualan->idpenjualan) }}">
            @csrf @method('PUT')
            <table class="table align-middle" id="tbl">
                <thead class="table-dark">
                    <tr><th>Barang</th><th class="text-end">Harga Satuan</th><th width="140">Jumlah</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>
                @foreach($details as $d)
                    <tr data-harga="{{ $d->harga_satuan }}">
                        <td>{{ $d->nama_barang }}
                            <input type="hidden" name="idbarang[]" value="{{ $d->idbarang }}">
                            <div class="small text-muted">Stok tersedia untuk ditambah: {{ $d->sisa_stok }}</div></td>
                        <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                        <td><input type="number" name="jumlah[]" class="form-control qty" min="0"
                                   max="{{ $d->jumlah + $d->sisa_stok }}" value="{{ $d->jumlah }}" required></td>
                        <td class="text-end sub">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-end mb-3">Estimasi subtotal: <strong id="est">Rp 0</strong>
                <div class="small text-muted">PPN dan total final dihitung database.</div></div>

            <button class="btn btn-warning">Simpan Perubahan</button>
            <a href="{{ url('/transaksi/penjualan/'.$penjualan->idpenjualan) }}" class="btn btn-light">Batal</a>
        </form>
    </div></div>
</div>

<script>
const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
function hitung() {
    let total = 0;
    document.querySelectorAll('#tbl tbody tr').forEach(tr => {
        const q = Number(tr.querySelector('.qty').value || 0), h = Number(tr.dataset.harga);
        tr.querySelector('.sub').textContent = rp(h * q);
        total += h * q;
    });
    document.getElementById('est').textContent = rp(total);
}
document.querySelector('#tbl').addEventListener('input', hitung);
hitung();
</script>
@endsection
