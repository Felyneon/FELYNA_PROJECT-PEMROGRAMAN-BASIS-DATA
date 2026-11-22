{{--
    FOOTER (partial). Dipanggil dari layouts/app.blade.php dengan:
        @include('layouts.partials.footer')
    Agar footer selalu menempel di bawah, <body> di layout memakai class "d-flex flex-column min-vh-100"
    dan @yield('content') dibungkus <main class="flex-grow-1">.
--}}
<footer class="mt-auto text-white-50" style="background:#212b3d;">
    <div class="container py-4">
        <div class="row gy-3 align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <div class="fw-bold text-white"><i class="fa-solid fa-store me-1"></i> Felyneon Store</div>
                <small>Sistem manajemen toko pakaian: barang, pengadaan, penerimaan, penjualan, dan retur.</small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="{{ url('/dashboard') }}" class="text-white-50 text-decoration-none me-3">Dashboard</a>
                <a href="{{ url('/transaksi/penjualan') }}" class="text-white-50 text-decoration-none me-3">Penjualan</a>
                <a href="{{ url('/transaksi/kartu-stok') }}" class="text-white-50 text-decoration-none">Kartu Stok</a>
            </div>
        </div>
        <hr class="border-secondary my-3">
        <div class="text-center small">
            &copy; {{ date('Y') }} Store Pakaian Project Management. All rights reserved.
        </div>
    </div>
</footer>
