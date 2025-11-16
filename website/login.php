<?php
require 'config.php';
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM login WHERE username='$username' AND status='aktif'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if ($row['password'] === md5($password)) {
            $_SESSION['id_login'] = $row['id_login'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } elseif ($row['role'] === 'petugas') {
                header('Location: petugas/dashboard.php');
            } elseif ($row['role'] === 'kurir') {
                header('Location: kurir/dashboard.php');
            } else {
                $error = 'Role tidak dikenali.';
            }
            exit;
        } else {
            $error = 'Password salah.';
        }
    } else {
        $error = 'Username tidak ditemukan atau akun nonaktif.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengiriman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: radial-gradient(circle at top left, #0d6efd 0, #0d6efd 25%, #0b5ed7 35%, #0a4275 60%, #020617 100%);
        }
        h2, h3, h4, h5 {
            font-weight: 600;
        }
        .btn-primary {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        .login-hero {
            background-image: url('https://images.pexels.com/photos/6169052/pexels-photo-6169052.jpeg?auto=compress&cs=tinysrgb&w=1200');
            background-size: cover;
            background-position: center;
            min-height: 100%;
        }
        .login-overlay {
            background: rgba(0,0,0,0.4);
            color: #fff;
            height: 100%;
        }
    </style>
</head>
<body>
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100 shadow-lg" style="max-width: 1040px; margin: 0 auto; border-radius: 16px; overflow: hidden; background-color:#ffffff;">
        <div class="col-md-6 p-0 login-hero d-none d-md-block">
            <div class="login-overlay d-flex flex-column justify-content-center p-4">
            </div>
        </div>
        <div class="col-md-6 bg-white p-4 p-md-5">
            <div class="text-center mb-4">
                <h4 class="fw-bold mb-1">Sistem Pengiriman Barang</h4>
                <small class="text-muted">Silakan masuk menggunakan akun yang sesuai dengan peran Anda.</small>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
