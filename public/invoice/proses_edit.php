<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id            = mysqli_real_escape_string($conn, $_POST['id']);
    $nomor_invoice = mysqli_real_escape_string($conn, $_POST['nomor_invoice']);
    $tanggal       = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $klien         = mysqli_real_escape_string($conn, $_POST['klien']);
    $jumlah        = floatval($_POST['jumlah'] ?? 0); // manual
    $status        = mysqli_real_escape_string($conn, $_POST['status']);

    // Detail
    $nama_transaksi = mysqli_real_escape_string($conn, $_POST['nama_transaksi']);
    $harga_satuan   = floatval($_POST['harga_satuan'] ?? 0);
    $kuantitas      = floatval($_POST['kuantitas'] ?? 0);
    $potongan_harga = floatval($_POST['potongan_harga'] ?? 0);
    $ppn_persen     = isset($_POST['ppn_persen']) ? floatval($_POST['ppn_persen']) : 0.11;

    // Hitung ulang server-side
    $total_harga = $harga_satuan * $kuantitas;
    $dpp         = max(0, $total_harga - $potongan_harga);
    $ppn         = $dpp * $ppn_persen;

    // Upload
    $targetDir = "../../uploads/invoice/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT lampiran FROM invoice WHERE id='$id'"));
    $oldFile = $row['lampiran'] ?? null;
    $newFileName = $oldFile;

    if (!empty($_POST['lampiran_camera']) && strpos($_POST['lampiran_camera'], 'base64,') !== false) {
        if ($oldFile && file_exists($targetDir.$oldFile)) unlink($targetDir.$oldFile);
        [$meta, $b64] = explode(";base64,", $_POST['lampiran_camera']);
        $newFileName = 'camera_' . time() . '.jpg';
        file_put_contents($targetDir.$newFileName, base64_decode($b64));
    } elseif (!empty($_FILES["lampiran"]["name"])) {
        if ($oldFile && file_exists($targetDir.$oldFile)) unlink($targetDir.$oldFile);
        $fileName = basename($_FILES["lampiran"]["name"]);
        $fileTmp  = $_FILES["lampiran"]["tmp_name"];
        $fileSize = $_FILES["lampiran"]["size"];
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['pdf','jpg','jpeg','png'];
        if (!in_array($ext,$allowed)) { header("Location: edit.php?id=$id&status=error&type=format"); exit; }
        if ($fileSize > 20000000)    { header("Location: edit.php?id=$id&status=error&type=size");   exit; }
        $newFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/","_",$fileName);
        move_uploaded_file($fileTmp, $targetDir.$newFileName);
    }

    // Update
    $sql = "UPDATE invoice SET
              nama_transaksi='$nama_transaksi',
              harga_satuan='$harga_satuan',
              kuantitas='$kuantitas',
              total_harga='$total_harga',
              potongan_harga='$potongan_harga',
              dpp='$dpp',
              ppn='$ppn',
              nomor_invoice='$nomor_invoice',
              tanggal='$tanggal',
              klien='$klien',
              jumlah='$jumlah',
              lampiran='$newFileName',
              status='$status'
            WHERE id='$id'";
    $ok = mysqli_query($conn, $sql);

    $admin_id = $_SESSION['admin_id'] ?? 1;
    if ($ok) {
        logAktivitas($conn, 'Edit Invoice', "Admin ID $admin_id mengedit invoice ID $id ($nomor_invoice - $klien)");
        header("Location: index.php?status=updated"); exit;
    } else {
        echo "<pre>Gagal DB:\n".mysqli_error($conn)."</pre>"; exit;
    }
} else {
    header("Location: index.php"); exit;
}
