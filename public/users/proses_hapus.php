<?php
include '../../includesg/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_lengkap FROM admin WHERE id='$id'"));
  $nama = $data['nama_lengkap'] ?? 'Tidak diketahui';

  mysqli_query($conn, "DELETE FROM admin WHERE id='$id'");
  logAktivitas($conn, 'Hapus User', "Admin $id menghapus user '$nama' (ID: $id)");

  header("Location: index.php?status=deleted");
  exit;
}
?>
