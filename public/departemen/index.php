<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php'; // ✅ untuk log aktivitas

// ====== Tambah atau Edit Data ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'] ?? null;
  $nama = mysqli_real_escape_string($conn, $_POST['nama_departemen']);

  if ($id) {
    $query = "UPDATE departemen SET nama_departemen='$nama' WHERE id='$id'";
    $msg = "updated";
    $aksi = "Edit Departemen";
    $keterangan = "Admin mengubah nama departemen (ID: $id) menjadi '$nama'";
  } else {
    $query = "INSERT INTO departemen (nama_departemen) VALUES ('$nama')";
    $msg = "added";
    $aksi = "Tambah Departemen";
    $keterangan = "Admin menambahkan departemen baru '$nama'";
  }

  if (mysqli_query($conn, $query)) {
    logAktivitas($conn, $aksi, $keterangan);
    header("Location: index.php?status=$msg");
    exit;
  } else {
    die("Gagal menyimpan data: " . mysqli_error($conn));
  }
}

// ====== Hapus Data ======
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $get = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama_departemen FROM departemen WHERE id='$id'"));
  $nama = $get['nama_departemen'] ?? 'Tidak diketahui';

  mysqli_query($conn, "DELETE FROM departemen WHERE id='$id'");
  logAktivitas($conn, 'Hapus Departemen', "Admin menghapus departemen '$nama' (ID: $id)");
  header("Location: index.php?status=deleted");
  exit;
}

// ====== Ambil Data ======
$result = mysqli_query($conn, "SELECT * FROM departemen ORDER BY id ASC");

// ✅ Include komponen tampilan
include '../../includes/sidebar.php';
include '../../includes/topbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Departemen | E-Archive PT Asrindo</title>
  <link rel="stylesheet" href="../../src/output.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#f4f7fe]">
    <?php include '../../includes/loader.php'; ?>
<div class="ml-64 p-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-semibold text-gray-800">Data Departemen</h1>
      <p class="text-gray-500 text-sm">Kelola daftar departemen perusahaan</p>
    </div>
    <button id="addBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
      <i data-lucide="plus" class="w-4 h-4 inline"></i> Tambah Departemen
    </button>
  </div>

  <!-- ALERT -->
  <?php if (isset($_GET['status'])): ?>
    <?php
      $alert = [
        'added' => ['#E6F6ED','#22C55E','#166534','Departemen berhasil ditambahkan!'],
        'updated' => ['#DBEAFE','#3B82F6','#1E3A8A','Departemen berhasil diperbarui!'],
        'deleted' => ['#FEE2E2','#EF4444','#991B1B','Departemen berhasil dihapus!']
      ];
      [$bg,$border,$text,$msg] = $alert[$_GET['status']] ?? ['#FFF','#000','#000',''];
    ?>
    <div class="auto-hide flex items-center justify-between px-4 py-3 rounded-md mb-4"
      style="background-color:<?= $bg ?>; border:1px solid <?= $border ?>; color:<?= $text ?>;">
      <div class="flex items-center space-x-2">
        <i data-lucide="check-circle" class="w-5 h-5"></i>
        <p><strong>Berhasil!</strong> <?= $msg ?></p>
      </div>
      <button onclick="this.parentElement.remove()" class="font-bold text-lg leading-none">&times;</button>
    </div>
  <?php endif; ?>

  <!-- TABLE -->
  <div class="bg-white rounded-xl shadow-md overflow-x-auto sm:p-4">
    <table class="w-full text-sm text-left text-gray-700">
      <thead class="bg-gradient-to-r from-[#00AEEF] to-[#0072FF] text-white">
        <tr>
          <th class="py-3 px-4 font-semibold">No</th>
          <th class="py-3 px-4 font-semibold">Nama Departemen</th>
          <th class="py-3 px-4 font-semibold text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
          <tr class="hover:bg-gray-50 transition">
            <td class="py-3 px-4"><?= $no++; ?></td>
            <td class="py-3 px-4"><?= htmlspecialchars($row['nama_departemen']); ?></td>
            <td class="py-3 px-4 text-center">
              <div class="flex justify-center items-center gap-4">
                <button 
                  class="text-blue-600 hover:text-blue-800 editBtn"
                  data-id="<?= $row['id'] ?>"
                  data-nama="<?= htmlspecialchars($row['nama_departemen']) ?>"
                  title="Edit">
                  <i data-lucide="pencil"></i>
                </button>
                <button 
                  type="button"
                  data-id="<?= $row['id'] ?>"
                  data-nama="<?= htmlspecialchars($row['nama_departemen']) ?>"
                  class="text-red-600 hover:text-red-800 deleteBtn"
                  title="Hapus">
                  <i data-lucide="trash-2"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL TAMBAH / EDIT -->
