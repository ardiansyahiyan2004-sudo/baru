<?php
require '../config.php';
require '../functions.php';
cekLogin();
cekRole(['petugas']);

$id_login = $_SESSION['id_login'];
$qPetugas = mysqli_query($conn, "SELECT * FROM petugas WHERE id_login=$id_login LIMIT 1");
$petugas = mysqli_fetch_assoc($qPetugas);
$id_petugas = $petugas ? $petugas['id_petugas'] : 0;

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_barang'])) {
    $kode_resi = mysqli_real_escape_string($conn, $_POST['kode_resi']);
    $nama_pengirim = mysqli_real_escape_string($conn, $_POST['nama_pengirim']);
    $alamat_pengirim = mysqli_real_escape_string($conn, $_POST['alamat_pengirim']);
    $nama_penerima = mysqli_real_escape_string($conn, $_POST['nama_penerima']);
    $alamat_penerima = mysqli_real_escape_string($conn, $_POST['alamat_penerima']);
    $no_hp_penerima = mysqli_real_escape_string($conn, $_POST['no_hp_penerima']);
    $berat = (float)$_POST['berat'];
    $jenis_barang = mysqli_real_escape_string($conn, $_POST['jenis_barang']);
    $id_kurir = (int)$_POST['id_kurir'];

    $qBarang = "INSERT INTO barang (kode_resi, nama_pengirim, alamat_pengirim, nama_penerima, alamat_penerima, no_hp_penerima, berat, jenis_barang, id_petugas)
                VALUES ('$kode_resi', '$nama_pengirim', '$alamat_pengirim', '$nama_penerima', '$alamat_penerima', '$no_hp_penerima', $berat, '$jenis_barang', $id_petugas)";

    if (mysqli_query($conn, $qBarang)) {
        $id_barang_baru = mysqli_insert_id($conn);
        $qPengiriman = "INSERT INTO pengiriman_barang (id_barang, id_kurir, status_pengiriman, update_oleh) VALUES ($id_barang_baru, $id_kurir, 'Menunggu Pickup', $id_login)";
        if (mysqli_query($conn, $qPengiriman)) {
            $success = 'Data barang dan pengiriman berhasil ditambahkan.';
        } else {
            $error = 'Barang tersimpan, tetapi gagal membuat data pengiriman: ' . mysqli_error($conn);
        }
    } else {
        $error = 'Gagal menyimpan data barang: ' . mysqli_error($conn);
    }
}

$tgl_awal = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : '';
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : '';

$where = "WHERE 1=1";
if ($tgl_awal && $tgl_akhir) {
    $where .= " AND DATE(b.tgl_input) BETWEEN '$tgl_awal' AND '$tgl_akhir'";
}

$sql = "SELECT b.*, k.nama_kurir, pb.status_pengiriman
        FROM barang b
        LEFT JOIN pengiriman_barang pb ON b.id_barang = pb.id_barang
        LEFT JOIN kurir k ON pb.id_kurir = k.id_kurir
        $where
        ORDER BY b.id_barang DESC";
$data = mysqli_query($conn, $sql);

$kurir = mysqli_query($conn, "SELECT * FROM kurir WHERE status='aktif'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">Petugas - Sistem Pengiriman</a>
    <div class="d-flex">
      <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Kelola Data Barang</h3>
        <a href="dashboard.php" class="btn btn-outline-success btn-sm">Kembali ke Dashboard</a>
    </div>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">Tambah Barang</div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="tambah_barang" value="1">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kode Resi</label>
                        <input type="text" name="kode_resi" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Pengirim</label>
                        <input type="text" name="nama_pengirim" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Penerima</label>
                        <input type="text" name="nama_penerima" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat Pengirim</label>
                        <textarea name="alamat_pengirim" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Alamat Penerima</label>
                        <textarea name="alamat_penerima" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No HP Penerima</label>
                        <input type="text" name="no_hp_penerima" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Berat (kg)</label>
                        <input type="number" step="0.01" name="berat" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Jenis Barang</label>
                        <input type="text" name="jenis_barang" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kurir</label>
                        <select name="id_kurir" class="form-select" required>
                            <option value="">Pilih Kurir</option>
                            <?php while($k = mysqli_fetch_assoc($kurir)): ?>
                                <option value="<?php echo $k['id_kurir']; ?>"><?php echo $k['nama_kurir']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Filter Periode</div>
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tanggal Awal</label>
                    <input type="date" name="tgl_awal" class="form-control" value="<?php echo $tgl_awal; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" class="form-control" value="<?php echo $tgl_akhir; ?>">
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-success">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <h5 class="mt-4 mb-3">Data Barang</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-success text-white">
                <tr>
                    <th><i class="bi bi-upc-scan me-1"></i>Resi</th>
                    <th><i class="bi bi-person-lines-fill me-1"></i>Penerima</th>
                    <th><i class="bi bi-geo-alt me-1"></i>Alamat</th>
                    <th><i class="bi bi-truck me-1"></i>Kurir</th>
                    <th><i class="bi bi-circle-half me-1"></i>Status</th>
                    <th><i class="bi bi-clock-history me-1"></i>Tgl Input</th>
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
                    <td><?php echo $row['tgl_input']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
