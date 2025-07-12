<?php
include '../inc/db.php';
include '../inc/functions.php';

$pesan = '';

// Ambil data tagihan yang akan diedit
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT t.*, kp.id_kamar, kp.id_penghuni,
                   p.nama as nama_penghuni,
                   km.nomor as nomor_kamar
            FROM tb_tagihan t
            JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id
            JOIN tb_penghuni p ON kp.id_penghuni = p.id
            JOIN tb_kamar km ON kp.id_kamar = km.id
            WHERE t.id = $id";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $tagihan = mysqli_fetch_assoc($result);
    } else {
        header('Location: tagihan.php');
        exit;
    }
} else {
    header('Location: tagihan.php');
    exit;
}

// Proses edit tagihan
if (isset($_POST['edit_tagihan'])) {
    $bulan = mysqli_real_escape_string($conn, $_POST['bulan']);
    $id_kmr_penghuni = (int) $_POST['id_kmr_penghuni'];
    $jml_tagihan = (int) $_POST['jml_tagihan'];
    
    // Validasi input
    if ($bulan && $id_kmr_penghuni > 0 && $jml_tagihan > 0) {
        // Cek apakah tagihan untuk bulan dan hunian yang sama sudah ada (kecuali yang sedang diedit)
        $check_sql = "SELECT COUNT(*) as count FROM tb_tagihan t 
                      JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id 
                      WHERE t.bulan = '$bulan' AND kp.id = $id_kmr_penghuni AND t.id != $id";
        $check_result = mysqli_query($conn, $check_sql);
        $check_row = mysqli_fetch_assoc($check_result);
        
        if ($check_row['count'] > 0) {
            $pesan = '<div class="alert alert-warning">Tagihan untuk bulan dan penghuni tersebut sudah ada.</div>';
        } else {
            $sql = "UPDATE tb_tagihan SET bulan='$bulan', id_kmr_penghuni=$id_kmr_penghuni, jml_tagihan=$jml_tagihan WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                header('Location: tagihan.php?msg=edit_sukses');
                exit;
            } else {
                $pesan = '<div class="alert alert-danger">Gagal memperbarui tagihan.</div>';
            }
        }
    } else {
        $pesan = '<div class="alert alert-warning">Semua field harus diisi dengan benar.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Tagihan - Admin Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Kos</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="kamar.php">Kamar</a></li>
                    <li class="nav-item"><a class="nav-link" href="penghuni.php">Penghuni</a></li>
                    <li class="nav-item"><a class="nav-link" href="kmr_penghuni.php">Data Hunian</a></li>
                    <li class="nav-item"><a class="nav-link" href="brng_bawaan.php">Barang Bawaan</a></li>
                    <li class="nav-item"><a class="nav-link" href="barang.php">Barang</a></li>
                    <li class="nav-item"><a class="nav-link active" href="tagihan.php">Tagihan</a></li>
                    <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Tagihan</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $pesan; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Bulan Tagihan</label>
                                <input type="month" class="form-control" id="bulan" name="bulan" required 
                                       value="<?php echo htmlspecialchars($tagihan['bulan']); ?>">
                                <div class="form-text">Pilih bulan untuk tagihan</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="id_kmr_penghuni" class="form-label">Penghuni & Kamar</label>
                                <select class="form-control" id="id_kmr_penghuni" name="id_kmr_penghuni" required>
                                    <option value="">Pilih Penghuni & Kamar</option>
                                    <?php
                                    $sql = "SELECT kp.id, p.nama as nama_penghuni, km.nomor as nomor_kamar, km.harga,
                                                  COALESCE(SUM(b.harga), 0) as total_barang
                                           FROM tb_kmr_penghuni kp
                                           JOIN tb_penghuni p ON kp.id_penghuni = p.id
                                           JOIN tb_kamar km ON kp.id_kamar = km.id
                                           LEFT JOIN tb_brng_bawaan bb ON p.id = bb.id_penghuni
                                           LEFT JOIN tb_barang b ON bb.id_barang = b.id
                                           WHERE kp.tgl_keluar IS NULL
                                           GROUP BY kp.id, p.nama, km.nomor, km.harga
                                           ORDER BY p.nama ASC";
                                    $result = mysqli_query($conn, $sql);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $selected = ($row['id'] == $tagihan['id_kmr_penghuni']) ? 'selected' : '';
                                        $total_tagihan = $row['harga'] + $row['total_barang'];
                                        echo '<option value="' . $row['id'] . '" data-harga="' . $row['harga'] . '" data-barang="' . $row['total_barang'] . '" ' . $selected . '>';
                                        echo htmlspecialchars($row['nama_penghuni']) . ' - Kamar ' . htmlspecialchars($row['nomor_kamar']) . ' (Sewa: Rp ' . number_format($row['harga'], 0, ',', '.') . ' + Barang: Rp ' . number_format($row['total_barang'], 0, ',', '.') . ')';
                                        echo '</option>';
                                    }
                                    ?>
                                </select>
                                <div class="form-text">Pilih penghuni yang masih aktif huni</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="jml_tagihan" class="form-label">Jumlah Tagihan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="jml_tagihan" name="jml_tagihan" 
                                           placeholder="Masukkan jumlah tagihan" required
                                           value="<?php echo htmlspecialchars($tagihan['jml_tagihan']); ?>">
                                </div>
                                <div class="form-text">Jumlah tagihan untuk bulan tersebut</div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="tagihan.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="edit_tagihan" class="btn btn-warning">Update Tagihan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Auto-fill total tagihan ketika penghuni dipilih
    document.getElementById('id_kmr_penghuni').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const harga = selectedOption.getAttribute('data-harga');
        const barang = selectedOption.getAttribute('data-barang') || 0;
        if (harga) {
            const total = parseInt(harga) + parseInt(barang);
            document.getElementById('jml_tagihan').value = total;
        }
    });
    </script>
</body>
</html> 