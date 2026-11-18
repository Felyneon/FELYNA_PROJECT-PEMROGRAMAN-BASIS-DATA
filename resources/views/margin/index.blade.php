@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0" style="border-radius:16px">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <div>
                    <h3 class="fw-bold">Kelola Margin Penjualan</h3>
                   
                </div>
                <a href="{{ url('/margin/create') }}" class="btn btn-primary align-self-start">+ Tambah Margin Baru</a>
            </div>

            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif
            @if(collect($margins)->where('status', 1)->count() === 0)
                <div class="alert alert-warning">Belum ada margin aktif. Penjualan tidak bisa dibuat sebelum satu margin diaktifkan.</div>
            @endif

            <table class="table align-middle">
                <thead class="table-dark">
                    <tr><th>No</th><th>Persentase Margin</th><th>Status</th><th>Dibuat Oleh</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                @foreach($margins as $i => $m)
                    <tr class="{{ $m->status == 1 ? 'table-success' : '' }}">
                        <td>{{ $i + 1 }}</td>
                        <td class="fw-bold">{{ rtrim(rtrim(number_format($m->persen, 2, '.', ''), '0'), '.') }}%</td>
                        <td>
                            @if($m->status == 1) <span class="badge bg-success">Aktif</span>
                            @else <span class="badge bg-secondary">Nonaktif</span> @endif
                        </td>
                        <td>{{ $m->dibuat_oleh }}</td>
                        <td>
                            <a href="{{ url('/margin/'.$m->idmargin_penjualan.'/edit') }}" class="btn btn-sm btn-warning">Edit</a>
                            @if($m->status == 0)
                                <form action="{{ url('/margin/'.$m->idmargin_penjualan.'/aktifkan') }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Aktifkan margin ini? Margin aktif lainnya akan nonaktif.')">
                                    @csrf <button class="btn btn-sm btn-success">Aktifkan</button>
                                </form>
                                <form action="{{ url('/margin/'.$m->idmargin_penjualan) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus margin ini?')">
                                    @csrf @method('DELETE') <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
