<?php
/**
 * ==========================================================
 *  KONFIGURASI DATABASE
 *  Project : E-Archive PT Asrindo Environt Investama
 *  Author  : (Your Name)
 *  Date    : <?php echo date('Y-m-d'); ?>
 * ==========================================================
 */

$host = "localhost";       // Server database (default: localhost)
$user = "root";            // Username MySQL
$pass = "";                // Password MySQL (kosong di XAMPP default)
$db   = "db_asrindo_archive"; // Nama database

// Buat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
  die("<h3 style='color:red;'>❌ Koneksi ke database gagal:</h3> " . mysqli_connect_error());
}

// Set timezone ke WIB
date_default_timezone_set('Asia/Jakarta');

// Opsional: echo status koneksi saat testing
// echo "✅ Koneksi database berhasil!";

?>
