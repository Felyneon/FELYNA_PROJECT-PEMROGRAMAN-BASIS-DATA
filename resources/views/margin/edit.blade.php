@extends('layouts.app')

@section('title', 'Edit Margin Penjualan')

@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0 rounded-4" style="max-width: 600px; margin: auto;">
        <h3 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit Margin Penjualan</h3>
        
        <form action="/margin/{{ $margin->idmargin_penjualan }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Persen Margin (%)</label>
                <input type="number" step="0.01" name="persen" value="{{ $margin->persen }}" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Status</label>
                <select name="status" class="form-select" required>
                    <option value="1" {{ $margin->status == 1 ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ $margin->status == 0 ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                </button>
                <a href="/margin" class="btn btn-secondary px-4 fw-semibold">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection