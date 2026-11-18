@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body">
        <h3 class="fw-bold mb-1">Catat Penerimaan Barang</h3>
        <p class="text-muted">Pilih pengadaan yang masih pending. Jika vendor mengubah harga, pilih "Harga berubah" lalu isi harga baru; harga barang akan ikut diperbarui otomatis.</p>

        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

        <form method="GET" action="{{ url('/transaksi/penerimaan/create') }}" class="mb-4">
            <label class="form-label fw-bold">Pengadaan (status Pending)</label>
            <select name="idpengadaan" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih pengadaan --</option>
                @foreach($pengadaans as $p)
                    <option value="{{ $p->idpengadaan }}" @selected(optional($selectedPengadaan)->idpengadaan == $p->idpengadaan)>
                        #{{ $p->idpengadaan }} - {{ $p->nama_vendor }} - {{ $p->tanggal_pengadaan }} ({{ $p->total_jenis_barang }} jenis, {{ $p->total_qty }} pcs)
                    </option>
                @endforeach
            </select>
        </form>

        @if($selectedPengadaan)
        <form method="POST" action="{{ url('/transaksi/penerimaan') }}">
            @csrf
            <input type="hidden" name="idpengadaan" value="{{ $selectedPengadaan->idpengadaan }}">
            <div class="mb-3"><strong>Vendor:</strong> {{ $selectedPengadaan->nama_vendor }}</div>

            <table class="table align-middle">
                <thead class="table-dark">
                    <tr><th>Barang</th><th class="text-end">Qty Dipesan</th><th class="text-end">Harga Pengadaan</th>
                        <th width="130">Jumlah Diterima</th><th width="190">Harga</th><th width="170">Harga Terima</th></tr>
                </thead>
                <tbody>
                @foreach($detailPengadaans as $d)
                    <tr data-harga="{{ $d->harga_satuan }}">
                        <td>{{ $d->nama_barang }}
                            <input type="hidden" name="idbarang[]" value="{{ $d->idbarang }}"></td>
                        <td class="text-end">{{ $d->jumlah }} {{ $d->nama_satuan }}</td>
                        <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                        <td><input type="number" name="jumlah_terima[]" class="form-control" min="0" value="{{ $d->jumlah }}" required></td>
                        <td>
                            <select class="form-select pilihan">
                                <option value="sama">Sesuai harga pengadaan</option>
                                <option value="baru">Harga berubah (input)</option>
                            </select>
                        </td>
                        <td><input type="number" name="harga_satuan_terima[]" class="form-control hrg" min="0" value="{{ $d->harga_satuan }}" readonly required></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button class="btn btn-warning">Simpan Penerimaan</button>
            <a href="{{ url('/transaksi/penerimaan') }}" class="btn btn-light">Batal</a>
        </form>
        <script>
        document.querySelectorAll('tr[data-harga]').forEach(tr => {
            const sel = tr.querySelector('.pilihan'), inp = tr.querySelector('.hrg');
            sel.addEventListener('change', () => {
                if (sel.value === 'sama') { inp.value = tr.dataset.harga; inp.readOnly = true; }
                else { inp.readOnly = false; inp.focus(); }
            });
        });
        </script>
        @endif
    </div></div>
</div>
@endsection
