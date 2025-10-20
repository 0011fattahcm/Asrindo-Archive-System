<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id             = mysqli_real_escape_string($conn, $_POST['id']);
    $nomor_surat    = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
    $tanggal_surat  = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
    $tanggal_terima = mysqli_real_escape_string($conn, $_POST['tanggal_terima']);
    $pengirim       = mysqli_real_escape_string($conn, $_POST['pengirim']);
    $perihal        = mysqli_real_escape_string($conn, $_POST['perihal']);
    $ringkasan      = mysqli_real_escape_string($conn, $_POST['ringkasan']);
    $departemen_id  = mysqli_real_escape_string($conn, $_POST['departemen_id']);

    $targetDir = "../../uploads/surat_masuk/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    // ambil data lama
    $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT lampiran FROM surat_masuk WHERE id='$id'"));
    $oldFile = $old['lampiran'];
    $newFileName = $oldFile;

    // Jika ambil foto baru
    if (!empty($_POST['lampiran_camera'])) {
        $data = $_POST['lampiran_camera'];
        if (strpos($data, 'base64,') !== false) {
            if ($oldFile && file_exists($targetDir . $oldFile)) unlink($targetDir . $oldFile);
            $image_parts = explode(";base64,", $data);
            $image_base64 = base64_decode($image_parts[1]);
            $newFileName = 'camera_' . time() . '.jpg';
            file_put_contents($targetDir . $newFileName, $image_base64);
        }
    }
    // Jika upload file baru
    elseif (!empty($_FILES["lampiran"]["name"])) {
        if ($oldFile && file_exists($targetDir . $oldFile)) unlink($targetDir . $oldFile);
        $fileName = basename($_FILES["lampiran"]["name"]);
        $fileTmp   = $_FILES["lampiran"]["tmp_name"];
        $newFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
        move_uploaded_file($fileTmp, $targetDir . $newFileName);
    }

    $query = "UPDATE surat_masuk SET 
                nomor_surat='$nomor_surat',
                tanggal_surat='$tanggal_surat',
                tanggal_terima='$tanggal_terima',
                pengirim='$pengirim',
                perihal='$perihal',
                ringkasan='$ringkasan',
                departemen_id='$departemen_id',
                lampiran='$newFileName'
              WHERE id='$id'";

    $result = mysqli_query($conn, $query);

if ($result) {
    logAktivitas($conn, 'Edit Surat Masuk', "Admin ID $admin_id mengedit Surat Masuk ID $id ($nomor_surat - $pengirim)");
    header("Location: index.php?status=updated");
    exit;
} else {
        echo "<pre>❌ Gagal mengupdate data:<br>" . mysqli_error($conn) . "</pre>";
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
