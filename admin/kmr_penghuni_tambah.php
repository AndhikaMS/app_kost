<?php
include '../inc/db.php';

// Ambil penghuni yang belum keluar
$penghuni = [];
$q = mysqli_query($conn, "SELECT id, nama FROM tb_penghuni WHERE tgl_keluar IS NULL ORDER BY nama ASC");
while ($row = mysqli_fetch_assoc($q)) {
    $penghuni[] = $row;
}
// Ambil kamar yang kosong (tidak ada relasi aktif di tb_kmr_penghuni)
$kamar = [];
$q = mysqli_query($conn, "SELECT id, nomor FROM tb_kamar WHERE id NOT IN (SELECT id_kamar FROM tb_kmr_penghuni WHERE tgl_keluar IS NULL)");
while ($row = mysqli_fetch_assoc($q)) {
    $kamar[] = $row;
}

$pesan = '';
if (isset($_POST['tambah_hunian'])) {
    $id_penghuni = (int) $_POST['id_penghuni'];
    $id_kamar = (int) $_POST['id_kamar'];
    $tgl_masuk = mysqli_real_escape_string($conn, $_POST['tgl_masuk']);
    if ($id_penghuni && $id_kamar && $tgl_masuk) {
        $sql = "INSERT INTO tb_kmr_penghuni (id_kamar, id_penghuni, tgl_masuk) VALUES ($id_kamar, $id_penghuni, '$tgl_masuk')";
        if (mysqli_query($conn, $sql)) {
            header('Location: kmr_penghuni.php?msg=sukses');
            exit;
        } else {
            $pesan = '<div class=\'alert alert-danger\'>Gagal menambah data hunian.</div>';
        }
    } else {
        $pesan = '<div class=\'alert alert-warning\'>Semua field wajib diisi.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data Hunian - Admin Kos</title>
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
                <li class="nav-item"><a class="nav-link active" href="kmr_penghuni.php">Data Hunian</a></li>
                <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
                <li class="nav-item"><a class="nav-link" href="tagihan.php">Tagihan</a></li>
                <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
            </ul>
        </div>
    </nav>
    <div class="container py-5">
        <h2 class="mb-4">Tambah Data Hunian</h2>
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
                        <label for="id_kamar" class="form-label">Pilih Kamar</label>
                        <select class="form-select" id="id_kamar" name="id_kamar" required>
                            <option value="">-- Pilih Kamar --</option>
                            <?php foreach ($kamar as $k): ?>
                                <option value="<?php echo $k['id']; ?>"><?php echo htmlspecialchars($k['nomor']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="tambah_hunian" class="btn btn-success">Tambah</button>
                        <a href="kmr_penghuni.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 