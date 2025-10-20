<?php

include '../../config/koneksi.php';

if (isset($_POST['selected'])) {
  $ids = implode(',', array_map('intval', $_POST['selected']));
  mysqli_query($conn, "DELETE FROM log_aktivitas WHERE id IN ($ids)");
}

header('Location: index.php?status=deleted');
exit;
?>
