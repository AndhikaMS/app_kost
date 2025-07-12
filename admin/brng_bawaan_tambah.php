<?php
include '../inc/db.php';

// Ambil penghuni yang belum keluar
$penghuni = [];
$q = mysqli_query($conn, "SELECT id, nama FROM tb_penghuni WHERE tgl_keluar IS NULL ORDER BY nama ASC");
while ($row = mysqli_fetch_assoc($q)) {
    $penghuni[] = $row;
}
// Ambil semua barang
$barang = [];
$q = mysqli_query($conn, "SELECT id, nama FROM tb_barang ORDER BY nama ASC");
while ($row = mysqli_fetch_assoc($q)) {
    $barang[] = $row;
}

$pesan = '';
if (isset($_POST['tambah_bawaan'])) {
    $id_penghuni = (int) $_POST['id_penghuni'];
    $id_barang = (int) $_POST['id_barang'];
    if ($id_penghuni && $id_barang) {
        $sql = "INSERT INTO tb_brng_bawaan (id_penghuni, id_barang) VALUES ($id_penghuni, $id_barang)";
        if (mysqli_query($conn, $sql)) {
            header('Location: brng_bawaan.php?msg=sukses');
            exit;
        } else {
            $pesan = '<div class=\'alert alert-danger\'>Gagal menambah barang bawaan.</div>';
        }
    } else {
        $pesan = '<div class=\'alert alert-warning\'>Semua field wajib diisi.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang Bawaan - Admin Kos</title>
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
                <li class="nav-item"><a class="nav-link active" href="brng_bawaan.php">Barang Bawaan</a></li>
                <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
                <li class="nav-item"><a class="nav-link" href="tagihan.php">Tagihan</a></li>
                <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
            </ul>
        </div>
    </nav>
    <div class="container py-5">
        <h2 class="mb-4">Tambah Barang Bawaan</h2>
        <?php echo $pesan; ?>
        <div class="card">
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label for="id_penghuni" class="form-label">Pilih Penghuni</label>
                        <select class="form-select" id="id_penghuni" name="id_penghuni" required>
                            <option value="">-- Pilih Penghuni --</option>
                            <?php foreach ($penghuni as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="id_barang" class="form-label">Pilih Barang</label>
                        <select class="form-select" id="id_barang" name="id_barang" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($barang as $b): ?>
                                <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="tambah_bawaan" class="btn btn-success">Tambah</button>
                        <a href="brng_bawaan.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 