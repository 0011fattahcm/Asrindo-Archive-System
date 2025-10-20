<!-- TOPBAR -->
<nav class="sticky top-0 z-30 bg-white/70 backdrop-blur-md shadow flex items-center justify-between px-4 md:px-6 py-3 transition-all duration-200 md:ml-64">
  <!-- Kiri: Judul Halaman -->
  <div class="flex items-center gap-3">
    <i data-lucide="layout-dashboard" class="w-5 h-5 text-blue-600"></i>
    <h1 class="text-base md:text-lg font-semibold text-gray-800 tracking-tight capitalize">
      <?php echo ucfirst(basename($_SERVER['PHP_SELF'], ".php")); ?>
    </h1>
  </div>

  <!-- Kanan: Notifikasi & User -->
  <div class="flex items-center gap-4">
    <!-- Bell -->
    <button class="p-2 rounded-full hover:bg-gray-200 transition-colors duration-150 focus:outline-none">
      <i class="fa-regular fa-bell text-gray-600 text-lg"></i>
    </button>

    <!-- User Dropdown -->
    <div class="relative">
      <button id="userBtn" class="flex items-center gap-2 focus:outline-none">
        <img src="../../assets/img/logo.png" alt="user" class="w-8 h-8 rounded-full border border-gray-300 object-cover">
        <span class="hidden sm:inline text-gray-700 font-medium">Admin</span>
        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-500 hidden sm:inline"></i>
      </button>

      <!-- Dropdown -->
      <div id="userDropdown"
        class="hidden absolute right-0 mt-3 w-44 bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
        <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profil</a>
        <a href="../auth/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
      </div>
    </div>
  </div>
</nav>

<!-- SCRIPT -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();

  const userBtn = document.getElementById("userBtn");
  const userDropdown = document.getElementById("userDropdown");

  userBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    userDropdown.classList.toggle("hidden");
  });

  document.addEventListener("click", (e) => {
    if (!userBtn.contains(e.target) && !userDropdown.contains(e.target)) {
      userDropdown.classList.add("hidden");
    }
  });
</script>

<!-- RESPONSIVE FIX -->
<style>
@media (max-width: 768px) {
  nav {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
    margin-left: 0 !important;
  }

  nav .flex.items-center.justify-between {
    flex-wrap: nowrap !important;
  }

  /* Judul tetap sejajar di tengah */
  nav h1 {
    font-size: 1rem !important;
  }

  /* User dropdown tetap di kanan atas */
  #userDropdown {
    right: 0 !important;
  }
}
</style>
