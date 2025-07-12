<?php
// Fungsi untuk format tanggal Indonesia
function tgl_indo($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

// Fungsi untuk mendapatkan kamar kosong (dummy, nanti diisi query)
function get_kamar_kosong($conn) {
    $sql = "SELECT * FROM tb_kamar WHERE id NOT IN (SELECT id_kamar FROM tb_kmr_penghuni WHERE tgl_keluar IS NULL)";
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}

// Fungsi untuk mendapatkan status tagihan
function get_status_tagihan($conn, $id_tagihan) {
    $sql = "SELECT t.jml_tagihan, COALESCE(SUM(b.jml_bayar), 0) as total_bayar
            FROM tb_tagihan t
            LEFT JOIN tb_bayar b ON t.id = b.id_tagihan
            WHERE t.id = $id_tagihan
            GROUP BY t.id";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $total_bayar = $row['total_bayar'];
        $jml_tagihan = $row['jml_tagihan'];
        
        if ($total_bayar >= $jml_tagihan) {
            return ['status' => 'Lunas', 'class' => 'success', 'sisa' => 0];
        } elseif ($total_bayar > 0) {
            return ['status' => 'Cicil', 'class' => 'warning', 'sisa' => $jml_tagihan - $total_bayar];
        } else {
            return ['status' => 'Belum Bayar', 'class' => 'danger', 'sisa' => $jml_tagihan];
        }
    }
    
    return ['status' => 'Tidak Diketahui', 'class' => 'secondary', 'sisa' => 0];
}

// Fungsi untuk format bulan Indonesia
function format_bulan($bulan) {
    $bulan_array = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    $pecah = explode('-', $bulan);
    if (count($pecah) == 2) {
        return $bulan_array[$pecah[1]] . ' ' . $pecah[0];
    }
    
    return $bulan;
}

// Fungsi untuk mendapatkan total pembayaran tagihan
function get_total_pembayaran($conn, $id_tagihan) {
    $sql = "SELECT COALESCE(SUM(jml_bayar), 0) as total FROM tb_bayar WHERE id_tagihan = $id_tagihan";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}

// Fungsi untuk mendapatkan sisa tagihan
function get_sisa_tagihan($conn, $id_tagihan) {
    $sql = "SELECT t.jml_tagihan, COALESCE(SUM(b.jml_bayar), 0) as total_bayar
            FROM tb_tagihan t
            LEFT JOIN tb_bayar b ON t.id = b.id_tagihan
            WHERE t.id = $id_tagihan
            GROUP BY t.id";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['jml_tagihan'] - $row['total_bayar'];
    }
    
    return 0;
}
