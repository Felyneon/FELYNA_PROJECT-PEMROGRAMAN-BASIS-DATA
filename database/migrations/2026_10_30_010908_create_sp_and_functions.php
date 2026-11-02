<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus dulu kalau sudah pernah dibuat manual
        foreach ([
            'PROCEDURE sp_laporan_penjualan_berdasarkan_user',
            'PROCEDURE sp_stok_barang_rendah',
            'PROCEDURE sp_riwayat_kartu_stok_barang',
            'PROCEDURE sp_laporan_pengadaan_vendor',
            'FUNCTION fn_hitung_harga_jual',
            'FUNCTION fn_status_badan_hukum_vendor',
            'FUNCTION fn_get_sisa_stok',
            'FUNCTION fn_hitung_total_penerimaan',
        ] as $obj) {
            DB::unprepared("DROP $obj IF EXISTS");
        }

        // ===================== STORED PROCEDURE =====================

        DB::unprepared("
            CREATE PROCEDURE sp_laporan_penjualan_berdasarkan_user (IN p_iduser INT)
            BEGIN
                SELECT p.idpenjualan,
                       p.created_at AS tanggal_transaksi,
                       u.username   AS nama_kasir,
                       p.subtotal_nilai,
                       p.ppn,
                       p.total_nilai
                FROM penjualan p
                JOIN `user` u ON p.iduser = u.iduser
                WHERE p.iduser = p_iduser;
            END
        ");

        // LEFT JOIN: barang yang belum punya kartu stok tetap tampil dengan stok 0
        DB::unprepared("
            CREATE PROCEDURE sp_stok_barang_rendah (IN p_batas_stok INT)
            BEGIN
                SELECT b.idbarang,
                       b.nama AS nama_barang,
                       b.jenis,
                       s.nama_satuan,
                       COALESCE(ks.stock, 0) AS sisa_stok
                FROM barang b
                JOIN satuan s ON b.idsatuan = s.idsatuan
                LEFT JOIN (
                    SELECT idbarang, stock,
                           ROW_NUMBER() OVER (PARTITION BY idbarang ORDER BY idkartu_stok DESC) AS rn
                    FROM kartu_stok
                ) ks ON b.idbarang = ks.idbarang AND ks.rn = 1
                WHERE COALESCE(ks.stock, 0) <= p_batas_stok;
            END
        ");

        DB::unprepared("
            CREATE PROCEDURE sp_riwayat_kartu_stok_barang (IN p_idbarang INT)
            BEGIN
                SELECT ks.idkartu_stok,
                       b.nama AS nama_barang,
                       CASE ks.jenis_transaksi
                           WHEN 'M' THEN 'Masuk (Penerimaan/Retur Masuk)'
                           WHEN 'K' THEN 'Keluar (Penjualan/Retur Keluar)'
                           WHEN 'R' THEN 'Retur ke Vendor'
                           ELSE 'Lainnya'
                       END AS ket_transaksi,
                       ks.masuk,
                       ks.keluar,
                       ks.stock AS stok_berjalan,
                       ks.created_at AS waktu_mutasi,
                       ks.idtransaksi
                FROM kartu_stok ks
                JOIN barang b ON ks.idbarang = b.idbarang
                WHERE ks.idbarang = p_idbarang
                ORDER BY ks.idkartu_stok ASC;
            END
        ");

        DB::unprepared("
            CREATE PROCEDURE sp_laporan_pengadaan_vendor (IN p_idvendor INT)
            BEGIN
                SELECT pg.idpengadaan,
                       pg.timestamp AS tanggal_pengadaan,
                       v.nama_vendor,
                       u.username AS nama_staff_pembuat,
                       pg.subtotal_nilai,
                       pg.ppn,
                       pg.total_nilai
                FROM pengadaan pg
                JOIN vendor v ON pg.vendor_idvendor = v.idvendor
                JOIN `user` u ON pg.user_iduser = u.iduser
                WHERE pg.vendor_idvendor = p_idvendor;
            END
        ");

        // ========================= FUNCTION =========================
        // READS SQL DATA (bukan DETERMINISTIC) karena function membaca tabel

        DB::unprepared("
            CREATE FUNCTION fn_hitung_harga_jual (p_idbarang INT, p_idmargin INT)
            RETURNS INT
            READS SQL DATA
            BEGIN
                DECLARE v_harga_dasar INT;
                DECLARE v_persen_margin DOUBLE;
                DECLARE v_harga_jual INT;

                SELECT harga INTO v_harga_dasar FROM barang WHERE idbarang = p_idbarang;
                SELECT persen INTO v_persen_margin FROM margin_penjualan
                 WHERE idmargin_penjualan = p_idmargin;

                SET v_harga_jual = v_harga_dasar + (v_harga_dasar * v_persen_margin / 100);
                RETURN v_harga_jual;
            END
        ");

        DB::unprepared("
            CREATE FUNCTION fn_status_badan_hukum_vendor (p_idvendor INT)
            RETURNS VARCHAR(50)
            READS SQL DATA
            BEGIN
                DECLARE v_kode CHAR(1);
                DECLARE v_hasil VARCHAR(50);

                SELECT badan_hukum INTO v_kode FROM vendor WHERE idvendor = p_idvendor;

                IF v_kode = 'P' THEN
                    SET v_hasil = 'Perseroan Terbatas (PT)';
                ELSEIF v_kode = 'C' THEN
                    SET v_hasil = 'Commanditaire Vennootschap (CV)';
                ELSEIF v_kode = 'U' THEN
                    SET v_hasil = 'Usaha Dagang (UD)';
                ELSE
                    SET v_hasil = 'Lainnya / Perorangan';
                END IF;

                RETURN v_hasil;
            END
        ");

        DB::unprepared("
            CREATE FUNCTION fn_get_sisa_stok (p_idbarang INT)
            RETURNS INT
            READS SQL DATA
            BEGIN
                DECLARE v_sisa INT;

                SELECT COALESCE((
                    SELECT stock FROM kartu_stok
                    WHERE idbarang = p_idbarang
                    ORDER BY idkartu_stok DESC
                    LIMIT 1
                ), 0) INTO v_sisa;

                RETURN v_sisa;
            END
        ");

        DB::unprepared("
            CREATE FUNCTION fn_hitung_total_penerimaan (p_idpenerimaan BIGINT)
            RETURNS INT
            READS SQL DATA
            BEGIN
                DECLARE v_total_terima INT;

                SELECT COALESCE(SUM(sub_total_terima), 0) INTO v_total_terima
                FROM detail_penerimaan
                WHERE idpenerimaan = p_idpenerimaan;

                RETURN v_total_terima;
            END
        ");
    }

    public function down(): void
    {
        foreach ([
            'PROCEDURE sp_laporan_penjualan_berdasarkan_user',
            'PROCEDURE sp_stok_barang_rendah',
            'PROCEDURE sp_riwayat_kartu_stok_barang',
            'PROCEDURE sp_laporan_pengadaan_vendor',
            'FUNCTION fn_hitung_harga_jual',
            'FUNCTION fn_status_badan_hukum_vendor',
            'FUNCTION fn_get_sisa_stok',
            'FUNCTION fn_hitung_total_penerimaan',
        ] as $obj) {
            DB::unprepared("DROP $obj IF EXISTS");
        }
    }
};