<?php
include '../inc/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: penghuni.php');
    exit;
}
$id = (int)$_GET['id'];

// Ambil data penghuni
$sql = "SELECT * FROM tb_penghuni WHERE id=$id";
$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: penghuni.php');
    exit;
}
$penghuni = mysqli_fetch_assoc($result);

$pesan = '';
if (isset($_POST['edit_penghuni'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_ktp = mysqli_real_escape_string($conn, $_POST['no_ktp']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $tgl_masuk = mysqli_real_escape_string($conn, $_POST['tgl_masuk']);
    $tgl_keluar = mysqli_real_escape_string($conn, $_POST['tgl_keluar']);
    if ($nama && $no_ktp && $no_hp && $tgl_masuk) {
        $sql = "UPDATE tb_penghuni SET nama='$nama', no_ktp='$no_ktp', no_hp='$no_hp', tgl_masuk='$tgl_masuk', tgl_keluar=" . ($tgl_keluar ? "'$tgl_keluar'" : "NULL") . " WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            header('Location: penghuni.php?msg=edit_sukses');
            exit;
        } else {
            $pesan = '<div class="alert alert-danger">Gagal mengedit penghuni.</div>';
        }
    } else {
        $pesan = '<div class="alert alert-warning">Semua field wajib diisi (kecuali tanggal keluar).</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Penghuni - Admin Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Kos</a>
        </div>
    </nav>
    <div class="container py-5">
        <h2 class="mb-4">Edit Penghuni</h2>
        <?php echo $pesan; ?>
        <div class="card">
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($penghuni['nama']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="no_ktp" class="form-label">No KTP</label>
                        <input type="text" class="form-control" id="no_ktp" name="no_ktp" value="<?php echo htmlspecialchars($penghuni['no_ktp']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="no_hp" class="form-label">No HP</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($penghuni['no_hp']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" value="<?php echo $penghuni['tgl_masuk']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tgl_keluar" class="form-label">Tanggal Keluar</label>
                        <input type="date" class="form-control" id="tgl_keluar" name="tgl_keluar" value="<?php echo $penghuni['tgl_keluar']; ?>">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="edit_penghuni" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="penghuni.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 