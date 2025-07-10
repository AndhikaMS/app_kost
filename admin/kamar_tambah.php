<?php
include '../inc/db.php';

$pesan = '';
if (isset($_POST['tambah_kamar'])) {
    $nomor = mysqli_real_escape_string($conn, $_POST['nomor']);
    $harga = (int) $_POST['harga'];
    if ($nomor && $harga > 0) {
        $sql = "INSERT INTO tb_kamar (nomor, harga) VALUES ('$nomor', $harga)";
        if (mysqli_query($conn, $sql)) {
            header('Location: kamar.php?msg=sukses');
            exit;
        } else {
            $pesan = '<div class="alert alert-danger">Gagal menambah kamar.</div>';
        }
    } else {
        $pesan = '<div class="alert alert-warning">Nomor kamar dan harga harus diisi dengan benar.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Kamar - Admin Kos</title>
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
        <h2 class="mb-4">Tambah Kamar</h2>
        <?php echo $pesan; ?>
        <div class="card">
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <label for="nomor" class="form-label">Nomor Kamar</label>
                        <input type="text" class="form-control" id="nomor" name="nomor" required>
                    </div>
                    <div class="col-md-6">
                        <label for="harga" class="form-label">Harga Sewa</label>
                        <input type="number" class="form-control" id="harga" name="harga" min="1" required>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="tambah_kamar" class="btn btn-success">Tambah</button>
                        <a href="kamar.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 