<div id="modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-xl p-6 shadow-xl w-[350px] animate-fadeIn">
    <h2 id="modalTitle" class="text-lg font-bold text-gray-800 mb-4">Tambah Departemen</h2>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="id" id="departemenId">
      <div>
        <label class="block text-sm text-gray-600 mb-1">Nama Departemen</label>
        <input type="text" name="nama_departemen" id="namaDepartemen"
               required
               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="flex justify-end gap-2 pt-3">
        <button type="button" id="closeModal"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-semibold">Batal</button>
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL HAPUS -->
<div id="confirmModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-xl p-6 shadow-xl max-w-sm text-center animate-fadeIn">
    <h2 class="text-lg font-bold text-gray-800 mb-2">Konfirmasi Hapus</h2>
    <p id="confirmText" class="text-gray-600 mb-5"></p>
    <div class="flex justify-center gap-3">
      <button type="button" id="cancelDelete"
        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-semibold">Batal</button>
      <a id="confirmDeleteLink"
        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold cursor-pointer">Hapus</a>
    </div>
  </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
lucide.createIcons();

// Modal tambah/edit
const modal = document.getElementById('modal');
const addBtn = document.getElementById('addBtn');
const closeModal = document.getElementById('closeModal');
const modalTitle = document.getElementById('modalTitle');
const departemenId = document.getElementById('departemenId');
const namaDepartemen = document.getElementById('namaDepartemen');

// Modal hapus
const confirmModal = document.getElementById('confirmModal');
const confirmText = document.getElementById('confirmText');
const confirmDeleteLink = document.getElementById('confirmDeleteLink');
const cancelDelete = document.getElementById('cancelDelete');

// Tambah
addBtn.addEventListener('click', () => {
  modalTitle.textContent = "Tambah Departemen";
  departemenId.value = "";
  namaDepartemen.value = "";
  modal.classList.remove('hidden');
  modal.classList.add('flex');
});

// Edit
document.querySelectorAll('.editBtn').forEach(btn => {
  btn.addEventListener('click', () => {
    modalTitle.textContent = "Edit Departemen";
    departemenId.value = btn.dataset.id;
    namaDepartemen.value = btn.dataset.nama;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  });
});

// Hapus
document.querySelectorAll('.deleteBtn').forEach(btn => {
  btn.addEventListener('click', () => {
    const nama = btn.dataset.nama;
    const id = btn.dataset.id;
    confirmText.textContent = `Apakah Anda yakin ingin menghapus departemen "${nama}"?`;
    confirmDeleteLink.href = `?delete=${id}`;
    confirmModal.classList.remove('hidden');
    confirmModal.classList.add('flex');
  });
});

// Tutup modal
closeModal.addEventListener('click', () => modal.classList.add('hidden'));
cancelDelete.addEventListener('click', () => confirmModal.classList.add('hidden'));

// Auto-hide alert
document.querySelectorAll(".auto-hide").forEach(box => {
  setTimeout(() => {
    box.style.transition = "opacity 0.6s ease";
    box.style.opacity = "0";
    setTimeout(() => box.remove(), 600);
  }, 3000);
});
</script>

<style>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn { animation: fadeIn 0.25s ease-out; }

/* ✅ FIX KHUSUS MOBILE - Desktop tidak berubah sama sekali */
@media (max-width: 768px) {
  /* Hilangkan margin kiri di mobile biar ga ketindihan sidebar */
  .ml-64 { margin-left: 0 !important; }

  /* Pastikan tidak ada scroll horizontal */
  body { overflow-x: hidden; }

  /* Table bisa discroll horizontal tanpa ganggu layout */
  table {
    display: block;
    width: 100%;
    overflow-x: auto;
    white-space: nowrap;
  }

  /* Modal biar muat di layar kecil tapi tetap elegan */
  #modal > div,
  #confirmModal > div {
    width: 90% !important;
    max-width: 400px !important;
  }
}
</style>


<?php include '../../includes/footer.php'; ?>
</body>
</html>
