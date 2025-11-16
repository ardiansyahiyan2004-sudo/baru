<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'petugas') {
        header('Location: petugas/dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'kurir') {
        header('Location: kurir/dashboard.php');
        exit;
    }
}
header('Location: login.php');
exit;
