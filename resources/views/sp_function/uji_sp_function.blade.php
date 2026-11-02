<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uji Stored Procedure dan Function</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

    <h2 class="mb-1">Uji Stored Procedure dan Function</h2>
    <p class="text-muted">
        Database <code>db_project_store</code>. Semua hasil di bawah dihitung langsung oleh
        SP/Function MySQL, bukan oleh PHP.
    </p>

    {{-- ============ FORM PARAMETER ============ --}}
    <form method="GET" action="{{ url('/uji-sp-function') }}" class="card card-body mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Kasir / User (SP laporan penjualan)</label>
                <select name="iduser" class="form-select">
                    @foreach ($users as $u)
                        <option value="{{ $u->iduser }}" @selected($iduser == $u->iduser)>
                            {{ $u->iduser }} - {{ $u->username }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Vendor (SP laporan pengadaan)</label>
                <select name="idvendor" class="form-select">
                    @foreach ($vendors as $v)
                        <option value="{{ $v->idvendor }}" @selected($idvendor == $v->idvendor)>
                            {{ $v->idvendor }} - {{ $v->nama_vendor }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Barang (riwayat kartu stok dan harga jual)</label>
                <select name="idbarang" class="form-select">
                    @foreach ($barangs as $b)
                        <option value="{{ $b->idbarang }}" @selected($idbarang == $b->idbarang)>
                            {{ $b->idbarang }} - {{ $b->nama }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Margin penjualan (fn harga jual)</label>
                <select name="idmargin" class="form-select">
                    @foreach ($margins as $m)
                        <option value="{{ $m->idmargin_penjualan }}" @selected($idmargin == $m->idmargin_penjualan)>
                            {{ $m->idmargin_penjualan }} - {{ $m->persen }}%
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Batas stok rendah (SP stok rendah)</label>
                <input type="number" name="batas" min="0" value="{{ $batas }}" class="form-control">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Jalankan ulang</button>
            </div>
        </div>
    </form>

    {{-- ============ HASIL SP DAN FUNCTION ============ --}}
    @foreach ($hasil as $h)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $h['nama'] }}</strong>
                <span class="badge {{ $h['jenis'] === 'FUNCTION' ? 'bg-success' : 'bg-primary' }}">
                    {{ $h['jenis'] }}
                </span>
            </div>
            <div class="card-body">
                <p class="mb-1 text-muted">{{ $h['info'] }}</p>
                <code>{{ $h['sql'] }}</code>

                @if (count($h['rows']) > 0)
                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-bordered table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    @foreach ((array) $h['rows'][0] as $kolom => $nilai)
                                        <th>{{ $kolom }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($h['rows'] as $row)
                                    <tr>
                                        @foreach ((array) $row as $nilai)
                                            <td>{{ $nilai }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted">{{ count($h['rows']) }} baris dikembalikan.</small>
                @else
                    <div class="alert alert-warning mt-3 mb-0">
                        Berhasil dijalankan, tetapi tidak ada baris yang cocok dengan parameter ini.
                    </div>
                @endif
            </div>
        </div>
    @endforeach

</div>
</body>
</html>