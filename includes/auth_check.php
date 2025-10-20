<?php
ob_start();

// hanya jalankan session kalau belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// proteksi login
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// auto logout jika idle > 30 menit
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();
?>
