<?php
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php';
include '../../includes/sidebar.php';
include '../../includes/topbar.php';

$result = mysqli_query($conn, "SELECT * FROM admin ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kelola User - E-Archive Asrindo</title>
  <link rel="stylesheet" href="../../src/output.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 text-gray-800">
<div class="p-6 md:ml-64">

  <h1 class="text-3xl font-bold mb-6 text-[#0A1D4A]">Kelola User 👥</h1>

  <!-- ==== WRAPPER TABEL (BIAR RESPONSIVE DI MOBILE) ==== -->
  <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm text-gray-700">
        <thead class="bg-[#0A1D4A] text-white">
          <tr>
            <th class="px-4 py-3 text-left">ADMIN ID</th>
            <th class="px-4 py-3 text-left">Nama Lengkap</th>
            <th class="px-4 py-3 text-left">Username</th>
            <th class="px-4 py-3 text-left">Email</th>
            <th class="px-4 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1; 
          while ($row = mysqli_fetch_assoc($result)): ?>
          <tr class="border-b hover:bg-gray-50 transition">
            <td class="px-4 py-3 whitespace-nowrap"><?= $no++; ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($row['nama_lengkap']); ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($row['username']); ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($row['email']); ?></td>
            <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap">
              <button onclick="editUser(<?= $row['id'] ?>, 
                '<?= htmlspecialchars($row['nama_lengkap']) ?>', 
                '<?= htmlspecialchars($row['username']) ?>', 
                '<?= htmlspecialchars($row['email']) ?>')" 
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition-all duration-200">
                Edit
              </button>

              <button onclick="hapusUser(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nama_lengkap']) ?>')" 
                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md text-xs font-semibold shadow transition-all duration-200">
                Hapus
              </button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  // ==== KONFIRMASI PIN SAAT EDIT ====
  function editUser(id, nama, username, email) {
    Swal.fire({
      title: 'Konfirmasi PIN',
      input: 'password',
      inputLabel: 'Masukkan PIN keamanan (6 digit)',
      inputPlaceholder: '••••••',
      inputAttributes: { maxlength: 6 },
      confirmButtonText: 'Verifikasi',
      background: '#0A1D4A',
      color: '#fff',
      confirmButtonColor: '#22c55e',
      showCancelButton: true,
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        if (result.value === '789789') {
          Swal.fire({
            title: 'Edit Data User',
            html: `
              <form id="formEdit" action="proses_edit.php" method="POST" class="text-left space-y-3">
                <input type="hidden" name="id" value="${id}">
                <label class="block text-sm font-semibold">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" value="${nama}" required class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-emerald-400">

                <label class="block text-sm font-semibold">Username</label>
                <input type="text" name="username" value="${username}" required class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-emerald-400">

                <label class="block text-sm font-semibold">Email</label>
                <input type="email" name="email" value="${email}" required class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-emerald-400">

                <hr class="my-2">
                <label class="block text-sm font-semibold text-emerald-600">Ubah Password (opsional)</label>
                <p class="text-xs text-gray-500">Isi jika ingin mengubah password. Masukkan password lama terlebih dahulu.</p>

                <input type="password" name="password_lama" placeholder="Password Lama" class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-emerald-400">
                <input type="password" name="password_baru" placeholder="Password Baru" class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-emerald-400">
              </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Simpan Perubahan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0A1D4A',
            preConfirm: () => {
              document.getElementById('formEdit').submit();
            }
          });
        } else {
          Swal.fire('PIN Salah!', 'Perubahan dibatalkan.', 'error');
        }
      }
    });
  }

  // ==== KONFIRMASI PIN SAAT HAPUS ====
  function hapusUser(id, nama) {
    Swal.fire({
      title: 'Konfirmasi PIN',
      input: 'password',
      inputLabel: `Masukkan PIN untuk menghapus user "${nama}"`,
      inputPlaceholder: '••••••',
      inputAttributes: { maxlength: 6 },
      confirmButtonText: 'Verifikasi',
      background: '#0A1D4A',
      color: '#fff',
      confirmButtonColor: '#e11d48',
      showCancelButton: true,
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        if (result.value === '789789') {
          Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: `User "${nama}" akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#e11d48'
          }).then((del) => {
            if (del.isConfirmed) {
              window.location.href = `proses_hapus.php?id=${id}`;
            }
          });
        } else {
          Swal.fire('PIN Salah!', 'Penghapusan dibatalkan.', 'error');
        }
      }
    });
  }
</script>
</body>
</html>
