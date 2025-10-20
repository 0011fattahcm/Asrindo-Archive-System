<?php
session_start();
require '../../includes/auth_check.php';
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

  // ✅ Pulihkan data (soft delete -> aktif kembali)
  $restore = mysqli_query($conn, "UPDATE `$tabel` SET deleted_at=NULL WHERE id='$record_id'");
  $removeFromBin = mysqli_query($conn, "DELETE FROM recycle_bin WHERE id='$id'");

  if ($restore && $removeFromBin) {
    $successCount++;
    // 🧾 Catat log aktivitas per data
    logAktivitas($conn, 'Restore Data', "Admin ID $admin_id memulihkan data '$judul' dari tabel $tabel (record ID: $record_id)");
  } else {
    $errorCount++;
  }
}

// ✅ Redirect hasil akhir
if ($successCount > 0 && $errorCount == 0) {
  header('Location: index.php?status=restored');
} elseif ($successCount > 0 && $errorCount > 0) {
  header('Location: index.php?status=partial');
} else {
  header('Location: index.php?status=error&type=restore_failed');
}
exit;
?>
