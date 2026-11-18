@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body">
        <h3 class="fw-bold mb-1">Transaksi Baru (POS)</h3>

        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

        @if(!$margin)
            <div class="alert alert-warning">Belum ada margin aktif. Aktifkan satu margin di menu <strong>Kelola Margin</strong> terlebih dahulu.</div>
        @else
            <p class="text-muted">Margin aktif: <strong>{{ rtrim(rtrim(number_format($margin->persen, 2, '.', ''), '0'), '.') }}%</strong> (otomatis). Harga jual dan subtotal final dihitung database.</p>

            <form method="POST" action="{{ url('/transaksi/penjualan') }}">
                @csrf
                <table class="table align-middle" id="tbl">
                    <thead class="table-dark"><tr><th>Barang</th><th width="110">Stok</th><th width="120">Jumlah</th><th width="160">Harga Jual</th><th width="170">Subtotal</th><th width="60"></th></tr></thead>
                    <tbody></tbody>
                </table>
                <button type="button" id="addRow" class="btn btn-outline-primary mb-3">+ Tambah Barang</button>

                <div class="text-end">
                    <div>Subtotal: <strong id="eSub">Rp 0</strong></div>
                    <div>PPN 11%: <strong id="ePpn">Rp 0</strong></div>
                    <div class="fs-5">Total: <strong id="eTot">Rp 0</strong></div>
                </div>
                <div class="mt-3"><button class="btn btn-primary">Simpan Transaksi</button>
                    <a href="{{ url('/transaksi/penjualan') }}" class="btn btn-light">Batal</a></div>
            </form>

            <template id="rowTpl">
                <tr>
                    <td>
                        <select name="idbarang[]" class="form-select brg" required>
                            <option value="">-- Pilih barang --</option>
                            @foreach($barangs as $b)
                                <option value="{{ $b->idbarang }}" data-harga="{{ $b->harga_dasar }}" data-stok="{{ $b->sisa_stok }}" @disabled($b->sisa_stok <= 0)>
                                    {{ $b->nama_barang }} @if($b->sisa_stok <= 0) (habis) @endif
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="stok">-</td>
                    <td><input type="number" name="jumlah[]" class="form-control qty" min="1" value="1" required></td>
                    <td class="hj">Rp 0</td>
                    <td class="sub">Rp 0</td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger del">&times;</button></td>
                </tr>
            </template>

            <script>
            const PERSEN = {{ (float) $margin->persen }};
            const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
            const body = document.querySelector('#tbl tbody');
            function hitung() {
                let sub = 0;
                body.querySelectorAll('tr').forEach(tr => {
                    const opt = tr.querySelector('.brg').selectedOptions[0];
                    const dasar = Number(opt?.dataset.harga || 0), stok = Number(opt?.dataset.stok || 0);
                    const qtyEl = tr.querySelector('.qty');
                    // VALIDASI STOK DI FRONTEND (bukan di database)
                    if (opt && opt.value) { qtyEl.max = stok; if (Number(qtyEl.value) > stok) qtyEl.value = stok; }
                    const hj = Math.round(dasar + dasar * PERSEN / 100), q = Number(qtyEl.value || 0);
                    tr.querySelector('.stok').textContent = opt && opt.value ? stok : '-';
                    tr.querySelector('.hj').textContent = rp(hj);
                    tr.querySelector('.sub').textContent = rp(hj * q);
                    sub += hj * q;
                });
                const ppn = Math.round(sub * 0.11);
                eSub.textContent = rp(sub); ePpn.textContent = rp(ppn); eTot.textContent = rp(sub + ppn);
            }
            function tambah() { body.appendChild(document.getElementById('rowTpl').content.cloneNode(true)); hitung(); }
            document.getElementById('addRow').onclick = tambah;
            body.addEventListener('input', hitung);
            body.addEventListener('click', e => { if (e.target.classList.contains('del') && body.rows.length > 1) { e.target.closest('tr').remove(); hitung(); } });
            tambah();
            </script>
        @endif
    </div></div>
</div>
@endsection
