<?php
include 'inc/db.php';
include 'inc/functions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Informasi Kos - Aplikasi Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Informasi Kos</h1>
        
        <!-- Kamar Kosong -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Kamar Tersedia</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $kamar_kosong = get_kamar_kosong($conn);
                        if (count($kamar_kosong) > 0) {
                            echo '<div class="row">';
                            foreach ($kamar_kosong as $kamar) {
                                echo '<div class="col-md-4 mb-3">';
                                echo '<div class="card border-success">';
                                echo '<div class="card-body text-center">';
                                echo '<h5 class="card-title">Kamar ' . htmlspecialchars($kamar['nomor']) . '</h5>';
                                echo '<p class="card-text text-success fw-bold">Rp ' . number_format($kamar['harga'], 0, ',', '.') . '/bulan</p>';
                                echo '<span class="badge bg-success">Tersedia</span>';
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-info mb-0">Tidak ada kamar kosong saat ini.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Kamar yang Sebentar Lagi Harus Bayar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Kamar yang Sebentar Lagi Harus Bayar</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Kamar yang penghuninya masuk 25-30 hari yang lalu (sebentar lagi harus bayar)
                        $sql = "SELECT p.nama, km.nomor, p.tgl_masuk, 
                                      DATEDIFF(CURDATE(), p.tgl_masuk) as hari_masuk
                               FROM tb_kmr_penghuni kp
                               JOIN tb_penghuni p ON kp.id_penghuni = p.id
                               JOIN tb_kamar km ON kp.id_kamar = km.id
                               WHERE kp.tgl_keluar IS NULL 
                               AND p.tgl_keluar IS NULL
                               AND DATEDIFF(CURDATE(), p.tgl_masuk) BETWEEN 25 AND 30
                               ORDER BY p.tgl_masuk ASC";
                        $result = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-warning table-striped">';
                            echo '<thead><tr><th>Kamar</th><th>Penghuni</th><th>Tanggal Masuk</th><th>Hari ke-</th></tr></thead>';
                            echo '<tbody>';
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td><strong>Kamar ' . htmlspecialchars($row['nomor']) . '</strong></td>';
                                echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                                echo '<td>' . tgl_indo($row['tgl_masuk']) . '</td>';
                                echo '<td><span class="badge bg-warning">' . $row['hari_masuk'] . ' hari</span></td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-warning mb-0">Tidak ada kamar yang sebentar lagi harus bayar.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Kamar yang Terlambat Bayar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Kamar yang Terlambat Bayar</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Kamar dengan tagihan yang belum lunas
                        $sql = "SELECT p.nama, km.nomor, t.bulan, t.jml_tagihan,
                                      COALESCE(SUM(b.jml_bayar), 0) as total_bayar,
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
                               ORDER BY sisa_tagihan DESC";
                        $result = mysqli_query($conn, $sql);
                        
                        if (mysqli_num_rows($result) > 0) {
                            echo '<div class="table-responsive">';
                            echo '<table class="table table-danger table-striped">';
                            echo '<thead><tr><th>Kamar</th><th>Penghuni</th><th>Bulan Tagihan</th><th>Total Tagihan</th><th>Total Bayar</th><th>Sisa</th></tr></thead>';
                            echo '<tbody>';
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td><strong>Kamar ' . htmlspecialchars($row['nomor']) . '</strong></td>';
                                echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                                echo '<td>' . format_bulan($row['bulan']) . '</td>';
                                echo '<td>Rp ' . number_format($row['jml_tagihan'], 0, ',', '.') . '</td>';
                                echo '<td>Rp ' . number_format($row['total_bayar'], 0, ',', '.') . '</td>';
                                echo '<td><span class="badge bg-danger">Rp ' . number_format($row['sisa_tagihan'], 0, ',', '.') . '</span></td>';
                                echo '</tr>';
                            }
                            echo '</tbody></table>';
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-success mb-0">Tidak ada kamar yang terlambat bayar.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Link ke Admin -->
        <div class="row">
            <div class="col-md-12 text-center">
                <a href="admin/dashboard.php" class="btn btn-primary btn-lg">Akses Admin</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
