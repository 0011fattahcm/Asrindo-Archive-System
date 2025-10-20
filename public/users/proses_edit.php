<?php
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = intval($_POST['id']);
  $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password_lama = $_POST['password_lama'] ?? '';
  $password_baru = $_POST['password_baru'] ?? '';

  // cek apakah username sudah digunakan user lain
  $cekUser = mysqli_query($conn, "SELECT id FROM admin WHERE username='$username' AND id!='$id'");
  if (mysqli_num_rows($cekUser) > 0) {
    echo "<script>
      alert('Username sudah digunakan user lain!');
      window.location.href='index.php';
    </script>";
    exit;
  }

  // ambil data user lama
  $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM admin WHERE id='$id'"));

  // validasi password lama jika mau ubah password
  if (!empty($password_lama) && !empty($password_baru)) {
    if (password_verify($password_lama, $data['password'])) {
      $hashed = password_hash($password_baru, PASSWORD_DEFAULT);
      $query = "UPDATE admin SET nama_lengkap='$nama', username='$username', email='$email', password='$hashed' WHERE id='$id'";
      $keterangan = "Admin $id mengubah data & password user ID: $id ($nama)";
    } else {
      echo "<script>
        alert('Password lama salah! Perubahan dibatalkan.');
        window.location.href='index.php';
      </script>";
      exit;
    }
  } else {
    // kalau tidak ubah password
    $query = "UPDATE admin SET nama_lengkap='$nama', username='$username', email='$email' WHERE id='$id'";
    $keterangan = "Admin $id mengubah data user ID: $id ($nama)";
  }

  if (mysqli_query($conn, $query)) {
    logAktivitas($conn, 'Edit User', $keterangan);
    header("Location: index.php?status=edited");
    exit;
  } else {
    die("Gagal mengedit data: " . mysqli_error($conn));
  }
}
?>
