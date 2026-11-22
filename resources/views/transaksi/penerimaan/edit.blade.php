
@extends('layouts.app')

@section('title', 'Edit Penerimaan')

@section('content')

<div class="container py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i>
                Edit Penerimaan
            </h3>
            <p class="text-muted mb-0">
                Perbarui jumlah dan harga barang yang telah diterima.
            </p>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali input berikut:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card card-custom">
        <div class="card-header bg-white py-3">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">ID Penerimaan</small>
                    <h5 class="fw-bold mb-0">
                        #{{ $penerimaan->idpenerimaan }}
                    </h5>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">ID Pengadaan</small>
                    <h5 class="fw-bold mb-0">
                        #{{ $penerimaan->idpengadaan }}
                    </h5>
                </div>
            </div>
        </div>

        <div class="card-body p-4">

            <form
                method="POST"
                action="{{ url('/transaksi/penerimaan/' . $penerimaan->idpenerimaan) }}"
            >
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Barang</th>
                                <th style="min-width:160px">Jumlah Diterima</th>
                                <th style="min-width:180px">Harga Satuan (Rp)</th>
                                <th class="text-end">Subtotal Saat Ini</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($details as $index => $detail)
                                <tr>
                                    <td class="text-center">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="fw-semibold">
                                        {{ $detail->nama_barang }}
                                    </td>

                                    <td>
                                        <input
                                            type="number"
                                            class="form-control"
                                            name="detail[{{ $detail->iddetail_penerimaan }}][jumlah_terima]"
                                            value="{{ old('detail.' . $detail->iddetail_penerimaan . '.jumlah_terima', $detail->jumlah_terima) }}"
                                            min="0"
                                            required
                                        >
                                    </td>

                                    <td>
                                        <input
                                            type="number"
                                            class="form-control"
                                            name="detail[{{ $detail->iddetail_penerimaan }}][harga_satuan_terima]"
                                            value="{{ old('detail.' . $detail->iddetail_penerimaan . '.harga_satuan_terima', $detail->harga_satuan_terima) }}"
                                            min="0"
                                            required
                                        >
                                    </td>

                                    <td class="text-end fw-semibold">
                                        Rp {{ number_format($detail->sub_total_terima, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada detail penerimaan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Perubahan jumlah dan harga satuan akan memperbarui subtotal,
                    harga barang, serta kartu stok melalui trigger database.
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a
                        href="{{ url('/transaksi/penerimaan/' . $penerimaan->idpenerimaan) }}"
                        class="btn btn-light border"
                    >
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection
