<?php
require '../config.php';
require '../functions.php';
cekLogin();
cekRole(['admin']);

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'petugas';

$success = '';
$error = '';

// handle hapus (nonaktif) pengguna
if (isset($_GET['hapus']) && isset($_GET['id'])) {
    $jenis_hapus = $_GET['hapus']; // petugas / kurir
    $id_detail = (int)$_GET['id'];

    if ($jenis_hapus === 'petugas') {
        $q = mysqli_query($conn, "SELECT l.id_login FROM petugas p JOIN login l ON p.id_login=l.id_login WHERE p.id_petugas=$id_detail");
        if ($q && mysqli_num_rows($q) === 1) {
            $row = mysqli_fetch_assoc($q);
            $id_login_hapus = (int)$row['id_login'];
            mysqli_query($conn, "UPDATE login SET status='nonaktif' WHERE id_login=$id_login_hapus");
            $success = 'Akun petugas berhasil dinonaktifkan.';
        }
    } elseif ($jenis_hapus === 'kurir') {
        $q = mysqli_query($conn, "SELECT l.id_login FROM kurir k JOIN login l ON k.id_login=l.id_login WHERE k.id_kurir=$id_detail");
        if ($q && mysqli_num_rows($q) === 1) {
            $row = mysqli_fetch_assoc($q);
            $id_login_hapus = (int)$row['id_login'];
            mysqli_query($conn, "UPDATE login SET status='nonaktif' WHERE id_login=$id_login_hapus");
            mysqli_query($conn, "UPDATE kurir SET status='nonaktif' WHERE id_kurir=$id_detail");
            $success = 'Akun kurir berhasil dinonaktifkan.';
        }
    }
}

// handle tambah / edit pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : 'tambah';
    $jenis = $_POST['jenis'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

    $role = $jenis; // sama dengan petugas/kurir

    if ($aksi === 'tambah') {
        $qLogin = "INSERT INTO login (username, password, role, status) VALUES ('$username', MD5('$password'), '$role', 'aktif')";
        if (mysqli_query($conn, $qLogin)) {
            $id_login_baru = mysqli_insert_id($conn);
            if ($jenis === 'petugas') {
                $qDetail = "INSERT INTO petugas (id_login, nama_petugas, email, no_hp) VALUES ($id_login_baru, '$nama', '$email', '$no_hp')";
            } else {
                $qDetail = "INSERT INTO kurir (id_login, nama_kurir, no_hp, alamat, status) VALUES ($id_login_baru, '$nama', '$no_hp', '', 'aktif')";
            }
            if (mysqli_query($conn, $qDetail)) {
                $success = 'Data pengguna berhasil ditambahkan.';
            } else {
                $error = 'Gagal menyimpan detail pengguna: ' . mysqli_error($conn);
            }
        } else {
            $error = 'Gagal membuat akun login: ' . mysqli_error($conn);
        }
    } elseif ($aksi === 'edit' && isset($_POST['id_login'])) {
        $id_login_edit = (int)$_POST['id_login'];

        // update login (username + optional password)
        if ($password !== '') {
            $qUpdateLogin = "UPDATE login SET username='$username', password=MD5('$password') WHERE id_login=$id_login_edit";
        } else {
            $qUpdateLogin = "UPDATE login SET username='$username' WHERE id_login=$id_login_edit";
        }

        if (mysqli_query($conn, $qUpdateLogin)) {
            if ($jenis === 'petugas') {
                $qUpdateDetail = "UPDATE petugas SET nama_petugas='$nama', email='$email', no_hp='$no_hp' WHERE id_login=$id_login_edit";
            } else {
                $qUpdateDetail = "UPDATE kurir SET nama_kurir='$nama', no_hp='$no_hp' WHERE id_login=$id_login_edit";
            }
            if (mysqli_query($conn, $qUpdateDetail)) {
                $success = 'Data pengguna berhasil diperbarui.';
            } else {
                $error = 'Gagal memperbarui detail pengguna: ' . mysqli_error($conn);
            }
        } else {
            $error = 'Gagal memperbarui akun login: ' . mysqli_error($conn);
        }
    }
}

$petugas = mysqli_query($conn, "SELECT p.*, l.username, l.id_login FROM petugas p JOIN login l ON p.id_login=l.id_login");
$kurir = mysqli_query($conn, "SELECT k.*, l.username, l.id_login FROM kurir k JOIN login l ON k.id_login=l.id_login");

