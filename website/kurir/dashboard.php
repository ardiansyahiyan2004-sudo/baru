<?php
require '../config.php';
require '../functions.php';
cekLogin();
cekRole(['kurir']);

// Statistik untuk dashboard kurir
$id_login = $_SESSION['id_login'];
$qKurir = mysqli_query($conn, "SELECT * FROM kurir WHERE id_login=$id_login LIMIT 1");
$kurir = $qKurir ? mysqli_fetch_assoc($qKurir) : null;
$id_kurir = $kurir ? (int)$kurir['id_kurir'] : 0;

$aktif_kurir = 0;
$selesai_kurir = 0;

if ($id_kurir > 0) {
    $resAktifKurir = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang WHERE id_kurir=$id_kurir AND status_pengiriman IN ('Menunggu Pickup','Sedang Diantar')");
    $rowAktifKurir = $resAktifKurir ? mysqli_fetch_assoc($resAktifKurir) : ['jml' => 0];
    $aktif_kurir = (int)$rowAktifKurir['jml'];

    $resSelesaiKurir = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang WHERE id_kurir=$id_kurir AND status_pengiriman='Terkirim'");
    $rowSelesaiKurir = $resSelesaiKurir ? mysqli_fetch_assoc($resSelesaiKurir) : ['jml' => 0];
    $selesai_kurir = (int)$rowSelesaiKurir['jml'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kurir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: url('https://images.pexels.com/photos/4391470/pexels-photo-4391470.jpeg?auto=compress&cs=tinysrgb&w=1600') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            min-height: 100vh;
            color: #e5e7eb;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.6);
            z-index: -1;
        }
        h3, h4, h5 {
            font-weight: 600;
            color: #ffffff;
        }
        .task-card {
            border-radius: 12px;
            border: 0;
            box-shadow: 0 0.5rem 1rem rgba(15,23,42,0.06);
        }
        .kurir-hero-panel {
            min-height: 260px;
            border-radius: 16px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            box-shadow: 0 0.75rem 1.5rem rgba(15,23,42,0.2);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-warning">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="https://cdn-icons-png.flaticon.com/512/1995/1995574.png" alt="Logo" width="32" height="32" class="me-2">
      <span>Kurir - Sistem Pengiriman</span>
    </a>
    <div class="d-flex">
      <a href="../logout.php" class="btn btn-outline-dark btn-sm">Logout</a>
    </div>
  </div>
</nav>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h3>Selamat datang, Kurir</h3>
            <div class="row g-3 mt-1">
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm text-white" style="border-radius:12px; background: linear-gradient(135deg,#ffc107,#fd7e14);">
                        <div class="card-body">
                            <small>Tugas Aktif</small>
                            <h5 class="mb-0"><?php echo $aktif_kurir; ?> pengiriman</h5>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-0 shadow-sm text-white" style="border-radius:12px; background: linear-gradient(135deg,#198754,#20c997);">
                        <div class="card-body">
                            <small>Terselesaikan</small>
                            <h5 class="mb-0"><?php echo $selesai_kurir; ?> pengiriman</h5>
                        </div>
                    </div>
                </div>
            </div>
            <h5 class="mt-4 mb-3">Menu Utama</h5>
            <div class="row mb-3 g-3">
                <div class="col-md-6">
                    <div class="card task-card">
                        <div class="card-body">
                            <h5 class="card-title">Daftar Tugas Hari Ini</h5>
                            <p class="card-text">Lihat paket yang perlu diantar dan detail alamat.</p>
                            <a href="pengiriman_saya.php" class="btn btn-warning btn-sm">Lihat Pengiriman</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card task-card">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
