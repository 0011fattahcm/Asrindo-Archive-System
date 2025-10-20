<footer class="bg-[#0A3A75] text-white mt-10 md:ml-64 border-t-4 border-[#EAC65B] shadow-inner">
  <div class="container mx-auto px-4 py-6 text-center text-sm">
    <p class="font-semibold text-lg mb-1">PT Asrindo Environt Investama</p>
    <p class="text-gray-200 mb-2">E-Archive Management System</p>

    <hr class="border-t border-[#EAC65B]/40 w-2/3 mx-auto mb-4" />

    <p class="text-gray-200">Perum Citra Garden BMW Blok B 01 No. 1, Serang, Banten</p>
    <p class="text-gray-300 mt-1">
      © <?php echo date('Y'); ?> PT Asrindo Environt Investama — All Rights Reserved
    </p>
  </div>
</footer>

<!-- ICONS -->
<script src="https://kit.fontawesome.com/4f7d2f4a4b.js" crossorigin="anonymous"></script>
</body>
<!-- ====================== MODAL KONFIRMASI ====================== -->
<div id="confirmModal"
  class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[9999] backdrop-blur-sm transition">
  <div class="bg-white rounded-2xl shadow-lg p-6 w-[90%] max-w-md text-center animate-[fadeIn_0.3s_ease]">
    <i data-lucide="alert-triangle" class="mx-auto w-12 h-12 text-amber-500 mb-4"></i>
    <h2 class="text-lg font-semibold text-gray-800 mb-2">Konfirmasi</h2>
    <p id="confirmMessage" class="text-gray-600 mb-6">Yakin ingin melanjutkan?</p>
    <div class="flex justify-center gap-3">
      <button id="confirmCancel"
        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-lg transition-all">
        Batal
      </button>
      <button id="confirmOk"
        class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white font-medium px-4 py-2 rounded-lg shadow transition-all">
        Ya, Hapus
      </button>
    </div>
  </div>
</div>

<!-- ====================== MODAL NOTIFIKASI ====================== -->
<div id="alertModal"
  class="fixed inset-0 bg-black/30 hidden items-center justify-center z-[9999] backdrop-blur-sm">
  <div class="bg-white rounded-2xl shadow-lg p-6 w-[90%] max-w-md text-center animate-[fadeIn_0.3s_ease]">
    <div id="alertIcon" class="mx-auto mb-4"></div>
    <p id="alertMessage" class="text-gray-700 text-sm mb-4"></p>
    <button id="alertOk"
      class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium px-5 py-2.5 rounded-lg transition-all">
      OK
    </button>
  </div>
</div>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>
<script>
  const confirmModal = document.getElementById('confirmModal');
  const confirmMessage = document.getElementById('confirmMessage');
  const confirmOk = document.getElementById('confirmOk');
  const confirmCancel = document.getElementById('confirmCancel');

  const alertModal = document.getElementById('alertModal');
  const alertMessage = document.getElementById('alertMessage');
  const alertIcon = document.getElementById('alertIcon');
  const alertOk = document.getElementById('alertOk');

  // === Fungsi konfirmasi ===
  function showConfirm(message, onConfirm) {
    confirmMessage.textContent = message;
    confirmModal.classList.remove('hidden');
    confirmModal.classList.add('flex');

    confirmOk.onclick = () => {
      confirmModal.classList.add('hidden');
      if (typeof onConfirm === 'function') onConfirm();
    };

    confirmCancel.onclick = () => {
      confirmModal.classList.add('hidden');
    };
  }

  // === Fungsi alert ===
  function showAlert(message, type = 'success') {
    alertMessage.textContent = message;
    alertIcon.innerHTML = type === 'success'
      ? `<i data-lucide="check-circle" class="text-emerald-500 w-12 h-12"></i>`
      : `<i data-lucide="x-circle" class="text-red-500 w-12 h-12"></i>`;
    lucide.createIcons();
    alertModal.classList.remove('hidden');
    alertModal.classList.add('flex');
  }

  alertOk.onclick = () => alertModal.classList.add('hidden');
</script>

</html>
