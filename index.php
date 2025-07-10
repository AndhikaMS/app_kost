<?php
include 'inc/db.php';
include 'inc/functions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Kamar Kosong - Aplikasi Kos</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4 text-center">Daftar Kamar Kosong</h1>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        Kamar Tersedia
                    </div>
                    <div class="card-body">
                        <?php
                        $kamar_kosong = get_kamar_kosong($conn);
                        if (count($kamar_kosong) > 0) {
                            echo '<ul class="list-group">';
                            foreach ($kamar_kosong as $kamar) {
                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                echo '<span>Nomor: <strong>' . htmlspecialchars($kamar['nomor']) . '</strong></span>';
                                echo '<span class="badge bg-success">Rp ' . number_format($kamar['harga'], 0, ',', '.') . '</span>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<div class="alert alert-info mb-0">Tidak ada kamar kosong saat ini.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
