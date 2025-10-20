<?php
// =======================
// UNIVERSAL LOG HELPER
// Bisa dipanggil di semua file proses (tambah, edit, hapus, restore, hapus permanen)
// =======================
function logAktivitas($conn, $aksi, $keterangan) {
    session_start();
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 1;
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    $stmt = mysqli_prepare($conn, "INSERT INTO log_aktivitas (admin_id, aksi, keterangan, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issss", $admin_id, $aksi, $keterangan, $ip, $ua);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>
