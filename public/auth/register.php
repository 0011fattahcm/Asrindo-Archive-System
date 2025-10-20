<?php
include '../../config/koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $pin = mysqli_real_escape_string($conn, $_POST['pin']);

  // PIN salah
  if ($pin !== "789789") {
    header("Location: register.php?error=pin");
    exit;
  }

  // Username duplikat
  $check = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
  if (mysqli_num_rows($check) > 0) {
    header("Location: register.php?error=username");
    exit;
  }

  // Hash password dan simpan data
  $hashed = password_hash($password, PASSWORD_DEFAULT);
  $query = "INSERT INTO admin (username, password, nama_lengkap, email)
            VALUES ('$username', '$hashed', '$nama_lengkap', '$email')";

  if (mysqli_query($conn, $query)) {
    header("Location: register.php?success=1");
    exit;
  } else {
    header("Location: register.php?error=fail");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Register - E-Archive Asrindo</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { font-family: 'Poppins', sans-serif; }
    @keyframes fade-in {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.8s ease forwards; }
  </style>
</head>

<body class="bg-gradient-to-br from-[#0A1D4A] via-[#1445b8] to-emerald-700 flex items-center justify-center min-h-screen relative overflow-hidden">

  <!-- Overlay efek cahaya -->
  <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.1),transparent_60%)]"></div>

  <!-- FORM CARD -->
  <form method="POST"
        class="relative bg-white/10 backdrop-blur-lg border border-white/20 px-8 py-6 
               rounded-2xl shadow-2xl w-[92%] max-w-md text-white space-y-3 z-10 
               animate-fade-in">

    <!-- LOGO + TITLE -->
    <div class="flex flex-col items-center space-y-2 mb-2">
      <img src="../logo.png" alt="Logo Asrindo" class="h-14 w-auto drop-shadow-lg" />
      <h1 class="text-xl font-semibold tracking-tight text-center leading-tight">
        E-ARCHIVE <br>
        <span class="text-sm font-normal">PT ASRINDO ENVIRONT INVESTAMA</span>
      </h1>
      <p class="text-xs opacity-80 text-center mt-1">Daftar akun admin untuk mengakses sistem arsip</p>
    </div>

    <!-- INPUTS -->
    <div class="space-y-3 mt-2">
      <input type="text" name="username" placeholder="Username" required 
             class="w-full rounded-lg px-4 py-2.5 bg-white/10 border border-white/30 
                    placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
      <input type="password" name="password" placeholder="Password" required 
             class="w-full rounded-lg px-4 py-2.5 bg-white/10 border border-white/30 
                    placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
      <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required 
             class="w-full rounded-lg px-4 py-2.5 bg-white/10 border border-white/30 
                    placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
      <input type="email" name="email" placeholder="Email" required 
             class="w-full rounded-lg px-4 py-2.5 bg-white/10 border border-white/30 
                    placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-emerald-400" />
    </div>

    <!-- PIN -->
    <div class="mt-3">
      <label class="block text-sm font-semibold mb-1 text-white/90 text-center">MASUKKAN PIN KEAMANAN</label>
      <div class="flex justify-center space-x-2">
        <?php for ($i = 1; $i <= 6; $i++): ?>
          <input 
            type="password"
            maxlength="1"
            inputmode="numeric"
            pattern="[0-9]*"
            class="pin-input w-10 h-10 text-center rounded-lg bg-white/10 border border-white/30 
                   text-white text-xl font-bold focus:ring-2 focus:ring-emerald-400 focus:outline-none"
            required
          />
        <?php endfor; ?>
      </div>
      <input type="hidden" name="pin" id="pinHidden" />
    </div>

    <!-- SUBMIT -->
    <button type="submit"
            class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2.5 
                   rounded-lg mt-4 transition-all duration-300 shadow-md hover:shadow-lg">
      Daftar Sekarang
    </button>

    <p class="text-xs text-center opacity-80">
      Sudah punya akun?
      <a href="login.php" class="text-emerald-300 hover:text-emerald-400 font-semibold underline-offset-4 hover:underline">
        Masuk
      </a>
    </p>
  </form>

  <!-- LOGIKA PIN -->
  <script>
    const inputs = document.querySelectorAll('.pin-input');
    const hiddenInput = document.getElementById('pinHidden');

    inputs.forEach((input, index) => {
      input.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '');
        if (e.target.value.length === 1 && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
        updateHidden();
      });
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
          inputs[index - 1].focus();
        }
      });
    });

    function updateHidden() {
      hiddenInput.value = Array.from(inputs).map(i => i.value).join('');
    }

    // Fokus otomatis ke input pertama
    window.addEventListener('load', () => inputs[0].focus());
  </script>

  <!-- SWEETALERT NOTIFIKASI -->
  <script>
    <?php if (isset($_GET['error']) && $_GET['error'] == 'pin'): ?>
      Swal.fire({
        icon: 'error',
        title: 'PIN Keamanan Salah!',
        text: 'Silakan masukkan PIN 6 digit yang benar.',
        background: 'linear-gradient(135deg, #0A1D4A 0%, #1445b8 50%, #059669 100%)',
        color: '#fff',
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Coba Lagi',
        customClass: { popup: 'rounded-2xl shadow-2xl border border-white/20 backdrop-blur-md' }
      });
    <?php elseif (isset($_GET['error']) && $_GET['error'] == 'username'): ?>
      Swal.fire({
        icon: 'warning',
        title: 'Username Sudah Digunakan!',
        text: 'Silakan pilih username lain.',
        background: 'linear-gradient(135deg, #0A1D4A 0%, #1445b8 50%, #059669 100%)',
        color: '#fff',
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Oke',
        customClass: { popup: 'rounded-2xl shadow-2xl border border-white/20 backdrop-blur-md' }
      });
    <?php elseif (isset($_GET['error']) && $_GET['error'] == 'fail'): ?>
      Swal.fire({
        icon: 'error',
        title: 'Registrasi Gagal!',
        text: 'Terjadi kesalahan pada server. Coba lagi nanti.',
        background: 'linear-gradient(135deg, #0A1D4A 0%, #1445b8 50%, #059669 100%)',
        color: '#fff',
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Tutup',
        customClass: { popup: 'rounded-2xl shadow-2xl border border-white/20 backdrop-blur-md' }
      });
    <?php elseif (isset($_GET['success']) && $_GET['success'] == 1): ?>
      Swal.fire({
        icon: 'success',
        title: 'Registrasi Berhasil!',
        text: 'Akun admin berhasil dibuat. Silakan login sekarang.',
        background: 'linear-gradient(135deg, #0A1D4A 0%, #1445b8 50%, #059669 100%)',
        color: '#fff',
        confirmButtonColor: '#22c55e',
        confirmButtonText: 'Login Sekarang',
        showCancelButton: true,
        cancelButtonText: 'Nanti Saja',
        cancelButtonColor: '#999',
        customClass: { popup: 'rounded-2xl shadow-2xl border border-white/20 backdrop-blur-md' }
      }).then((result) => {
        if (result.isConfirmed) window.location.href = 'login.php';
      });
    <?php endif; ?>
  </script>

</body>
</html>
