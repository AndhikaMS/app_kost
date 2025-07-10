<?php
include '../inc/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: kmr_penghuni.php');
    exit;
}
$id = (int)$_GET['id'];

// Ambil data hunian
$sql = "SELECT * FROM tb_kmr_penghuni WHERE id=$id";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: kmr_penghuni.php');
    exit;
}
$hunian = mysqli_fetch_assoc($result);

// Ambil data penghuni
$q = mysqli_query($conn, "SELECT nama FROM tb_penghuni WHERE id=" . $hunian['id_penghuni']);
$penghuni = mysqli_fetch_assoc($q);

// Ambil semua kamar
$kamar = [];
$q = mysqli_query($conn, "SELECT id, nomor FROM tb_kamar ORDER BY nomor ASC");
while ($row = mysqli_fetch_assoc($q)) {
    $kamar[] = $row;
}

$pesan = '';
if (isset($_POST['edit_hunian'])) {
    $id_kamar = (int) $_POST['id_kamar'];
    $tgl_masuk = mysqli_real_escape_string($conn, $_POST['tgl_masuk']);
    $tgl_keluar = mysqli_real_escape_string($conn, $_POST['tgl_keluar']);
    if ($id_kamar && $tgl_masuk) {
        $sql = "UPDATE tb_kmr_penghuni SET id_kamar=$id_kamar, tgl_masuk='$tgl_masuk', tgl_keluar=" . ($tgl_keluar ? "'$tgl_keluar'" : "NULL") . " WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            header('Location: kmr_penghuni.php?msg=edit_sukses');
            exit;
        } else {
            $pesan = '<div class=\'alert alert-danger\'>Gagal mengedit data hunian.</div>';
        }
    } else {
        $pesan = '<div class=\'alert alert-warning\'>Semua field wajib diisi (kecuali tanggal keluar).</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Data Hunian - Admin Kos</title>
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
        <h2 class="mb-4">Edit Data Hunian</h2>
        <?php echo $pesan; ?>
        <div class="card">
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Penghuni</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($penghuni['nama']); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="id_kamar" class="form-label">Pilih Kamar</label>
                        <select class="form-select" id="id_kamar" name="id_kamar" required>
                            <option value="">-- Pilih Kamar --</option>
                            <?php foreach ($kamar as $k): ?>
                                <option value="<?php echo $k['id']; ?>" <?php if ($k['id'] == $hunian['id_kamar']) echo 'selected'; ?>><?php echo htmlspecialchars($k['nomor']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" value="<?php echo $hunian['tgl_masuk']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_keluar" class="form-label">Tanggal Keluar</label>
                        <input type="date" class="form-control" id="tgl_keluar" name="tgl_keluar" value="<?php echo $hunian['tgl_keluar']; ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="edit_hunian" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="kmr_penghuni.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 