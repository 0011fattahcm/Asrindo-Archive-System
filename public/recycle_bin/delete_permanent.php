<?php
require '../../config/koneksi.php';
require '../../includes/log_helper.php'; // ✅ panggil helper log

if (empty($_POST['selected'])) {
  header('Location: index.php?status=error&type=missing');
  exit;
}

$admin_id = $_SESSION['admin_id'] ?? 1;
$selected = $_POST['selected'];
$successCount = 0;
$errorCount = 0;

foreach ($selected as $id) {
  $rb = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM recycle_bin WHERE id='$id'"));
  if (!$rb) {
    $errorCount++;
    continue;
  }

  $tabel = $rb['tabel_asal'];
  $record_id = $rb['record_id'];
  $judul = $rb['judul'];
  $allowed = ['surat_masuk', 'surat_keluar', 'draft', 'invoice'];
  if (!in_array($tabel, $allowed)) {
    $errorCount++;
    continue;
  }

  // 🔍 Hapus lampiran jika ada
  $lamp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT lampiran FROM `$tabel` WHERE id='$record_id'"));
  if (!empty($lamp['lampiran'])) {
    $path = "../../uploads/$tabel/" . $lamp['lampiran'];
    if (file_exists($path)) @unlink($path);
  }

  // ❌ Hapus data dari tabel utama & recycle_bin
  $del1 = mysqli_query($conn, "DELETE FROM `$tabel` WHERE id='$record_id'");
  $del2 = mysqli_query($conn, "DELETE FROM recycle_bin WHERE id='$id'");

  if ($del1 && $del2) {
    $successCount++;
    // ✅ Catat log aktivitas untuk tiap data
    logAktivitas($conn, 'Hapus Permanen', "Admin ID $admin_id menghapus permanen data '$judul' dari tabel $tabel (record ID: $record_id)");
  } else {
    $errorCount++;
  }
}

// Redirect dengan status
if ($successCount > 0 && $errorCount == 0) {
  header('Location: index.php?status=deleted');
} elseif ($successCount > 0 && $errorCount > 0) {
  header('Location: index.php?status=partial');
} else {
  header('Location: index.php?status=error&type=failed');
}
exit;
?>
