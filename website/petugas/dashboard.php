<?php
require '../config.php';
require '../functions.php';
cekLogin();
cekRole(['petugas']);

// Statistik untuk dashboard petugas
$total_barang = 0;
$barang_hari_ini = 0;
$total_pengiriman = 0;

$resTotalBarang = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM barang");
$rowTotalBarang = $resTotalBarang ? mysqli_fetch_assoc($resTotalBarang) : ['jml' => 0];
$total_barang = (int)$rowTotalBarang['jml'];

$resHariIni = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM barang WHERE DATE(tgl_input) = CURDATE()");
$rowHariIni = $resHariIni ? mysqli_fetch_assoc($resHariIni) : ['jml' => 0];
$barang_hari_ini = (int)$rowHariIni['jml'];

$resPengiriman = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang");
$rowPengiriman = $resPengiriman ? mysqli_fetch_assoc($resPengiriman) : ['jml' => 0];
$total_pengiriman = (int)$rowPengiriman['jml'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: url('https://images.pexels.com/photos/4484078/pexels-photo-4484078.jpeg?auto=compress&cs=tinysrgb&w=1600') no-repeat center center fixed;
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
        .feature-card { border-radius: 12px; }
        .petugas-hero-panel {
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
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="https://cdn-icons-png.flaticon.com/512/3138/3138297.png" alt="Logo" width="32" height="32" class="me-2">
      <span>Petugas - Sistem Pengiriman</span>
    </a>
    <div class="d-flex">
      <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h3>Selamat datang, Petugas</h3>
            
            <div class="row g-3 mt-2">
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm text-white" style="border-radius:12px; background: linear-gradient(135deg,#198754,#20c997);">
                        <div class="card-body">
                            <small>Total Barang</small>
                            <h5 class="mb-0"><?php echo $total_barang; ?> item</h5>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm text-white" style="border-radius:12px; background: linear-gradient(135deg,#0d6efd,#20c997);">
                        <div class="card-body">
                            <small>Barang Hari Ini</small>
                            <h5 class="mb-0"><?php echo $barang_hari_ini; ?> item</h5>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card border-0 shadow-sm text-white" style="border-radius:12px; background: linear-gradient(135deg,#6610f2,#0d6efd);">
                        <div class="card-body">
                            <small>Total Pengiriman</small>
                            <h5 class="mb-0"><?php echo $total_pengiriman; ?> pengiriman</h5>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-4 mb-3">Menu Utama</h5>
            <div class="row mb-3 g-3">
                <div class="col-md-4">
                    <div class="card feature-card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Input Barang Masuk</h5>
                            <p class="card-text">Catat paket baru, data pengirim, dan penerima.</p>
                            <a href="barang.php" class="btn btn-success btn-sm">Buka</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card border-0 shadow-sm">
                        
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card border-0 shadow-sm">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
