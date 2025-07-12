<?php
include '../inc/db.php';

// Proses hapus barang bawaan
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id > 0) {
        $sql = "DELETE FROM tb_brng_bawaan WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            header('Location: brng_bawaan.php?msg=hapus_sukses');
            exit;
        } else {
            $pesan = '<div class="alert alert-danger">Gagal menghapus barang bawaan.</div>';
        }
    }
}

$pesan = $pesan ?? '';
if (isset($_GET['msg']) && $_GET['msg'] === 'sukses') {
    $pesan = '<div class="alert alert-success">Barang bawaan berhasil ditambahkan.</div>';
}
if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_sukses') {
    $pesan = '<div class="alert alert-success">Barang bawaan berhasil dihapus.</div>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Barang Bawaan Penghuni - Admin Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function confirmHapus(nama, barang) {
        return confirm('Yakin ingin menghapus barang ' + barang + ' milik ' + nama + '?');
    }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Kos</a>
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="kamar.php">Kamar</a></li>
                <li class="nav-item"><a class="nav-link" href="penghuni.php">Penghuni</a></li>
                <li class="nav-item"><a class="nav-link" href="kmr_penghuni.php">Data Hunian</a></li>
                <li class="nav-item"><a class="nav-link active" href="brng_bawaan.php">Barang Bawaan</a></li>
                <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
                <li class="nav-item"><a class="nav-link" href="tagihan.php">Tagihan</a></li>
                <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
            </ul>
        </div>
    </nav>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Data Barang Bawaan Penghuni</h2>
            <a href="brng_bawaan_tambah.php" class="btn btn-success">+ Tambah Barang Bawaan</a>
        </div>
        <?php echo $pesan; ?>
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Nama Penghuni</th>
                    <th>Nama Barang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT b.id, p.nama AS nama_penghuni, br.nama AS nama_barang
                        FROM tb_brng_bawaan b
                        JOIN tb_penghuni p ON b.id_penghuni = p.id
                        JOIN tb_barang br ON b.id_barang = br.id
                        ORDER BY p.nama, br.nama";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $no++ . '</td>';
                        echo '<td>' . htmlspecialchars($row['nama_penghuni']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['nama_barang']) . '</td>';
                        echo '<td>';
                        echo '<a href="?hapus=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirmHapus(\'' . htmlspecialchars($row['nama_penghuni']) . '\', \'' . htmlspecialchars($row['nama_barang']) . '\')">Hapus</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center">Belum ada data barang bawaan penghuni.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
