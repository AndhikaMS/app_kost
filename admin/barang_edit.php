<?php
include '../inc/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: barang.php');
    exit;
}
$id = (int)$_GET['id'];

// Ambil data barang
$sql = "SELECT * FROM tb_barang WHERE id=$id";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: barang.php');
    exit;
}
$barang = mysqli_fetch_assoc($result);

$pesan = '';
if (isset($_POST['edit_barang'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $harga = (int) $_POST['harga'];
    if ($nama && $harga > 0) {
        $sql = "UPDATE tb_barang SET nama='$nama', harga=$harga WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            header('Location: barang.php?msg=edit_sukses');
            exit;
        } else {
            $pesan = '<div class="alert alert-danger">Gagal mengedit barang.</div>';
        }
    } else {
        $pesan = '<div class="alert alert-warning">Nama barang dan harga harus diisi dengan benar.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang - Admin Kos</title>
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
                <li class="nav-item"><a class="nav-link" href="brng_bawaan.php">Barang Bawaan</a></li>
                <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
                <li class="nav-item"><a class="nav-link" href="tagihan.php">Tagihan</a></li>
                <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
            </ul>
        </div>
    </nav>
    <div class="container py-5">
        <h2 class="mb-4">Edit Barang</h2>
        <?php echo $pesan; ?>
        <div class="card">
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($barang['nama']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="harga" class="form-label">Harga</label>
                        <input type="number" class="form-control" id="harga" name="harga" min="1" value="<?php echo $barang['harga']; ?>" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="edit_barang" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="barang.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 