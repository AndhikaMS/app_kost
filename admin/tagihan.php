<?php
include '../inc/db.php';
include '../inc/functions.php';

// Proses hapus tagihan
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id > 0) {
        // Cek apakah ada pembayaran terkait
        $check_sql = "SELECT COUNT(*) as count FROM tb_bayar WHERE id_tagihan = $id";
        $check_result = mysqli_query($conn, $check_sql);
        $check_row = mysqli_fetch_assoc($check_result);
        
        if ($check_row['count'] > 0) {
            $pesan = '<div class="alert alert-warning">Tagihan tidak dapat dihapus karena sudah ada pembayaran terkait.</div>';
        } else {
            $sql = "DELETE FROM tb_tagihan WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                header('Location: tagihan.php?msg=hapus_sukses');
                exit;
            } else {
                $pesan = '<div class="alert alert-danger">Gagal menghapus tagihan.</div>';
            }
        }
    }
}

// Pesan feedback
$pesan = $pesan ?? '';
if (isset($_GET['msg']) && $_GET['msg'] === 'sukses') {
    $pesan = '<div class="alert alert-success">Tagihan berhasil ditambahkan.</div>';
}
if (isset($_GET['msg']) && $_GET['msg'] === 'edit_sukses') {
    $pesan = '<div class="alert alert-success">Tagihan berhasil diperbarui.</div>';
}
if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_sukses') {
    $pesan = '<div class="alert alert-success">Tagihan berhasil dihapus.</div>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Tagihan - Admin Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function confirmHapus(bulan, nama) {
        return confirm('Yakin ingin menghapus tagihan ' + bulan + ' untuk ' + nama + '?');
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
                    <li class="nav-item"><a class="nav-link active" href="tagihan.php">Tagihan</a></li>
                    <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Data Tagihan</h2>
            <div>
                <a href="generate_tagihan.php" class="btn btn-info me-2">Generate Tagihan</a>
                <a href="tagihan_tambah.php" class="btn btn-success">+ Tambah Tagihan</a>
            </div>
        </div>
        <?php echo $pesan; ?>
        
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Bulan</th>
                    <th>Penghuni</th>
                    <th>Kamar</th>
                    <th>Jumlah Tagihan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT t.*, 
                               kp.id_kamar, kp.id_penghuni,
                               p.nama as nama_penghuni,
                               km.nomor as nomor_kamar,
                               km.harga as harga_kamar,
                               COALESCE(SUM(b.jml_bayar), 0) as total_bayar
                        FROM tb_tagihan t
                        JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id
                        JOIN tb_penghuni p ON kp.id_penghuni = p.id
                        JOIN tb_kamar km ON kp.id_kamar = km.id
                        LEFT JOIN tb_bayar b ON t.id = b.id_tagihan
                        GROUP BY t.id
                        ORDER BY t.bulan DESC, p.nama ASC";
                
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $status = '';
                        $status_class = '';
                        
                        $status_info = get_status_tagihan($conn, $row['id']);
                        
                        echo '<tr>';
                        echo '<td>' . $no++ . '</td>';
                        echo '<td>' . format_bulan($row['bulan']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['nama_penghuni']) . '</td>';
                        echo '<td>Kamar ' . htmlspecialchars($row['nomor_kamar']) . '</td>';
                        echo '<td>Rp ' . number_format($row['jml_tagihan'], 0, ',', '.') . '</td>';
                        echo '<td><span class="badge bg-' . $status_info['class'] . '">' . $status_info['status'] . '</span></td>';
                        echo '<td>';
                        echo '<a href="tagihan_edit.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a> ';
                        echo '<a href="?hapus=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirmHapus(\'' . format_bulan($row['bulan']) . '\', \'' . htmlspecialchars($row['nama_penghuni']) . '\')">Hapus</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="7" class="text-center">Belum ada data tagihan.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
