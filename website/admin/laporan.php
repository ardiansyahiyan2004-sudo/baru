<?php
require '../config.php';
require '../functions.php';
cekLogin();
cekRole(['admin']);

// Ambil statistik pengiriman
$totals = [
    'total' => 0,
    'menunggu' => 0,
    'diantar' => 0,
    'terkirim' => 0,
    'gagal' => 0,
];

$resTotal = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang");
$rowTotal = $resTotal ? mysqli_fetch_assoc($resTotal) : ['jml' => 0];
$totals['total'] = (int)$rowTotal['jml'];

$resMenunggu = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang WHERE status_pengiriman='Menunggu Pickup'");
$rowMenunggu = $resMenunggu ? mysqli_fetch_assoc($resMenunggu) : ['jml' => 0];
$totals['menunggu'] = (int)$rowMenunggu['jml'];

$resDiantar = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang WHERE status_pengiriman='Sedang Diantar'");
$rowDiantar = $resDiantar ? mysqli_fetch_assoc($resDiantar) : ['jml' => 0];
$totals['diantar'] = (int)$rowDiantar['jml'];

$resTerkirim = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang WHERE status_pengiriman='Terkirim'");
$rowTerkirim = $resTerkirim ? mysqli_fetch_assoc($resTerkirim) : ['jml' => 0];
$totals['terkirim'] = (int)$rowTerkirim['jml'];

$resGagal = mysqli_query($conn, "SELECT COUNT(*) AS jml FROM pengiriman_barang WHERE status_pengiriman='Gagal'");
$rowGagal = $resGagal ? mysqli_fetch_assoc($resGagal) : ['jml' => 0];
$totals['gagal'] = (int)$rowGagal['jml'];

// Data detail pengiriman untuk tabel ringkasan
$sql = "SELECT pb.*, b.kode_resi, b.nama_penerima, b.alamat_penerima, k.nama_kurir
        FROM pengiriman_barang pb
        JOIN barang b ON pb.id_barang = b.id_barang
        JOIN kurir k ON pb.id_kurir = k.id_kurir
        ORDER BY pb.id_pengiriman DESC";
$data = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Singkat Pengiriman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f3f4f6;
        }
        h3, h4, h5 {
            font-weight: 600;
        }
        .stat-card {
            border-radius: 12px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: #ffffff;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary no-print">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Admin - Laporan Pengiriman</a>
    <div class="d-flex">
      <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Kembali ke Dashboard</a>
      <button onclick="window.print()" class="btn btn-light btn-sm">Print</button>
    </div>
  </div>
</nav>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Laporan Singkat Pengiriman</h3>
        <span class="text-muted small">Dicetak pada: <?php echo date('d-m-Y H:i'); ?></span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <small>Total Pengiriman</small>
                    <h5 class="mb-0"><?php echo $totals['total']; ?> pengiriman</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <small>Menunggu Pickup</small>
                    <h5 class="mb-0"><?php echo $totals['menunggu']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm bg-info text-dark">
                <div class="card-body">
                    <small>Sedang Diantar</small>
                    <h5 class="mb-0"><?php echo $totals['diantar']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <small>Terkirim</small>
                    <h5 class="mb-0"><?php echo $totals['terkirim']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-0 shadow-sm bg-danger text-white mt-3 mt-md-0">
                <div class="card-body">
                    <small>Gagal</small>
                    <h5 class="mb-0"><?php echo $totals['gagal']; ?></h5>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">Ringkasan Data Pengiriman</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Resi</th>
                    <th>Penerima</th>
                    <th>Alamat</th>
                    <th>Kurir</th>
                    <th>Status</th>
                    <th>Tgl Kirim</th>
                    <th>Tgl Terima</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($data && mysqli_num_rows($data) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?php echo $row['kode_resi']; ?></td>
                        <td><?php echo $row['nama_penerima']; ?></td>
                        <td><?php echo $row['alamat_penerima']; ?></td>
                        <td><?php echo $row['nama_kurir']; ?></td>
                        <td><?php echo $row['status_pengiriman']; ?></td>
                        <td><?php echo $row['tgl_kirim']; ?></td>
                        <td><?php echo $row['tgl_terima']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Belum ada data pengiriman.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
