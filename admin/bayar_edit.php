<?php
include '../inc/db.php';
include '../inc/functions.php';

$pesan = '';

// Ambil data pembayaran yang akan diedit
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $sql = "SELECT b.*, t.bulan, t.jml_tagihan,
                   p.nama as nama_penghuni,
                   km.nomor as nomor_kamar
            FROM tb_bayar b
            JOIN tb_tagihan t ON b.id_tagihan = t.id
            JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id
            JOIN tb_penghuni p ON kp.id_penghuni = p.id
            JOIN tb_kamar km ON kp.id_kamar = km.id
            WHERE b.id = $id";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $bayar = mysqli_fetch_assoc($result);
    } else {
        header('Location: bayar.php');
        exit;
    }
} else {
    header('Location: bayar.php');
    exit;
}

// Proses edit pembayaran
if (isset($_POST['edit_bayar'])) {
    $jml_bayar = (int) $_POST['jml_bayar'];
    $tgl_bayar = mysqli_real_escape_string($conn, $_POST['tgl_bayar']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Validasi input
    if ($jml_bayar > 0 && $tgl_bayar) {
        // Hitung total pembayaran untuk tagihan ini (termasuk pembayaran yang sedang diedit)
        $check_sql = "SELECT t.jml_tagihan, 
                             COALESCE(SUM(CASE WHEN b.id != $id THEN b.jml_bayar ELSE 0 END), 0) as total_bayar_lain
                      FROM tb_tagihan t
                      LEFT JOIN tb_bayar b ON t.id = b.id_tagihan
                      WHERE t.id = " . $bayar['id_tagihan'] . "
                      GROUP BY t.id";
        $check_result = mysqli_query($conn, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            $check_row = mysqli_fetch_assoc($check_result);
            $total_bayar = $check_row['total_bayar_lain'] + $jml_bayar;
            
            // Update status berdasarkan total pembayaran
            if ($total_bayar >= $check_row['jml_tagihan']) {
                $status = 'lunas';
            } else {
                $status = 'cicil';
            }
            
            $sql = "UPDATE tb_bayar SET jml_bayar=$jml_bayar, tgl_bayar='$tgl_bayar', status='$status' WHERE id=$id";
            if (mysqli_query($conn, $sql)) {
                header('Location: bayar.php?msg=edit_sukses');
                exit;
            } else {
                $pesan = '<div class="alert alert-danger">Gagal memperbarui pembayaran.</div>';
            }
        } else {
            $pesan = '<div class="alert alert-warning">Tagihan tidak ditemukan.</div>';
        }
    } else {
        $pesan = '<div class="alert alert-warning">Semua field harus diisi dengan benar.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Pembayaran - Admin Kos</title>
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
                    <li class="nav-item"><a class="nav-link" href="tagihan.php">Tagihan</a></li>
                    <li class="nav-item"><a class="nav-link active" href="bayar.php">Pembayaran</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Pembayaran</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $pesan; ?>
                        
                        <!-- Informasi Tagihan -->
                        <div class="alert alert-info">
                            <strong>Informasi Tagihan:</strong><br>
                            Penghuni: <?php echo htmlspecialchars($bayar['nama_penghuni']); ?><br>
                            Kamar: <?php echo htmlspecialchars($bayar['nomor_kamar']); ?><br>
                            Bulan: <?php echo format_bulan($bayar['bulan']); ?><br>
                            Total Tagihan: Rp <?php echo number_format($bayar['jml_tagihan'], 0, ',', '.'); ?>
                        </div>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="jml_bayar" class="form-label">Jumlah Bayar</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="jml_bayar" name="jml_bayar" 
                                           placeholder="Masukkan jumlah pembayaran" required
                                           value="<?php echo htmlspecialchars($bayar['jml_bayar']); ?>">
                                </div>
                                <div class="form-text">Jumlah pembayaran untuk tagihan ini</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="tgl_bayar" class="form-label">Tanggal Pembayaran</label>
                                <input type="date" class="form-control" id="tgl_bayar" name="tgl_bayar" 
                                       value="<?php echo htmlspecialchars($bayar['tgl_bayar']); ?>" required>
                                <div class="form-text">Tanggal pembayaran dilakukan</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="cicil" <?php echo ($bayar['status'] == 'cicil') ? 'selected' : ''; ?>>Cicil</option>
                                    <option value="lunas" <?php echo ($bayar['status'] == 'lunas') ? 'selected' : ''; ?>>Lunas</option>
                                </select>
                                <div class="form-text">Status akan diupdate otomatis berdasarkan jumlah pembayaran</div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="bayar.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="edit_bayar" class="btn btn-warning">Update Pembayaran</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Auto-update status berdasarkan jumlah pembayaran
    document.getElementById('jml_bayar').addEventListener('input', function() {
        const jmlBayar = parseInt(this.value || 0);
        const totalTagihan = <?php echo $bayar['jml_tagihan']; ?>;
        const bayarLain = <?php 
            $sql_lain = "SELECT COALESCE(SUM(CASE WHEN b.id != " . $bayar['id'] . " THEN b.jml_bayar ELSE 0 END), 0) as total_lain FROM tb_bayar b WHERE b.id_tagihan = " . $bayar['id_tagihan'];
            $result_lain = mysqli_query($conn, $sql_lain);
            $row_lain = mysqli_fetch_assoc($result_lain);
            echo $row_lain['total_lain'];
        ?>;
        
        const totalBayar = bayarLain + jmlBayar;
        const statusSelect = document.getElementById('status');
        
        if (totalBayar >= totalTagihan) {
            statusSelect.value = 'lunas';
        } else {
            statusSelect.value = 'cicil';
        }
    });
    </script>
</body>
</html> 