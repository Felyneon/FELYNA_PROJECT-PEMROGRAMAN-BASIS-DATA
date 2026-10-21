<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Barang Store Pakaian</title>
</head>
<body>
    <h1>Daftar Katalog Pakaian</h1>

    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Nama Pakaian</th>
                <th>Jenis</th>
                <th>Harga</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangs as $item)
            <tr>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->jenis }}</td>
                <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                <td>{{ $item->satuan->nama_satuan }}</td> 
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>