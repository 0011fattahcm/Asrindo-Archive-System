<?php
include '../../config/koneksi.php';
function logAktivitas($conn, $aksi, $keterangan) {
    // ✅ Pastikan tidak ada session_start() ganda
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $admin_id = $_SESSION['admin_id'] ?? 1;

    $stmt = mysqli_prepare($conn, 
        "INSERT INTO log_aktivitas (admin_id, aksi, keterangan) VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "iss", $admin_id, $aksi, $keterangan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $tabel_asal = 'invoice';
    $tanggal_hapus = date('Y-m-d H:i:s');
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 1;
    $judul = '';

    // Ambil data invoice untuk disimpan ke recycle_bin
    $queryGet = mysqli_query($conn, "SELECT nomor_invoice, klien FROM invoice WHERE id='$id'");
    if ($data = mysqli_fetch_assoc($queryGet)) {
        $judul = $data['nomor_invoice'] . ' - ' . $data['klien'];

        // Masukkan ke recycle_bin
        $insertRecycle = mysqli_query($conn, "
            INSERT INTO recycle_bin (tabel_asal, record_id, judul, tanggal_hapus, admin_id)
            VALUES ('$tabel_asal', '$id', '$judul', '$tanggal_hapus', '$admin_id')
        ");

        if ($insertRecycle) {
            // Hapus fisik (bisa diganti soft delete kalau mau)
          $delete = mysqli_query($conn, "UPDATE invoice SET deleted_at='$tanggal_hapus' WHERE id='$id'");

            if ($delete) {
                // ✅ Catat log aktivitas di sini (setelah delete sukses)
                logAktivitas($conn, 'Hapus Invoice', "Admin ID $admin_id menghapus invoice: $judul");

                $status = "success";
                $message = "Invoice berhasil dipindahkan ke Recycle Bin!";
            } else {
                $status = "error";
                $message = "Gagal menghapus invoice dari tabel utama: " . mysqli_error($conn);
            }
        } else {
            $status = "error";
            $message = "Gagal memindahkan invoice ke Recycle Bin: " . mysqli_error($conn);
        }
    } else {
        $status = "error";
        $message = "Data invoice tidak ditemukan!";
    }
} else {
    $status = "error";
    $message = "ID invoice tidak ditemukan!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Status Hapus Invoice</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="bg-white shadow-xl rounded-xl p-8 w-[400px] text-center">
    <?php if ($status === "success"): ?>
      <div class="flex justify-center mb-4">
        <i data-lucide="check-circle" class="w-14 h-14 text-emerald-500 animate-bounce"></i>
      </div>
      <h2 class="text-xl font-bold text-gray-800 mb-2">Berhasil!</h2>
      <p class="text-gray-600 mb-6"><?php echo $message; ?></p>
      <a href="index.php" 
         class="inline-block bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md transition">
         Kembali ke Daftar
      </a>
    <?php else: ?>
      <div class="flex justify-center mb-4">
        <i data-lucide="x-circle" class="w-14 h-14 text-red-500 animate-pulse"></i>
      </div>
      <h2 class="text-xl font-bold text-gray-800 mb-2">Gagal!</h2>
      <p class="text-gray-600 mb-6"><?php echo $message; ?></p>
      <button onclick="history.back()" 
              class="bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md transition">
              Kembali
      </button>
    <?php endif; ?>
  </div>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>
