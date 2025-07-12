<?php
include '../inc/db.php';
include '../inc/functions.php';

// Proses hapus pembayaran
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id > 0) {
        $sql = "DELETE FROM tb_bayar WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            header('Location: bayar.php?msg=hapus_sukses');
            exit;
        } else {
            $pesan = '<div class="alert alert-danger">Gagal menghapus pembayaran.</div>';
        }
    }
}

// Pesan feedback
$pesan = $pesan ?? '';
if (isset($_GET['msg']) && $_GET['msg'] === 'sukses') {
    $pesan = '<div class="alert alert-success">Pembayaran berhasil ditambahkan.</div>';
}
if (isset($_GET['msg']) && $_GET['msg'] === 'edit_sukses') {
    $pesan = '<div class="alert alert-success">Pembayaran berhasil diperbarui.</div>';
}
if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_sukses') {
    $pesan = '<div class="alert alert-success">Pembayaran berhasil dihapus.</div>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Pembayaran - Admin Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function confirmHapus(tanggal, nama, jumlah) {
        return confirm('Yakin ingin menghapus pembayaran Rp ' + jumlah + ' untuk ' + nama + ' pada ' + tanggal + '?');
    }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Kos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="kamar.php">Kamar</a></li>
                    <li class="nav-item"><a class="nav-link" href="penghuni.php">Penghuni</a></li>
                    <li class="nav-item"><a class="nav-link" href="kmr_penghuni.php">Data Hunian</a></li>
                    <li class="nav-item"><a class="nav-link" href="brng_bawaan.php">Barang Bawaan</a></li>
                    <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
                    <li class="nav-item"><a class="nav-link" href="tagihan.php">Tagihan</a></li>
                    <li class="nav-item"><a class="nav-link active" href="bayar.php">Pembayaran</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Data Pembayaran</h2>
            <a href="bayar_tambah.php" class="btn btn-success">+ Tambah Pembayaran</a>
        </div>
        <?php echo $pesan; ?>
        
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Penghuni</th>
                    <th>Kamar</th>
                    <th>Bulan Tagihan</th>
                    <th>Jumlah Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT b.*, t.bulan, t.jml_tagihan,
                               p.nama as nama_penghuni,
                               km.nomor as nomor_kamar,
                               COALESCE(SUM(b2.jml_bayar), 0) as total_bayar_sebelumnya
                        FROM tb_bayar b
                        JOIN tb_tagihan t ON b.id_tagihan = t.id
                        JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id
                        JOIN tb_penghuni p ON kp.id_penghuni = p.id
                        JOIN tb_kamar km ON kp.id_kamar = km.id
                        LEFT JOIN tb_bayar b2 ON t.id = b2.id_tagihan AND b2.id < b.id
                        GROUP BY b.id
                        ORDER BY b.id DESC";
                
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $total_bayar = $row['total_bayar_sebelumnya'] + $row['jml_bayar'];
                        $sisa_tagihan = $row['jml_tagihan'] - $total_bayar;
                        
                        $status = '';
                        $status_class = '';
                        
                        if ($sisa_tagihan <= 0) {
                            $status = 'Lunas';
                            $status_class = 'success';
                        } else {
                            $status = 'Cicil';
                            $status_class = 'warning';
                        }
                        
                        echo '<tr>';
                        echo '<td>' . $no++ . '</td>';
                        echo '<td>' . tgl_indo($row['tgl_bayar']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['nama_penghuni']) . '</td>';
                        echo '<td>Kamar ' . htmlspecialchars($row['nomor_kamar']) . '</td>';
                        echo '<td>' . format_bulan($row['bulan']) . '</td>';
                        echo '<td>Rp ' . number_format($row['jml_bayar'], 0, ',', '.') . '</td>';
                        echo '<td><span class="badge bg-' . $status_class . '">' . $status . '</span></td>';
                        echo '<td>';
                        echo '<a href="bayar_edit.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a> ';
                        echo '<a href="?hapus=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirmHapus(\'' . tgl_indo($row['tgl_bayar']) . '\', \'' . htmlspecialchars($row['nama_penghuni']) . '\', \'' . number_format($row['jml_bayar'], 0, ',', '.') . '\')">Hapus</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="8" class="text-center">Belum ada data pembayaran.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
