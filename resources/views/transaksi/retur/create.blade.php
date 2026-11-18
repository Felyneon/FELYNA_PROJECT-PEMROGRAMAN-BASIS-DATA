@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body">
        <h3 class="fw-bold mb-3">Catat Retur Baru</h3>

        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

        <form method="GET" action="{{ url('/transaksi/retur/create') }}" class="mb-4">
            <label class="form-label fw-bold">Penerimaan</label>
            <select name="idpenerimaan" class="form-select" onchange="this.form.submit()">
                <option value="">-- Pilih penerimaan --</option>
                @foreach($penerimaans as $p)
                    <option value="{{ $p->idpenerimaan }}" @selected(optional($selectedPenerimaan)->idpenerimaan == $p->idpenerimaan)>
                        #{{ $p->idpenerimaan }} - {{ $p->nama_vendor }} - {{ $p->tanggal_penerimaan }}
                    </option>
                @endforeach
            </select>
        </form>

        @if($selectedPenerimaan)
            @if(count($details) === 0)
                <div class="alert alert-info">Semua barang pada penerimaan ini sudah diretur.</div>
            @else
            <form method="POST" action="{{ url('/transaksi/retur') }}">
                @csrf
                <input type="hidden" name="idpenerimaan" value="{{ $selectedPenerimaan->idpenerimaan }}">
                <div class="mb-3">
                    <label class="form-label fw-bold">Barang</label>
                    <select name="iddetail_penerimaan" id="detail" class="form-select" required>
                        @foreach($details as $d)
                            <option value="{{ $d->iddetail_penerimaan }}" data-max="{{ $d->sisa_bisa_retur }}">
                                {{ $d->nama_barang }} (diterima {{ $d->jumlah_terima }}, sisa bisa diretur {{ $d->sisa_bisa_retur }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Jumlah retur</label>
                    <input type="number" name="jumlah" id="jml" class="form-control" min="1" value="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Alasan</label>
                    <input type="text" name="alasan" class="form-control" required>
                </div>
                <button class="btn btn-danger">Simpan Retur</button>
                <a href="{{ url('/transaksi/retur') }}" class="btn btn-light">Batal</a>
            </form>
            <script>
            // VALIDASI JUMLAH RETUR DI FRONTEND (bukan di database)
            const d = document.getElementById('detail'), j = document.getElementById('jml');
            function batas() { j.max = d.selectedOptions[0].dataset.max; if (Number(j.value) > Number(j.max)) j.value = j.max; }
            d.addEventListener('change', batas); j.addEventListener('input', batas); batas();
            </script>
            @endif
        @endif
    </div></div>
</div>
@endsection
