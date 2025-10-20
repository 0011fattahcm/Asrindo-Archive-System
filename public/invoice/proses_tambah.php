<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Data utama
    $nomor_invoice = mysqli_real_escape_string($conn, $_POST['nomor_invoice']);
    $tanggal       = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $klien         = mysqli_real_escape_string($conn, $_POST['klien']);
    $jumlah        = floatval($_POST['jumlah'] ?? 0); // manual
    $status        = mysqli_real_escape_string($conn, $_POST['status']);

    // Detail transaksi
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
    $newFileName = null;

    if (!empty($_POST['lampiran_camera']) && strpos($_POST['lampiran_camera'], 'base64,') !== false) {
        [$meta, $b64] = explode(";base64,", $_POST['lampiran_camera']);
        $newFileName = 'camera_' . time() . '.jpg';
        file_put_contents($targetDir . $newFileName, base64_decode($b64));
    } elseif (!empty($_FILES["lampiran"]["name"])) {
        $fileName = basename($_FILES["lampiran"]["name"]);
        $fileTmp  = $_FILES["lampiran"]["tmp_name"];
        $fileSize = $_FILES["lampiran"]["size"];
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['pdf','jpg','jpeg','png'];
        if (!in_array($ext,$allowed)) { header("Location: tambah.php?status=error&type=format"); exit; }
        if ($fileSize > 20000000)    { header("Location: tambah.php?status=error&type=size");   exit; }
        $newFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/","_",$fileName);
        move_uploaded_file($fileTmp, $targetDir.$newFileName);
    }

    // Insert
    $sql = "INSERT INTO invoice
            (nama_transaksi, harga_satuan, kuantitas, total_harga, potongan_harga, dpp, ppn,
             nomor_invoice, tanggal, klien, jumlah, file_id, lampiran, status)
            VALUES
            ('$nama_transaksi', '$harga_satuan', '$kuantitas', '$total_harga', '$potongan_harga', '$dpp', '$ppn',
             '$nomor_invoice', '$tanggal', '$klien', '$jumlah', NULL, '$newFileName', '$status')";
    $ok = mysqli_query($conn, $sql);

    $admin_id = $_SESSION['admin_id'] ?? 1;
    if ($ok) {
        logAktivitas($conn, 'Tambah Invoice', "Admin ID $admin_id menambahkan Invoice baru: $nomor_invoice - $klien");
        header("Location: index.php?status=success"); exit;
    } else {
        echo "<pre>Gagal DB:\n".mysqli_error($conn)."</pre>"; exit;
    }
} else {
    header("Location: tambah.php?status=error&type=invalid"); exit;
}
