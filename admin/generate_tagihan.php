<?php
include '../inc/db.php';
include '../inc/functions.php';

$pesan = '';

// Proses generate tagihan
if (isset($_POST['generate_tagihan'])) {
    $bulan = mysqli_real_escape_string($conn, $_POST['bulan']);
    
    if ($bulan) {
        // Ambil semua penghuni aktif
        $sql_hunian = "SELECT kp.id, kp.id_kamar, kp.id_penghuni, km.harga
                       FROM tb_kmr_penghuni kp
                       JOIN tb_kamar km ON kp.id_kamar = km.id
                       WHERE kp.tgl_keluar IS NULL";
        $result_hunian = mysqli_query($conn, $sql_hunian);
        
        $berhasil = 0;
        $gagal = 0;
        $sudah_ada = 0;
        
        while ($hunian = mysqli_fetch_assoc($result_hunian)) {
            // Cek apakah tagihan untuk bulan dan hunian ini sudah ada
            $check_sql = "SELECT COUNT(*) as count FROM tb_tagihan 
                          WHERE bulan = '$bulan' AND id_kmr_penghuni = " . $hunian['id'];
            $check_result = mysqli_query($conn, $check_sql);
            $check_row = mysqli_fetch_assoc($check_result);
            
            if ($check_row['count'] == 0) {
                // Hitung total tagihan = harga kamar + harga barang bawaan
                $sql_barang = "SELECT COALESCE(SUM(b.harga), 0) as total_barang
                              FROM tb_brng_bawaan bb
                              JOIN tb_barang b ON bb.id_barang = b.id
                              WHERE bb.id_penghuni = " . $hunian['id_penghuni'];
                $result_barang = mysqli_query($conn, $sql_barang);
                $row_barang = mysqli_fetch_assoc($result_barang);
                $total_barang = $row_barang['total_barang'];
                
                $total_tagihan = $hunian['harga'] + $total_barang;
                
                // Generate tagihan baru
                $insert_sql = "INSERT INTO tb_tagihan (bulan, id_kmr_penghuni, jml_tagihan) 
                              VALUES ('$bulan', " . $hunian['id'] . ", $total_tagihan)";
                if (mysqli_query($conn, $insert_sql)) {
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } else {
                $sudah_ada++;
            }
        }
        
        if ($berhasil > 0) {
            $pesan = '<div class="alert alert-success">Berhasil generate ' . $berhasil . ' tagihan baru.';
            if ($sudah_ada > 0) {
                $pesan .= ' ' . $sudah_ada . ' tagihan sudah ada sebelumnya.';
            }
            if ($gagal > 0) {
                $pesan .= ' ' . $gagal . ' tagihan gagal dibuat.';
            }
            $pesan .= '</div>';
        } else {
            $pesan = '<div class="alert alert-warning">Tidak ada tagihan baru yang dibuat. Semua penghuni sudah memiliki tagihan untuk bulan ' . format_bulan($bulan) . '.</div>';
        }
    } else {
        $pesan = '<div class="alert alert-warning">Pilih bulan untuk generate tagihan.</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Generate Tagihan - Admin Kos</title>
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
                        <h4 class="mb-0">Generate Tagihan Otomatis</h4>
                    </div>
                    <div class="card-body">
                        <?php echo $pesan; ?>
                        
                        <div class="alert alert-info">
                            <strong>Fitur ini akan:</strong><br>
                            • Generate tagihan untuk semua penghuni aktif<br>
                            • Menggunakan harga sewa kamar sebagai jumlah tagihan<br>
                            • Hanya membuat tagihan untuk bulan yang belum ada<br>
                            • Tagihan akan dibuat untuk bulan yang dipilih
                        </div>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Bulan Tagihan</label>
                                <input type="month" class="form-control" id="bulan" name="bulan" required 
                                       value="<?php echo date('Y-m'); ?>">
                                <div class="form-text">Pilih bulan untuk generate tagihan</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Penghuni Aktif yang Akan Digenereate:</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                                                                            <tr>
                                                    <th>No</th>
                                                    <th>Nama Penghuni</th>
                                                    <th>Kamar</th>
                                                    <th>Harga Sewa</th>
                                                    <th>Barang Bawaan</th>
                                                    <th>Total Tagihan</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                                                         $sql = "SELECT p.nama, p.id as id_penghuni, km.nomor, km.harga,
                                                           COALESCE(SUM(b.harga), 0) as total_barang
                                                    FROM tb_kmr_penghuni kp
                                                    JOIN tb_penghuni p ON kp.id_penghuni = p.id
                                                    JOIN tb_kamar km ON kp.id_kamar = km.id
                                                    LEFT JOIN tb_brng_bawaan bb ON p.id = bb.id_penghuni
                                                    LEFT JOIN tb_barang b ON bb.id_barang = b.id
                                                    WHERE kp.tgl_keluar IS NULL
                                                    GROUP BY p.id, p.nama, km.nomor, km.harga
                                                    ORDER BY p.nama ASC";
                                            $result = mysqli_query($conn, $sql);
                                            if (mysqli_num_rows($result) > 0) {
                                                $no = 1;
                                                                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        $total_tagihan = $row['harga'] + $row['total_barang'];
                                                        echo '<tr>';
                                                        echo '<td>' . $no++ . '</td>';
                                                        echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                                                        echo '<td>Kamar ' . htmlspecialchars($row['nomor']) . '</td>';
                                                        echo '<td>Rp ' . number_format($row['harga'], 0, ',', '.') . '</td>';
                                                        echo '<td>Rp ' . number_format($row['total_barang'], 0, ',', '.') . '</td>';
                                                        echo '<td>Rp ' . number_format($total_tagihan, 0, ',', '.') . '</td>';
                                                        echo '</tr>';
                                                    }
                                            } else {
                                                echo '<tr><td colspan="6" class="text-center">Tidak ada penghuni aktif.</td></tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="tagihan.php" class="btn btn-secondary">Kembali</a>
                                <button type="submit" name="generate_tagihan" class="btn btn-primary" 
                                        onclick="return confirm('Yakin ingin generate tagihan untuk semua penghuni aktif?')">
                                    Generate Tagihan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
