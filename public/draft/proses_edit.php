<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $id         = mysqli_real_escape_string($conn, $_POST['id']);
    $judul      = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi  = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status     = mysqli_real_escape_string($conn, $_POST['status']);

    $targetDir = "../../uploads/draft/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    // Ambil data lama
    $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT lampiran FROM draft WHERE id='$id'"));
    $oldFile = $old['lampiran'];
    $newFileName = $oldFile;

    // === 1️⃣ Jika ambil foto dari kamera ===
    if (!empty($_POST['lampiran_camera'])) {
        $data = $_POST['lampiran_camera'];
        if (strpos($data, 'base64,') !== false) {
            // Hapus file lama jika ada
            if ($oldFile && file_exists($targetDir . $oldFile)) unlink($targetDir . $oldFile);

            $image_parts = explode(";base64,", $data);
            $image_base64 = base64_decode($image_parts[1]);
            $newFileName = 'camera_' . time() . '.jpg';
            file_put_contents($targetDir . $newFileName, $image_base64);
        }
    }

    // === 2️⃣ Jika upload file baru ===
    elseif (!empty($_FILES["lampiran"]["name"])) {
        // Hapus file lama jika ada
        if ($oldFile && file_exists($targetDir . $oldFile)) unlink($targetDir . $oldFile);

        $fileName = basename($_FILES["lampiran"]["name"]);
        $fileTmp   = $_FILES["lampiran"]["tmp_name"];
        $fileSize  = $_FILES["lampiran"]["size"];
        $fileType  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validasi format file
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        if (!in_array($fileType, $allowedTypes)) {
            header("Location: edit.php?id=$id&status=error&type=format");
            exit;
        }

        // Validasi ukuran (maks 20MB)
        if ($fileSize > 20000000) {
            header("Location: edit.php?id=$id&status=error&type=size");
            exit;
        }

        // Simpan file baru
        $newFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
        move_uploaded_file($fileTmp, $targetDir . $newFileName);
    }

    // === 3️⃣ Update data ke database ===
    $query = "UPDATE draft SET 
                judul='$judul',
                deskripsi='$deskripsi',
                status='$status',
                lampiran='$newFileName',
                updated_at=NOW()
              WHERE id='$id'";

    $result = mysqli_query($conn, $query);

if ($result) {
    logAktivitas($conn, 'Edit Draft', "Admin ID $admin_id mengedit Draft ID $id ($judul)");
    header("Location: index.php?status=updated");
    exit;
} else {
        echo "<pre>❌ Gagal mengupdate data draft:<br>" . mysqli_error($conn) . "</pre>";
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

?>
