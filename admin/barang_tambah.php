<?php
include '../inc/db.php';

$pesan = '';
if (isset($_POST['tambah_barang'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = (int) $_POST['harga'];
    if ($nama && $harga > 0) {
        $sql = "INSERT INTO tb_barang (nama, harga) VALUES ('$nama', $harga)";
        if (mysqli_query($conn, $sql)) {
            header('Location: barang.php?msg=sukses');
            exit;
        } else {
            $pesan = '<div class="alert alert-danger">Gagal menambah barang.</div>';
        }
    } else {
        $pesan = '<div class="alert alert-warning">Nama barang dan harga harus diisi dengan benar.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang - Admin Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
                <li class="nav-item"><a class="nav-link" href="tagihan.php">Tagihan</a></li>
                <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
            </ul>
        </div>
    </nav>
    <div class="container py-5">
        <h2 class="mb-4">Tambah Barang</h2>
        <?php echo $pesan; ?>
        <div class="card">
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="col-md-6">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" min="1" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="tambah_barang" class="btn btn-success">Tambah</button>
                        <a href="barang.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 