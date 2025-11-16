<?php
session_start();

function cekLogin() {
    if (!isset($_SESSION['id_login'])) {
        header('Location: login.php');
        exit;
    }
}

function cekRole($roleDiizinkan = []) {
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roleDiizinkan)) {
        header('Location: login.php');
        exit;
    }
}
?>
