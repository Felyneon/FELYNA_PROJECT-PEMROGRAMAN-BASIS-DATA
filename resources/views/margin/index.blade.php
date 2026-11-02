@extends('layouts.app')

@section('title', 'Kelola Margin Penjualan')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow p-4 border-0 rounded-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-percent text-primary me-2"></i> Kelola Margin Penjualan</h3>
                <p class="text-muted small mb-0">Atur besaran persentase keuntungan untuk transaksi penjualan.</p>
            </div>
            <a href="/margin/create" class="btn btn-primary fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Tambah Margin Baru
            </a>
        </div>
        
        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Persentase Margin</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($margins as $index => $m)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold text-success fs-5">{{ $m->persen }}%</td>
                        <td>
                            @if($m->status == 1)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>{{ $m->user->username ?? '-' }}</td>
                        <td>
                            <a href="/margin/{{ $m->idmargin_penjualan }}/edit" class="btn btn-warning btn-sm fw-semibold text-dark">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>
                            <form action="/margin/{{ $m->idmargin_penjualan }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus margin ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm fw-semibold">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data margin penjualan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection