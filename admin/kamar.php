<?php
include '../inc/db.php';
include '../inc/functions.php';

// Proses hapus kamar
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id > 0) {
        $sql = "DELETE FROM tb_kamar WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            header('Location: kamar.php?msg=hapus_sukses');
            exit;
        } else {
            $pesan = '<div class="alert alert-danger">Gagal menghapus kamar.</div>';
        }
    }
}

// Proses tambah kamar
$pesan = $pesan ?? '';
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
if (isset($_GET['msg']) && $_GET['msg'] === 'sukses') {
    $pesan = '<div class="alert alert-success">Kamar berhasil ditambahkan.</div>';
}
if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_sukses') {
    $pesan = '<div class="alert alert-success">Kamar berhasil dihapus.</div>';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Kamar - Admin Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
    function confirmHapus(nomor) {
        return confirm('Yakin ingin menghapus kamar nomor ' + nomor + '?');
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
                    <li class="nav-item"><a class="nav-link active" href="kamar.php">Kamar</a></li>
                    <li class="nav-item"><a class="nav-link" href="penghuni.php">Penghuni</a></li>
                    <li class="nav-item"><a class="nav-link" href="kmr_penghuni.php">Data Hunian</a></li>
                    <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
                    <li class="nav-item"><a class="nav-link" href="tagihan.php">Tagihan</a></li>
                    <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Data Kamar</h2>
            <a href="kamar_tambah.php" class="btn btn-success">+ Tambah Kamar</a>
        </div>
        <?php echo $pesan; ?>
        <!-- Hapus card/form tambah kamar di sini, hanya tampilkan tabel kamar -->
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Nomor Kamar</th>
                    <th>Harga Sewa</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM tb_kamar ORDER BY nomor ASC";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>' . $no++ . '</td>';
                        echo '<td>' . htmlspecialchars($row['nomor']) . '</td>';
                        echo '<td>Rp ' . number_format($row['harga'], 0, ',', '.') . '</td>';
                        echo '<td>';
                        echo '<a href="kamar_edit.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a> ';
                        echo '<a href="?hapus=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirmHapus(\'' . htmlspecialchars($row['nomor']) . '\')">Hapus</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center">Belum ada data kamar.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
