<!-- includes/loader.php -->
<div id="page-loader"
     class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-[#f4f7fe] text-gray-700 transition-opacity duration-700">
  <div class="animate-spin rounded-full h-14 w-14 border-t-4 border-b-4 border-blue-600 mb-4"></div>
  <h2 class="font-semibold text-lg text-gray-600 tracking-wide">Memuat halaman...</h2>
</div>

<script>
// Loader fade out setelah halaman siap
window.addEventListener("load", () => {
  const loader = document.getElementById("page-loader");
  if (loader) {
    loader.style.opacity = "0";
    loader.style.transition = "opacity 0.7s ease";
    setTimeout(() => loader.remove(), 700);
  }
});
</script>
