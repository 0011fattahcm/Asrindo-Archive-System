<?php
include '../../includes/auth_check.php';

// Deteksi halaman aktif
$currentFile = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));

// Daftar menu sidebar
$menuItems = [
  ['title' => 'Dashboard', 'icon' => 'layout-dashboard', 'link' => '../dashboard/dashboard.php', 'dir' => 'dashboard'],
  ['title' => 'Kelola User', 'icon' => 'users', 'link' => '../users/index.php', 'dir' => 'users'],
  ['title' => 'Departemen', 'icon' => 'building-2', 'link' => '../departemen/index.php', 'dir' => 'departemen'],
  ['title' => 'Surat Masuk', 'icon' => 'inbox', 'link' => '../surat_masuk/index.php', 'dir' => 'surat_masuk'],
  ['title' => 'Surat Keluar', 'icon' => 'send', 'link' => '../surat_keluar/index.php', 'dir' => 'surat_keluar'],
  ['title' => 'Invoice', 'icon' => 'receipt', 'link' => '../invoice/index.php', 'dir' => 'invoice'],
  ['title' => 'Draft Dokumen', 'icon' => 'file-text', 'link' => '../draft/index.php', 'dir' => 'draft'],
  ['title' => 'Recycle Bin', 'icon' => 'trash-2', 'link' => '../recycle_bin/index.php', 'dir' => 'recycle_bin'],
  ['title' => 'Log Aktivitas', 'icon' => 'activity', 'link' => '../log_aktivitas/index.php', 'dir' => 'log_aktivitas'],
];
?>

<!-- TOGGLE BUTTON (HAMBURGER) -->
<button id="openSidebar" class="md:hidden fixed top-5 left-5 z-50 bg-[#0A1D4A] text-white p-2 rounded-lg shadow-lg focus:outline-none">
  <i data-lucide="menu" class="w-6 h-6"></i>
</button>

<!-- OVERLAY (untuk mobile) -->
<div id="overlay" class="fixed inset-0 bg-black/40 hidden z-30 md:hidden"></div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SIDEBAR -->
<aside id="sidebar"
  class="fixed top-0 left-0 h-screen w-64 bg-[#0A1D4A] text-white flex flex-col shadow-2xl z-40
  transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">

  <!-- HEADER -->
  <div class="flex items-center justify-between px-5 py-5 border-b border-blue-400/20 relative">
    <div class="flex items-center gap-3">
      <div class="bg-gradient-to-br from-cyan-500 to-emerald-400 p-2 rounded-lg shadow-lg">
        <img src="/asrindo-archive/assets/img/logo.png" alt="Logo" class="h-10 rounded-lg">
      </div>
      <div>
        <h1 class="text-lg font-bold tracking-wide">ASRINDO</h1>
        <p class="text-xs text-blue-200">E-Archive System</p>
      </div>
    </div>

    <!-- Tombol Close (mobile) -->
    <button id="closeSidebar" class="md:hidden text-gray-300 hover:text-white focus:outline-none absolute right-5 top-1/2 -translate-y-1/2">
      <i data-lucide="x" class="w-6 h-6"></i>
    </button>
  </div>

  <!-- MENU -->
  <nav class="mt-4 space-y-2 px-4 text-[15px] font-medium flex-1 overflow-y-auto">
    <?php foreach ($menuItems as $item): ?>
      <?php $isActive = ($currentDir == $item['dir']); ?>
      <a href="<?= $item['link']; ?>"
        class="relative flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all duration-200 group
        <?= $isActive
          ? 'bg-gradient-to-r from-cyan-400 via-blue-500 to-blue-700 text-white font-semibold shadow-md before:content-[\'\'] before:absolute before:left-0 before:top-0 before:h-full before:w-[3px] before:bg-cyan-300 before:rounded-r-md'
          : 'text-gray-300 hover:text-cyan-400'; ?>">
        <i data-lucide="<?= $item['icon']; ?>" class="w-5 h-5"></i>
        <?= $item['title']; ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <!-- LOGOUT -->
  <div class="px-4 pb-6 mt-auto">
    <a href="../auth/logout.php" id="logoutBtn"
      class="flex items-center justify-center gap-3 bg-gradient-to-r from-red-600 to-red-700 
      hover:from-red-700 hover:to-red-800 text-white font-semibold rounded-lg px-4 py-2.5 
      transition-all duration-150 shadow-md hover:scale-[1.03]">
      <i data-lucide="log-out" class="w-5 h-5"></i> Keluar
    </a>
  </div>
</aside>

<!-- ICONS -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const openBtn = document.getElementById('openSidebar');
  const closeBtn = document.getElementById('closeSidebar');

  openBtn.addEventListener('click', () => {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
  });

  const closeSidebar = () => {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
  };
  closeBtn.addEventListener('click', closeSidebar);
  overlay.addEventListener('click', closeSidebar);

  // Logout confirmation
  logoutBtn.addEventListener('click', (e) => {
    e.preventDefault();
    Swal.fire({
      title: 'Keluar dari Sistem?',
      text: 'Anda yakin ingin logout dari akun admin?',
      icon: 'question',
      background: 'linear-gradient(135deg, #0A1D4A 0%, #1445b8 50%, #059669 100%)',
      color: '#fff',
      showCancelButton: true,
      confirmButtonColor: '#e11d48',
      cancelButtonColor: '#22c55e',
      confirmButtonText: 'Ya, Keluar',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      customClass: {
        popup: 'rounded-2xl shadow-2xl border border-white/20 backdrop-blur-md',
        confirmButton: 'text-white font-semibold px-5 py-2 rounded-lg shadow-lg hover:scale-105 transition-all',
        cancelButton: 'text-white font-semibold px-5 py-2 rounded-lg shadow-lg hover:scale-105 transition-all'
      }
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Sedang keluar...',
          text: 'Harap tunggu sebentar',
          background: '#0A1D4A',
          color: '#fff',
          timer: 800,
          showConfirmButton: false,
          willClose: () => {
            window.location.href = logoutBtn.href;
          }
        });
      }
    });
  });
</script>

<!-- Responsive Fix -->
<style>
@media (max-width: 768px) {
  /* Sidebar 75% layar */
  #sidebar {
    width: 75% !important;
  }

  /* Tombol X di kanan sejajar logo */
  #closeSidebar {
    right: 1.25rem !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    position: absolute !important;
  }

  /* Header tetap rata tengah */
  #sidebar .flex.items-center.justify-between {
    position: relative !important;
    align-items: center !important;
  }
}
</style>
