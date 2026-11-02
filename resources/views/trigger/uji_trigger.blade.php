<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Uji SP, Function, dan Trigger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">

    <h2 class="mb-1">Uji SP, Function, dan Trigger</h2>
    <p class="text-muted">
        <!--Halaman ini memeriksa apakah objek database benar-benar aktif, dan menjalankan uji trigger
        yang <strong>otomatis dibatalkan (rollback)</strong>, sehingga data aslimu tidak berubah. -->
    </p>

    {{-- ============ 1. STATUS OBJEK DATABASE ============ --}}
    <h4 class="mt-4">1. Status objek di database</h4>
    <div class="row g-3 mb-3">
        @foreach (['PROCEDURE' => 'Stored Procedure', 'FUNCTION' => 'Function', 'TRIGGER' => 'Trigger'] as $kunci => $label)
            @php
                $ada = $jumlah[$kunci];
                $tgt = $target[$kunci];
                $kelas = $ada === $tgt ? 'bg-success' : ($ada === 0 ? 'bg-danger' : 'bg-warning text-dark');
            @endphp
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <div class="text-muted">{{ $label }}</div>
                        <div class="display-5">{{ $ada }} <small class="fs-6 text-muted">/ {{ $tgt }}</small></div>
                        <span class="badge {{ $kelas }}">
                            {{ $ada === $tgt ? 'AKTIF' : ($ada === 0 ? 'BELUM ADA' : 'TIDAK LENGKAP') }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <details class="mb-2">
        <summary>Lihat daftar Stored Procedure dan Function</summary>
        <ul class="mt-2">
            @foreach ($routines as $r)
                <li><span class="badge {{ $r->jenis === 'FUNCTION' ? 'bg-success' : 'bg-primary' }}">{{ $r->jenis }}</span> {{ $r->nama }}</li>
            @endforeach
        </ul>
    </details>
    <details class="mb-4">
        <summary>Lihat daftar Trigger</summary>
        <div class="table-responsive mt-2">
            <table class="table table-sm table-bordered bg-white">
                <thead class="table-dark"><tr><th>Trigger</th><th>Tabel</th><th>Waktu</th><th>Aksi</th></tr></thead>
                <tbody>
                @foreach ($triggers as $t)
                    <tr><td>{{ $t->nama }}</td><td>{{ $t->tabel }}</td><td>{{ $t->waktu }}</td><td>{{ $t->aksi }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </details>

    {{-- ============ 2. UJI OTOMATIS TRIGGER ============ --}}
    <h4>2. Uji otomatis trigger</h4>
    <form method="POST" action="{{ url('/uji-trigger') }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-primary">Jalankan uji trigger (otomatis di-rollback)</button>
    </form>

    @if ($pesanSiap)
        <div class="alert alert-danger">{{ $pesanSiap }}</div>
    @endif

    @if ($hasilUji !== null)
        @php
            $totalUji = count($hasilUji);
            $lulusUji = collect($hasilUji)->where('lulus', true)->count();
        @endphp
        <div class="alert {{ $lulusUji === $totalUji ? 'alert-success' : 'alert-warning' }}">
            <strong>{{ $lulusUji }} dari {{ $totalUji }} pemeriksaan lulus.</strong>
            @if ($lulusUji === $totalUji)
                Semua trigger bekerja dan seluruh data uji sudah dibatalkan.
            @else
                Ada pemeriksaan yang gagal, lihat kolom Hasil aktual dan Harapan di bawah.
            @endif
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered bg-white align-middle">
                <thead class="table-dark">
                    <tr><th>Langkah</th><th>Trigger / Function</th><th>Hasil aktual</th><th>Harapan</th><th>Status</th></tr>
                </thead>
                <tbody>
                @foreach ($hasilUji as $h)
                    <tr>
                        <td>{{ $h['langkah'] }}</td>
                        <td><code>{{ $h['trigger'] }}</code></td>
                        <td>{{ $h['aktual'] }}</td>
                        <td>{{ $h['harapan'] }}</td>
                        <td>
                            @if ($h['lulus'])
                                <span class="badge bg-success">LULUS</span>
                            @else
                                <span class="badge bg-danger">GAGAL</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <h6>Baris kartu stok yang dibuat otomatis oleh trigger selama uji (sudah dibatalkan)</h6>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-bordered bg-white">
                <thead class="table-secondary">
                    <tr><th>ID</th><th>ID Barang</th><th>Jenis</th><th>Masuk</th><th>Keluar</th><th>Stok</th><th>ID Transaksi</th></tr>
                </thead>
                <tbody>
                @forelse ($kartuBaru as $k)
                    <tr>
                        <td>{{ $k->idkartu_stok }}</td><td>{{ $k->idbarang }}</td><td>{{ $k->jenis_transaksi }}</td>
                        <td>{{ $k->masuk }}</td><td>{{ $k->keluar }}</td><td>{{ $k->stock }}</td><td>{{ $k->idtransaksi }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">Tidak ada.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <h6>Baris log_aktivitas yang dibuat trigger selama uji (sudah dibatalkan)</h6>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-bordered bg-white">
                <thead class="table-secondary">
                    <tr><th>ID</th><th>Tabel</th><th>Aksi</th><th>Data lama</th><th>Data baru</th></tr>
                </thead>
                <tbody>
                @forelse ($logBaru as $l)
                    <tr>
                        <td>{{ $l->idlog }}</td><td>{{ $l->nama_tabel }}</td><td>{{ $l->jenis_aksi }}</td>
                        <td>{{ $l->data_lama }}</td><td>{{ $l->data_baru }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Tidak ada.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif

    {{-- ============ 3. DATA NYATA TERBARU ============ --}}
    <h4 class="mt-4">3. trigger dari transaksi </h4>
    <p class="text-muted">
        Buat transaksi lewat browser (pengadaan, penerimaan, penjualan, retur, atau ubah harga barang),
        <!--lalu muat ulang halaman ini untuk melihat hasil kerja trigger.-->
    </p>

    <h6>10 baris kartu stok terakhir</h6>
    <div class="table-responsive mb-4">
        <table class="table table-sm table-bordered bg-white">
            <thead class="table-dark">
                <tr><th>ID</th><th>Barang</th><th>Jenis</th><th>Masuk</th><th>Keluar</th><th>Stok</th><th>ID Transaksi</th><th>Waktu</th></tr>
            </thead>
            <tbody>
            @forelse ($kartuTerakhir as $k)
                <tr>
                    <td>{{ $k->idkartu_stok }}</td><td>{{ $k->nama }}</td><td>{{ $k->jenis_transaksi }}</td>
                    <td>{{ $k->masuk }}</td><td>{{ $k->keluar }}</td><td>{{ $k->stock }}</td>
                    <td>{{ $k->idtransaksi }}</td><td>{{ $k->created_at }}</td>
                </tr>
            @empty
                <tr><td colspan="8">Belum ada kartu stok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <h6>10 log perubahan harga terakhir</h6>
    <div class="table-responsive mb-4">
        <table class="table table-sm table-bordered bg-white">
            <thead class="table-dark"><tr><th>ID</th><th>ID Barang</th><th>Harga lama</th><th>Harga baru</th><th>Waktu</th></tr></thead>
            <tbody>
            @forelse ($logHarga as $l)
                <tr>
                    <td>{{ $l->idlog }}</td><td>{{ $l->idbarang }}</td>
                    <td>Rp {{ number_format($l->harga_lama, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($l->harga_baru, 0, ',', '.') }}</td>
                    <td>{{ $l->waktu_ubah }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada perubahan harga.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <h6>10 log aktivitas terakhir (update/hapus barang dan penjualan)</h6>
    <div class="table-responsive mb-4">
        <table class="table table-sm table-bordered bg-white">
            <thead class="table-dark"><tr><th>ID</th><th>Tabel</th><th>Aksi</th><th>Data lama</th><th>Data baru</th><th>Waktu</th></tr></thead>
            <tbody>
            @forelse ($logAktivitas as $l)
                <tr>
                    <td>{{ $l->idlog }}</td><td>{{ $l->nama_tabel }}</td><td>{{ $l->jenis_aksi }}</td>
                    <td>{{ $l->data_lama }}</td><td>{{ $l->data_baru }}</td><td>{{ $l->waktu }}</td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada log aktivitas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

</div>
</body>
</html>