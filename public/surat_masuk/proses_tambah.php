<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // === Ambil data dari form ===
    $nomor_surat    = mysqli_real_escape_string($conn, $_POST['nomor_surat']);
    $tanggal_surat  = mysqli_real_escape_string($conn, $_POST['tanggal_surat']);
    $tanggal_terima = mysqli_real_escape_string($conn, $_POST['tanggal_terima']);
    $pengirim       = mysqli_real_escape_string($conn, $_POST['pengirim']);
    $perihal        = mysqli_real_escape_string($conn, $_POST['perihal']);
    $ringkasan      = mysqli_real_escape_string($conn, $_POST['ringkasan']);
    $departemen_id  = isset($_POST['departemen_id']) ? mysqli_real_escape_string($conn, $_POST['departemen_id']) : null;

    // Validasi wajib isi departemen
    if (empty($departemen_id)) {
        header("Location: tambah.php?status=error&type=departemen");
        exit;
    }

    $targetDir = "../../uploads/surat_masuk/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $newFileName = null; // default nama file

    // === 1️⃣ Jika ada foto dari kamera ===
    if (!empty($_POST['lampiran_camera'])) {
        $data = $_POST['lampiran_camera'];
        if (strpos($data, 'base64,') !== false) {
            $image_parts = explode(";base64,", $data);
            $image_base64 = base64_decode($image_parts[1]);
            $newFileName = 'camera_' . time() . '.jpg';
            $filePath = $targetDir . $newFileName;
            if (file_put_contents($filePath, $image_base64) === false) {
                echo "<pre>❌ Gagal menyimpan hasil kamera ke folder.</pre>";
                exit;
            }
        }
    }

    // === 2️⃣ Jika upload manual (file biasa) ===
    elseif (!empty($_FILES["lampiran"]["name"])) {
        $fileName  = basename($_FILES["lampiran"]["name"]);
        $fileTmp   = $_FILES["lampiran"]["tmp_name"];
        $fileSize  = $_FILES["lampiran"]["size"];
        $fileType  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validasi format file
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        if (!in_array($fileType, $allowedTypes)) {
            header("Location: tambah.php?status=error&type=format");
            exit;
        }

        // Validasi ukuran (maks 20MB)
        if ($fileSize > 20000000) {
            header("Location: tambah.php?status=error&type=size");
            exit;
        }

        $newFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
        $targetFilePath = $targetDir . $newFileName;

        if (!move_uploaded_file($fileTmp, $targetFilePath)) {
            header("Location: tambah.php?status=error&type=upload");
            exit;
        }
    }

    // === 3️⃣ Simpan ke database ===
    $query = "INSERT INTO surat_masuk 
              (nomor_surat, tanggal_surat, tanggal_terima, pengirim, perihal, ringkasan, departemen_id, lampiran)
              VALUES 
              ('$nomor_surat', '$tanggal_surat', '$tanggal_terima', '$pengirim', '$perihal', '$ringkasan', '$departemen_id', '$newFileName')";

    $result = mysqli_query($conn, $query);

// (kode insert ke database di sini...)
if ($result) {
    logAktivitas($conn, 'Tambah Surat Masuk', "Admin ID $admin_id menambahkan Surat Masuk baru: $nomor_surat - $pengirim");
    header("Location: index.php?status=success");
    exit;
} else {
        echo "<pre>❌ Gagal menyimpan ke database:<br>" . mysqli_error($conn) . "</pre>";
        exit;
    }
} else {
    header("Location: tambah.php?status=error&type=invalid");
    exit;
}


?>
