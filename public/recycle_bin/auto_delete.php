<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';

// Auto hapus recycle_bin lebih dari 30 hari
$result = mysqli_query($conn, "SELECT * FROM recycle_bin WHERE tanggal_hapus < NOW() - INTERVAL 30 DAY");
while ($row = mysqli_fetch_assoc($result)) {
  $tabel = $row['tabel_asal'];
  $record_id = $row['record_id'];

  $lamp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT lampiran FROM `$tabel` WHERE id='$record_id'"));
  if (!empty($lamp['lampiran'])) {
    $path = "../../uploads/$tabel/" . $lamp['lampiran'];
    if (file_exists($path)) @unlink($path);
  }

  mysqli_query($conn, "DELETE FROM `$tabel` WHERE id='$record_id'");
  mysqli_query($conn, "DELETE FROM recycle_bin WHERE id='{$row['id']}'");
}
echo "Auto delete selesai: " . date('Y-m-d H:i:s');
