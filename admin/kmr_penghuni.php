<?php
include '../inc/db.php';

// Proses hapus data hunian
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id > 0) {
        $sql = "DELETE FROM tb_kmr_penghuni WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            header('Location: kmr_penghuni.php?msg=hapus_sukses');
            exit;
        } else {
            $pesan = '<div class="alert alert-danger">Gagal menghapus data hunian.</div>';
        }
    }
}

$pesan = $pesan ?? '';
if (isset($_GET['msg']) && $_GET['msg'] === 'sukses') {
    $pesan = '<div class="alert alert-success">Data hunian berhasil ditambahkan.</div>';
}
if (isset($_GET['msg']) && $_GET['msg'] === 'edit_sukses') {
    $pesan = '<div class="alert alert-success">Data hunian berhasil diedit.</div>';
}
if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_sukses') {
    $pesan = '<div class="alert alert-success">Data hunian berhasil dihapus.</div>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Relasi Kamar-Penghuni - Admin Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function confirmHapus(nama, kamar) {
        return confirm('Yakin ingin menghapus hunian: ' + nama + ' di kamar ' + kamar + '?');
    }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Kos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="#navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="kamar.php">Kamar</a></li>
                    <li class="nav-item"><a class="nav-link" href="penghuni.php">Penghuni</a></li>
                    <li class="nav-item"><a class="nav-link active" href="kmr_penghuni.php">Data Hunian</a></li>
                    <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
                    <li class="nav-item"><a class="nav-link" href="tagihan.php">Tagihan</a></li>
                    <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Data Relasi Kamar - Penghuni</h2>
            <a href="kmr_penghuni_tambah.php" class="btn btn-success">+ Tambah Relasi</a>
        </div>
        <?php echo $pesan; ?>
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Nama Penghuni</th>
                    <th>Nomor Kamar</th>
                    <th>Tgl Masuk</th>
                    <th>Tgl Keluar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT r.id, p.nama, k.nomor, r.tgl_masuk, r.tgl_keluar
                        FROM tb_kmr_penghuni r
                        JOIN tb_penghuni p ON r.id_penghuni = p.id
                        JOIN tb_kamar k ON r.id_kamar = k.id
                        ORDER BY r.tgl_masuk DESC, r.id DESC";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $no++ . '</td>';
                        echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['nomor']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['tgl_masuk']) . '</td>';
                        echo '<td>' . ($row['tgl_keluar'] ? htmlspecialchars($row['tgl_keluar']) : '-') . '</td>';
                        echo '<td>';
                        echo '<a href="kmr_penghuni_edit.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a> ';
                        echo '<a href="?hapus=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirmHapus(\'' . htmlspecialchars($row['nama']) . '\', \'' . htmlspecialchars($row['nomor']) . '\')">Hapus</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">Belum ada data relasi kamar-penghuni.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
