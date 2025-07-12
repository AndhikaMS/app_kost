<?php
include '../inc/db.php';
include '../inc/functions.php';

$pesan = '';

// Proses tambah tagihan
if (isset($_POST['tambah_tagihan'])) {
    $bulan = mysqli_real_escape_string($conn, $_POST['bulan']);
    $id_kmr_penghuni = (int) $_POST['id_kmr_penghuni'];
    $jml_tagihan = (int) $_POST['jml_tagihan'];
    
    // Validasi input
    if ($bulan && $id_kmr_penghuni > 0 && $jml_tagihan > 0) {
        // Cek apakah tagihan untuk bulan dan hunian yang sama sudah ada
        $check_sql = "SELECT COUNT(*) as count FROM tb_tagihan t 
                      JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id 
                      WHERE t.bulan = '$bulan' AND kp.id = $id_kmr_penghuni";
        $check_result = mysqli_query($conn, $check_sql);
        $check_row = mysqli_fetch_assoc($check_result);
        
        if ($check_row['count'] > 0) {
            $pesan = '<div class="alert alert-warning">Tagihan untuk bulan dan penghuni tersebut sudah ada.</div>';
        } else {
            $sql = "INSERT INTO tb_tagihan (bulan, id_kmr_penghuni, jml_tagihan) VALUES ('$bulan', $id_kmr_penghuni, $jml_tagihan)";
            if (mysqli_query($conn, $sql)) {
                header('Location: tagihan.php?msg=sukses');
                exit;
            } else {
                $pesan = '<div class="alert alert-danger">Gagal menambah tagihan.</div>';
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
    <title>Tambah Tagihan - Admin Kos</title>
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
                        <h4 class="mb-0">Tambah Tagihan Baru</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $pesan; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Bulan Tagihan</label>
                                <input type="month" class="form-control" id="bulan" name="bulan" required 
                                       value="<?php echo date('Y-m'); ?>">
                                <div class="form-text">Pilih bulan untuk tagihan</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="id_kmr_penghuni" class="form-label">Penghuni & Kamar</label>
                                <select class="form-control" id="id_kmr_penghuni" name="id_kmr_penghuni" required>
                                    <option value="">Pilih Penghuni & Kamar</option>
                                    <?php
                                    $sql = "SELECT kp.id, p.nama as nama_penghuni, km.nomor as nomor_kamar, km.harga
                                           FROM tb_kmr_penghuni kp
                                           JOIN tb_penghuni p ON kp.id_penghuni = p.id
                                           JOIN tb_kamar km ON kp.id_kamar = km.id
                                           WHERE kp.tgl_keluar IS NULL
                                           ORDER BY p.nama ASC";
                                    $result = mysqli_query($conn, $sql);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row['id'] . '" data-harga="' . $row['harga'] . '">';
                                        echo htmlspecialchars($row['nama_penghuni']) . ' - Kamar ' . htmlspecialchars($row['nomor_kamar']);
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
                                           placeholder="Masukkan jumlah tagihan" required>
                                </div>
                                <div class="form-text">Jumlah tagihan untuk bulan tersebut</div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="tagihan.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="tambah_tagihan" class="btn btn-success">Simpan Tagihan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Auto-fill harga kamar ketika penghuni dipilih
    document.getElementById('id_kmr_penghuni').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const harga = selectedOption.getAttribute('data-harga');
        if (harga) {
            document.getElementById('jml_tagihan').value = harga;
        }
    });
    </script>
</body>
</html> 