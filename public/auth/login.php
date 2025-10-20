<?php
include '../../config/koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  $query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
  $data = mysqli_fetch_assoc($query);

  if ($data && password_verify($password, $data['password'])) {
    $_SESSION['admin_id'] = $data['id'];
    $_SESSION['nama_admin'] = $data['nama_lengkap'];
    header("Location: ../dashboard/dashboard.php");
    exit;
  } else {
    header("Location: login.php?error=1");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Login - E-Archive Asrindo</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Poppins', sans-serif; }
  </style>
</head>

<body class="bg-gradient-to-br from-[#0A1D4A] via-[#1445b8] to-emerald-700 flex items-center justify-center min-h-screen relative overflow-hidden">
  <!-- Background overlay -->
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.08),transparent_65%)]"></div>

  <!-- NOTIFIKASI -->
  <script>
    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
      Swal.fire({
        icon: 'error',
        title: 'Login Gagal!',
        text: 'Username atau password salah.',
        background: 'linear-gradient(135deg, #0A1D4A 0%, #1445b8 50%, #059669 100%)',
        color: '#fff',
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Coba Lagi',
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-white/20 backdrop-blur-md'
        }
      });
    <?php endif; ?>

    <?php if (isset($_GET['timeout'])): ?>
      Swal.fire({
        icon: 'warning',
        title: 'Sesi Berakhir',
        text: 'Anda logout otomatis karena tidak aktif selama 30 menit.',
        background: 'linear-gradient(135deg, #0A1D4A 0%, #1445b8 50%, #059669 100%)',
        color: '#fff',
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Login Ulang',
        customClass: {
          popup: 'rounded-2xl shadow-2xl border border-white/20 backdrop-blur-md'
        }
      });
    <?php endif; ?>
  </script>

  <!-- FORM LOGIN -->
  <form method="POST" class="relative bg-white/10 backdrop-blur-lg border border-white/20 p-8 rounded-2xl shadow-2xl w-[95%] max-w-md text-white space-y-5 z-10 animate-fade-in">
    <div class="flex flex-col items-center space-y-2 mb-4 text-center">
      <img src="../logo.png" alt="Logo Asrindo" class="h-16 w-auto drop-shadow-lg" />
      <h1 class="text-2xl font-semibold leading-tight">
        E-ARCHIVE<br>
        <span class="text-sm font-normal opacity-80">PT ASRINDO ENVIRONT INVESTAMA</span>
      </h1>
      <p class="text-sm opacity-80">Silakan login untuk mengakses sistem</p>
    </div>

    <div class="space-y-3">
      <input type="text" name="username" placeholder="Username" required
        class="w-full rounded-lg px-4 py-2 bg-white/10 border border-white/30 placeholder-white/70 
               focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all duration-200" />

      <input type="password" name="password" placeholder="Password" required
        class="w-full rounded-lg px-4 py-2 bg-white/10 border border-white/30 placeholder-white/70 
               focus:outline-none focus:ring-2 focus:ring-emerald-400 transition-all duration-200" />
    </div>

    <button type="submit"
      class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2.5 rounded-lg 
             transition-all duration-300 shadow-md hover:shadow-lg">
      Masuk
    </button>

    <div class="text-center mt-4">
      <a href="register.php"
        class="text-emerald-300 hover:text-emerald-400 font-semibold underline-offset-4 hover:underline transition-colors duration-300">
        Buat Akun Baru
      </a>
    </div>
  </form>

  <!-- ANIMASI -->
  <style>
    @keyframes fade-in {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.8s ease forwards; }
  </style>
</body>
</html>
