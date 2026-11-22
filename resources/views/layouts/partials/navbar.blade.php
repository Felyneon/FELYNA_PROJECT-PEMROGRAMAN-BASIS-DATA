{{--
    NAVBAR (partial). Dipanggil dari layouts/app.blade.php dengan:
        @include('layouts.partials.navbar')
    Butuh Bootstrap 5 (CSS + JS bundle) dan Font Awesome 6.
--}}
@php
    $namaUser = session('nama_lengkap') ?? session('username') ?? 'User';
    $aktifTransaksi = request()->is('transaksi/*');
    // Dropdown Transaksi tampil hanya jika role punya minimal satu izin transaksi
    $adaTransaksi = \App\Support\Akses::bisa('penjualan.read') || \App\Support\Akses::bisa('pengadaan.read') || \App\Support\Akses::bisa('penerimaan.read')
                 || \App\Support\Akses::bisa('retur.read') || \App\Support\Akses::bisa('kartu_stok.read');
@endphp

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm sticky-top"
     style="background: linear-gradient(90deg, #5671c9 0%, #3b5bdb 100%);">
    <div class="container-fluid px-4">

        {{-- Brand --}}
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="{{ url('/dashboard') }}">
            <i class="fa-solid fa-store"></i> Felyneon Store
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navUtama"
                aria-controls="navUtama" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navUtama">

            {{-- Menu --}}
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                @akses('barang.read')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active fw-semibold' : '' }}" href="{{ url('/dashboard') }}">
                        <i class="fa-solid fa-chart-pie me-1"></i> Dashboard Barang
                    </a>
                </li>
                @endakses
                @akses('role.manage')
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('role*') ? 'active fw-semibold' : '' }}" href="{{ url('/role') }}">
                        <i class="fa-solid fa-user-gear me-1"></i> Kelola Role
                    </a>
                </li>
                @endakses
                @akses('margin.read')
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/margin') }}">
                        <i class="fa-solid fa-percent me-1"></i>
                Kelola Margin
                        </a>
                    </li>
                @endakses
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('margin*') ? 'active fw-semibold' : '' }}" href="{{ url('/margin') }}">
                        <i class="fa-solid fa-percent me-1"></i> Kelola Margin
                    </a>
                </li>
                @endakses
                @if($adaTransaksi)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ $aktifTransaksi ? 'active fw-semibold' : '' }}" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-cash-register me-1"></i> Manajemen Transaksi
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        @akses('penjualan.read')
                        <li><a class="dropdown-item {{ request()->is('transaksi/penjualan*') ? 'active' : '' }}" href="{{ url('/transaksi/penjualan') }}">Penjualan (POS)</a></li>
                        @endakses
                        @akses('pengadaan.read')
                        <li><a class="dropdown-item {{ request()->is('transaksi/pengadaan*') ? 'active' : '' }}" href="{{ url('/transaksi/pengadaan') }}">Pengadaan</a></li>
                        @endakses
                        @akses('penerimaan.read')
                        <li><a class="dropdown-item {{ request()->is('transaksi/penerimaan*') ? 'active' : '' }}" href="{{ url('/transaksi/penerimaan') }}">Penerimaan</a></li>
                        @endakses
                        @akses('retur.read')
                        <li><a class="dropdown-item {{ request()->is('transaksi/retur*') ? 'active' : '' }}" href="{{ url('/transaksi/retur') }}">Retur</a></li>
                        @endakses
                        @akses('kartu_stok.read')
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item {{ request()->is('transaksi/kartu-stok*') ? 'active' : '' }}" href="{{ url('/transaksi/kartu-stok') }}">Kartu Stok</a></li>
                        @endakses
                    </ul>
                </li>
                @endif
            </ul>

            {{-- Search bar: mencari barang (nama, jenis, satuan) di Dashboard --}}
            @akses('barang.read')
            <form class="d-flex me-lg-3 mb-2 mb-lg-0" role="search" method="GET" action="{{ url('/dashboard') }}">
                <div class="input-group">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control border-0"
                           placeholder="Cari barang..." aria-label="Cari barang" autocomplete="off">
                    <button class="btn btn-light" type="submit" aria-label="Cari">
                        <i class="fa-solid fa-magnifying-glass text-primary"></i>
                    </button>
                </div>
            </form>
            @endakses

            {{-- User + Logout --}}
            <div class="d-flex align-items-center gap-3">
                <a href="{{ url('/profile') }}" class="text-white text-decoration-none d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-user fs-5"></i> <span>{{ $namaUser }}</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm fw-semibold text-danger">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
