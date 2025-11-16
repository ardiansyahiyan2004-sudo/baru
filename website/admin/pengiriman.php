<?php
require '../config.php';
require '../functions.php';
cekLogin();
cekRole(['admin']);

$success = '';
$error = '';

if (isset($_POST['update_status'])) {
    $id_pengiriman = (int)$_POST['id_pengiriman'];
    $status = mysqli_real_escape_string($conn, $_POST['status_pengiriman']);

    $tgl_kirim = 'NULL';
    $tgl_terima = 'NULL';
    if ($status === 'Sedang Diantar') {
        $tgl_kirim = 'NOW()';
    } elseif ($status === 'Terkirim') {
        $tgl_terima = 'NOW()';
    }

    $q = "UPDATE pengiriman_barang SET status_pengiriman='$status', tgl_kirim=IF($tgl_kirim IS NULL, tgl_kirim, $tgl_kirim), tgl_terima=IF($tgl_terima IS NULL, tgl_terima, $tgl_terima) WHERE id_pengiriman=$id_pengiriman";
    if (mysqli_query($conn, $q)) {
        $success = 'Status pengiriman berhasil diperbarui.';
    } else {
        $error = 'Gagal memperbarui status: ' . mysqli_error($conn);
    }
}

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
    <title>Kelola Pengiriman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Admin - Sistem Pengiriman</a>
    <div class="d-flex">
      <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Kelola Pengiriman Barang</h3>
        <a href="dashboard.php" class="btn btn-outline-primary btn-sm">Kembali ke Dashboard</a>
    </div>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-primary text-white">
                <tr>
                    <th><i class="bi bi-upc-scan me-1"></i>Resi</th>
                    <th><i class="bi bi-person-lines-fill me-1"></i>Penerima</th>
                    <th><i class="bi bi-geo-alt me-1"></i>Alamat</th>
                    <th><i class="bi bi-truck me-1"></i>Kurir</th>
                    <th><i class="bi bi-circle-half me-1"></i>Status</th>
                    <th><i class="bi bi-sliders me-1"></i>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($data)): ?>
                <tr>
                    <td><?php echo $row['kode_resi']; ?></td>
                    <td><?php echo $row['nama_penerima']; ?></td>
                    <td><?php echo $row['alamat_penerima']; ?></td>
                    <td><?php echo $row['nama_kurir']; ?></td>
                    <td>
                        <?php
                        $status = $row['status_pengiriman'];
                        $badgeClass = 'bg-secondary';
                        if ($status === 'Menunggu Pickup') $badgeClass = 'bg-warning text-dark';
                        elseif ($status === 'Sedang Diantar') $badgeClass = 'bg-info text-dark';
                        elseif ($status === 'Terkirim') $badgeClass = 'bg-success';
                        elseif ($status === 'Gagal') $badgeClass = 'bg-danger';
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                    </td>
                    <td>
                    <form method="post" class="d-flex gap-2">
                        <input type="hidden" name="id_pengiriman" value="<?php echo $row['id_pengiriman']; ?>">
                        <select name="status_pengiriman" class="form-select form-select-sm">
                            <option value="Menunggu Pickup" <?php if($row['status_pengiriman']=='Menunggu Pickup') echo 'selected'; ?>>Menunggu Pickup</option>
                            <option value="Sedang Diantar" <?php if($row['status_pengiriman']=='Sedang Diantar') echo 'selected'; ?>>Sedang Diantar</option>
                            <option value="Terkirim" <?php if($row['status_pengiriman']=='Terkirim') echo 'selected'; ?>>Terkirim</option>
                            <option value="Gagal" <?php if($row['status_pengiriman']=='Gagal') echo 'selected'; ?>>Gagal</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
