<?php
require '../config.php';
require '../functions.php';
cekLogin();
cekRole(['admin']);

// Statistik untuk dashboard admin
$total_admin   = 0;
$total_petugas = 0;
$total_kurir   = 0;
$pengiriman_aktif = 0;
$pengiriman_selesai = 0;

$resAdmin   = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM admin");
$rowAdmin   = $resAdmin ? mysqli_fetch_assoc($resAdmin) : ['jml' => 0];
$total_admin = (int)$rowAdmin['jml'];

$resPetugas = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM petugas");
$rowPetugas = $resPetugas ? mysqli_fetch_assoc($resPetugas) : ['jml' => 0];
$total_petugas = (int)$rowPetugas['jml'];

$resKurir   = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM kurir");
$rowKurir   = $resKurir ? mysqli_fetch_assoc($resKurir) : ['jml' => 0];
$total_kurir = (int)$rowKurir['jml'];

$resAktif = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang WHERE status_pengiriman IN ('Menunggu Pickup','Sedang Diantar')");
$rowAktif = $resAktif ? mysqli_fetch_assoc($resAktif) : ['jml' => 0];
$pengiriman_aktif = (int)$rowAktif['jml'];

$resSelesai = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang WHERE status_pengiriman = 'Terkirim'");
$rowSelesai = $resSelesai ? mysqli_fetch_assoc($resSelesai) : ['jml' => 0];
$pengiriman_selesai = (int)$rowSelesai['jml'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: url('https://images.pexels.com/photos/669610/pexels-photo-669610.jpeg?auto=compress&cs=tinysrgb&w=1600') no-repeat center center fixed;
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
        .stat-card {
            border-radius: 12px;
        }
        .feature-card-admin {
            border-radius: 12px;
            border: 0;
            box-shadow: 0 0.5rem 1rem rgba(15,23,42,0.06);
        }
        .admin-hero-panel {
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
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="https://cdn-icons-png.flaticon.com/512/1048/1048310.png" alt="Logo" width="32" height="32" class="me-2">
      <span>Admin - Sistem Pengiriman</span>
    </a>
    <div class="d-flex">
      <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h3>Selamat datang, Admin</h3>
            
            <div class="row g-3 mt-2">
                <div class="col-sm-4">
                    <div class="card stat-card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body">
                            <small>Total Pengguna</small>
                            <h5 class="mb-0"><?php echo $total_admin + $total_petugas + $total_kurir; ?> user</h5>
                            <small>Admin: <?php echo $total_admin; ?> | Petugas: <?php echo $total_petugas; ?> | Kurir: <?php echo $total_kurir; ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card stat-card border-0 shadow-sm bg-success text-white">
                        <div class="card-body">
                            <small>Pengiriman Aktif</small>
                            <h5 class="mb-0"><?php echo $pengiriman_aktif; ?> paket</h5>
                            <small>Menunggu / Sedang Diantar</small>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="card stat-card border-0 shadow-sm bg-info text-white">
                        <div class="card-body">
                            <small>Pengiriman Selesai</small>
                            <h5 class="mb-0"><?php echo $pengiriman_selesai; ?> paket</h5>
                            <small>Status Terkirim</small>
                        </div>
                    </div>
                </div>
            </div>
            <h5 class="mt-4 mb-3">Menu Utama</h5>
            <div class="row g-3">
                <div class="col-md-4 mb-3">
                    <div class="card feature-card-admin">
                        <div class="card-body">
                            <h5 class="card-title">Kelola Pengguna</h5>
                            <p class="card-text">Tambah / edit / hapus petugas, dan kurir.</p>
                            <a href="pengguna.php" class="btn btn-sm btn-primary mt-2">Buka</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card feature-card-admin">
                        <div class="card-body">
                            <h5 class="card-title">Kelola Pengiriman</h5>
                            <p class="card-text">Lihat dan kelola semua data barang dan pengiriman.</p>
                            <a href="pengiriman.php" class="btn btn-sm btn-primary mt-2">Buka</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card feature-card-admin">
                        <div class="card-body">
                            <h5 class="card-title">Laporan </h5>
                            <p class="card-text">Lihat laporan data pengiriman dan status paket.</p>
                            <a href="laporan.php" class="btn btn-sm btn-primary mt-2">Buka Laporan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