// data untuk form edit
$edit_mode = false;
$edit_jenis = '';
$edit_data = null;
if (isset($_GET['edit']) && isset($_GET['id'])) {
    $edit_jenis = $_GET['edit']; // petugas / kurir
    $id_detail = (int)$_GET['id'];

    if ($edit_jenis === 'petugas') {
        $q = mysqli_query($conn, "SELECT p.*, l.username, l.id_login FROM petugas p JOIN login l ON p.id_login=l.id_login WHERE p.id_petugas=$id_detail");
    } elseif ($edit_jenis === 'kurir') {
        $q = mysqli_query($conn, "SELECT k.*, l.username, l.id_login FROM kurir k JOIN login l ON k.id_login=l.id_login WHERE k.id_kurir=$id_detail");
    }

    if (isset($q) && $q && mysqli_num_rows($q) === 1) {
        $edit_mode = true;
        $edit_data = mysqli_fetch_assoc($q);
        $tab = $edit_jenis;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna</title>
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
        <h3 class="mb-0">Kelola Pengguna</h3>
        <a href="dashboard.php" class="btn btn-outline-primary btn-sm">Kembali ke Dashboard</a>
    </div>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-3">
      <li class="nav-item">
        <a class="nav-link <?php echo $tab=='petugas'?'active':''; ?>" href="?tab=petugas">Petugas</a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?php echo $tab=='kurir'?'active':''; ?>" href="?tab=kurir">Kurir</a>
      </li>
    </ul>

    <div class="card mb-4">
        <div class="card-header"><?php echo $edit_mode ? 'Edit Pengguna' : 'Tambah Pengguna'; ?></div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="aksi" value="<?php echo $edit_mode ? 'edit' : 'tambah'; ?>">
                <?php if ($edit_mode && $edit_data): ?>
                    <input type="hidden" name="id_login" value="<?php echo $edit_data['id_login']; ?>">
                <?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis Pengguna</label>
                        <?php if ($edit_mode): ?>
                            <select class="form-select" disabled>
                                <option value="petugas" <?php if($edit_jenis==='petugas') echo 'selected'; ?>>Petugas</option>
                                <option value="kurir" <?php if($edit_jenis==='kurir') echo 'selected'; ?>>Kurir</option>
                            </select>
                            <input type="hidden" name="jenis" value="<?php echo $edit_jenis; ?>">
                        <?php else: ?>
                            <select name="jenis" class="form-select" required>
                                <option value="petugas">Petugas</option>
                                <option value="kurir">Kurir</option>
                            </select>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $edit_mode && $edit_data ? htmlspecialchars($edit_data['username']) : ''; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" <?php echo $edit_mode ? 'placeholder="Kosongkan jika tidak diubah"' : 'required'; ?> >
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?php
                            if ($edit_mode && $edit_data) {
                                echo htmlspecialchars($edit_jenis==='petugas' ? $edit_data['nama_petugas'] : $edit_data['nama_kurir']);
                            }
                        ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php
                            if ($edit_mode && $edit_data && $edit_jenis==='petugas') {
                                echo htmlspecialchars($edit_data['email']);
                            }
                        ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?php
                            if ($edit_mode && $edit_data) {
                                echo htmlspecialchars($edit_data['no_hp']);
                            }
                        ?>">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Perbarui' : 'Simpan'; ?></button>
                    <?php if ($edit_mode): ?>
                        <a href="pengguna.php?tab=<?php echo $edit_jenis; ?>" class="btn btn-secondary ms-2">Batal</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if ($tab == 'petugas'): ?>
        <h5 class="mt-4 mb-3">Daftar Petugas</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary text-white">
                    <tr>
                        <th><i class="bi bi-person-badge me-1"></i>Username</th>
                        <th><i class="bi bi-person-lines-fill me-1"></i>Nama</th>
                        <th><i class="bi bi-envelope me-1"></i>Email</th>
                        <th><i class="bi bi-telephone me-1"></i>No HP</th>
                        <th><i class="bi bi-sliders me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = mysqli_fetch_assoc($petugas)): ?>
                    <tr>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['nama_petugas']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['no_hp']; ?></td>
                        <td>
                            <a href="pengguna.php?tab=petugas&edit=petugas&id=<?php echo $row['id_petugas']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="pengguna.php?tab=petugas&hapus=petugas&id=<?php echo $row['id_petugas']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Nonaktifkan akun petugas ini?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <h5 class="mt-4 mb-3">Daftar Kurir</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary text-white">
                    <tr>
                        <th><i class="bi bi-person-badge me-1"></i>Username</th>
                        <th><i class="bi bi-person-lines-fill me-1"></i>Nama</th>
                        <th><i class="bi bi-telephone me-1"></i>No HP</th>
                        <th><i class="bi bi-circle-half me-1"></i>Status</th>
                        <th><i class="bi bi-sliders me-1"></i>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($row = mysqli_fetch_assoc($kurir)): ?>
                    <tr>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['nama_kurir']; ?></td>
                        <td><?php echo $row['no_hp']; ?></td>
                        <td>
                            <?php if ($row['status'] === 'aktif'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="pengguna.php?tab=kurir&edit=kurir&id=<?php echo $row['id_kurir']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="pengguna.php?tab=kurir&hapus=kurir&id=<?php echo $row['id_kurir']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Nonaktifkan akun kurir ini?');">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
