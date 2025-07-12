<?php
include '../inc/db.php';
include '../inc/functions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin - Aplikasi Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Kos</a>
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
                    <li class="nav-item"><a class="nav-link" href="bayar.php">Pembayaran</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container py-5">
        <div class="mb-3 text-end">
            <a href="../index.php" class="btn btn-outline-primary btn-sm" target="_blank">
                Lihat Halaman Depan
            </a>
        </div>
        <h2 class="mb-4">Dashboard Admin</h2>
        
        <!-- Statistik Dashboard -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Kamar</h5>
                        <h3 class="card-text">
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM tb_kamar";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total'];
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Kamar Terisi</h5>
                        <h3 class="card-text">
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM tb_kmr_penghuni WHERE tgl_keluar IS NULL";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total'];
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Tagihan Belum Lunas</h5>
                        <h3 class="card-text">
                            <?php
                            // Perbaiki query agar tidak error di MySQL
                            $sql = "SELECT t.id FROM tb_tagihan t
                                    LEFT JOIN tb_bayar b ON t.id = b.id_tagihan
                                    GROUP BY t.id, t.jml_tagihan
                                    HAVING COALESCE(SUM(b.jml_bayar), 0) < t.jml_tagihan";
                            $result = mysqli_query($conn, $sql);
                            if (!$result) {
                                echo '<span style=\'font-size:12px\'>' . mysqli_error($conn) . '</span>';
                            } else {
                                echo mysqli_num_rows($result);
                            }
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Penghuni</h5>
                        <h3 class="card-text">
                            <?php
                            $sql = "SELECT COUNT(DISTINCT p.id) as total FROM tb_penghuni p 
                                   JOIN tb_kmr_penghuni kp ON p.id = kp.id_penghuni 
                                   WHERE kp.tgl_keluar IS NULL";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total'];
                            ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informasi Kos (Sama dengan Halaman Depan) -->
        <div class="row mb-4">
            <!-- Kamar Kosong -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Kamar Tersedia</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $kamar_kosong = get_kamar_kosong($conn);
                        if (count($kamar_kosong) > 0) {
                            echo '<ul class="list-group list-group-flush">';
                            foreach ($kamar_kosong as $kamar) {
                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                echo '<span>Kamar ' . htmlspecialchars($kamar['nomor']) . '</span>';
                                echo '<span class="badge bg-success">Rp ' . number_format($kamar['harga'], 0, ',', '.') . '</span>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p class="text-muted mb-0">Tidak ada kamar kosong</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Kamar yang Sebentar Lagi Harus Bayar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">Harus Bayar Segera</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql = "SELECT p.nama, km.nomor, DATEDIFF(CURDATE(), p.tgl_masuk) as hari_masuk
                               FROM tb_kmr_penghuni kp
                               JOIN tb_penghuni p ON kp.id_penghuni = p.id
                               JOIN tb_kamar km ON kp.id_kamar = km.id
                               WHERE kp.tgl_keluar IS NULL 
                               AND p.tgl_keluar IS NULL
                               AND DATEDIFF(CURDATE(), p.tgl_masuk) BETWEEN 25 AND 30
                               ORDER BY p.tgl_masuk ASC
                               LIMIT 5";
                        $result = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<ul class="list-group list-group-flush">';
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                echo '<span>Kamar ' . htmlspecialchars($row['nomor']) . '</span>';
                                echo '<span class="badge bg-warning">' . $row['hari_masuk'] . ' hari</span>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p class="text-muted mb-0">Tidak ada yang harus bayar</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            
            <!-- Kamar yang Terlambat Bayar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">Terlambat Bayar</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $sql = "SELECT p.nama, km.nomor, 
                                      (t.jml_tagihan - COALESCE(SUM(b.jml_bayar), 0)) as sisa_tagihan
                               FROM tb_tagihan t
                               JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id
                               JOIN tb_penghuni p ON kp.id_penghuni = p.id
                               JOIN tb_kamar km ON kp.id_kamar = km.id
                               LEFT JOIN tb_bayar b ON t.id = b.id_tagihan
                               WHERE kp.tgl_keluar IS NULL 
                               AND p.tgl_keluar IS NULL
                               GROUP BY t.id
                               HAVING sisa_tagihan > 0
                               ORDER BY sisa_tagihan DESC
                               LIMIT 5";
                        $result = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<ul class="list-group list-group-flush">';
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                echo '<span>Kamar ' . htmlspecialchars($row['nomor']) . '</span>';
                                echo '<span class="badge bg-danger">Rp ' . number_format($row['sisa_tagihan'], 0, ',', '.') . '</span>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p class="text-muted mb-0">Tidak ada yang terlambat</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tagihan Terbaru -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tagihan Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Penghuni</th>
                                        <th>Kamar</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT t.*, p.nama as nama_penghuni, km.nomor as nomor_kamar,
                                                  km.harga as harga_kamar,
                                                  COALESCE(SUM(bb_barang.harga), 0) as total_barang
                                           FROM tb_tagihan t
                                           JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id
                                           JOIN tb_penghuni p ON kp.id_penghuni = p.id
                                           JOIN tb_kamar km ON kp.id_kamar = km.id
                                           LEFT JOIN tb_brng_bawaan bb ON p.id = bb.id_penghuni
                                           LEFT JOIN tb_barang bb_barang ON bb.id_barang = bb_barang.id
                                           GROUP BY t.id
                                           ORDER BY t.bulan DESC, t.id DESC
                                           LIMIT 5";
                                    $result = mysqli_query($conn, $sql);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $status_info = get_status_tagihan($conn, $row['id']);
                                            echo '<tr>';
                                            echo '<td>' . format_bulan($row['bulan']) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['nama_penghuni']) . '</td>';
                                            echo '<td>Kamar ' . htmlspecialchars($row['nomor_kamar']) . '</td>';
                                            echo '<td>Rp ' . number_format($row['jml_tagihan'], 0, ',', '.') . '</td>';
                                            echo '<td><span class="badge bg-' . $status_info['class'] . '">' . $status_info['status'] . '</span></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center">Belum ada data tagihan.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="tagihan.php" class="btn btn-sm btn-primary">Lihat Semua Tagihan</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Pembayaran Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Penghuni</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT b.*, p.nama as nama_penghuni
                                           FROM tb_bayar b
                                           JOIN tb_tagihan t ON b.id_tagihan = t.id
                                           JOIN tb_kmr_penghuni kp ON t.id_kmr_penghuni = kp.id
                                           JOIN tb_penghuni p ON kp.id_penghuni = p.id
                                           ORDER BY b.id DESC
                                           LIMIT 5";
                                    $result = mysqli_query($conn, $sql);
                                    if (mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $status_class = ($row['status'] == 'lunas') ? 'success' : 'warning';
                                            echo '<tr>';
                                            echo '<td>' . date('d/m/Y', strtotime($row['tgl_bayar'])) . '</td>';
                                            echo '<td>' . htmlspecialchars($row['nama_penghuni']) . '</td>';
                                            echo '<td>Rp ' . number_format($row['jml_bayar'], 0, ',', '.') . '</td>';
                                            echo '<td><span class="badge bg-' . $status_class . '">' . ucfirst($row['status']) . '</span></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center">Belum ada data pembayaran.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="bayar.php" class="btn btn-sm btn-primary">Lihat Semua Pembayaran</a>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Menu Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="bayar_tambah.php" class="btn btn-success">Tambah Pembayaran</a>
                            <a href="generate_tagihan.php" class="btn btn-info">Generate Tagihan</a>
                            <a href="tagihan_tambah.php" class="btn btn-warning">Tambah Tagihan</a>
                            <a href="kamar_tambah.php" class="btn btn-primary">Tambah Kamar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
