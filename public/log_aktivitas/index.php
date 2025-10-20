<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/sidebar.php';
include '../../includes/topbar.php';

// ==========================
// FILTER & PAGINATION
// ==========================
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$where = [];
if ($search != '') {
  $where[] = "(aksi LIKE '%$search%' OR keterangan LIKE '%$search%' OR admin_id LIKE '%$search%')";
}
if ($startDate != '' && $endDate != '') {
  $where[] = "(waktu BETWEEN '$startDate' AND '$endDate')";
}
$whereSQL = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$limitOptions = [20, 50, 100];
$limit = isset($_GET['limit']) && in_array($_GET['limit'], $limitOptions) ? (int)$_GET['limit'] : 20;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM log_aktivitas $whereSQL";
$countResult = mysqli_query($conn, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Ambil data log
$query = "SELECT * FROM log_aktivitas $whereSQL ORDER BY waktu DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log Aktivitas | E-Archive PT Asrindo</title>
  <link rel="icon" href="../assets/img/logo.png">
  <link rel="stylesheet" href="../../src/output.css">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-[#f4f7fe] min-h-screen text-gray-800">
<?php include '../../includes/loader.php'; ?>

<div class="ml-64 flex flex-col min-h-screen transition-all duration-300">
  <main class="flex-1 p-6 md:p-8">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8 flex-wrap gap-3">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Log Aktivitas</h1>
        <p class="text-gray-500 text-sm">Riwayat aktivitas admin sistem</p>
      </div>
    </div>

    <!-- FILTER -->
    <div class="bg-white border border-gray-100 shadow rounded-xl p-4 mb-6">
      <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div class="md:col-span-2">
          <input type="text" name="search" placeholder="Cari log..."
            value="<?= htmlspecialchars($search) ?>"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid grid-cols-2 gap-2">
          <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
          <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex md:justify-end">
          <button type="submit"
            class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-all">
            Filter
          </button>
        </div>
      </form>
    </div>

    <!-- NOTIFIKASI -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
      <div class="auto-hide flex items-center justify-between px-4 py-3 rounded-md mb-4 bg-red-50 border border-red-400 text-red-700">
        <div class="flex items-center gap-2">
          <i data-lucide="trash-2" class="w-5 h-5"></i>
          <p><strong>Berhasil!</strong> Log aktivitas berhasil dihapus.</p>
        </div>
        <button onclick="this.parentElement.remove()" class="font-bold text-lg leading-none">&times;</button>
      </div>
    <?php endif; ?>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.auto-hide').forEach(el => {
          setTimeout(() => {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
          }, 3000);
        });
      });
    </script>

    <!-- TABEL -->
    <form id="logForm" action="hapus_log.php" method="POST">
      <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-b">
          <button type="button" id="deleteSelected"
            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 transition">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Terpilih
          </button>
        </div>

        <table class="w-full text-sm text-left text-gray-700 min-w-[800px]">
          <thead class="bg-gradient-to-r from-[#00AEEF] to-[#0072FF] text-white">
            <tr>
              <th class="py-3 px-4 text-center"><input type="checkbox" id="checkAll"></th>
              <th class="py-3 px-4 font-semibold">No</th>
              <th class="py-3 px-4 font-semibold">Aksi</th>
              <th class="py-3 px-4 font-semibold">Keterangan</th>
              <th class="py-3 px-4 font-semibold">Waktu</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php
            $no = $offset + 1;
            if (mysqli_num_rows($result) > 0):
              while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-gray-50 transition">
                  <td class="py-3 px-4 text-center"><input type="checkbox" name="selected[]" value="<?= $row['id']; ?>" class="rowCheck"></td>
                  <td class="py-3 px-4"><?= $no++; ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['aksi']); ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['keterangan']); ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['waktu']); ?></td>
                </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="6" class="py-4 text-center text-gray-500">Belum ada log aktivitas</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </form>

    <!-- PAGINATION -->
    <div class="flex flex-wrap justify-between items-center mt-6 text-sm text-gray-600 gap-4">
      <div>
        Menampilkan <b><?= $offset + 1 ?></b>–<b><?= min($offset + $limit, $totalRows) ?></b> dari <b><?= $totalRows ?></b> log
      </div>

      <div class="flex items-center gap-3 flex-wrap">
        <form method="GET" class="flex items-center gap-2">
          <label for="limit">Tampilkan</label>
          <select name="limit" id="limit" class="border border-gray-300 rounded-md px-2 py-1 text-sm" onchange="this.form.submit()">
            <?php foreach ($limitOptions as $opt): ?>
              <option value="<?= $opt ?>" <?= $limit == $opt ? 'selected' : '' ?>><?= $opt ?></option>
            <?php endforeach; ?>
          </select>
          <span>per halaman</span>
          <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
          <input type="hidden" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
          <input type="hidden" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
        </form>

        <div class="flex items-center gap-2">
          <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&search=<?= $search ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="px-3 py-1 border rounded-lg hover:bg-gray-100">Prev</a>
          <?php else: ?>
            <span class="px-3 py-1 border rounded-lg text-gray-400">Prev</span>
          <?php endif; ?>

          <span>Halaman <b><?= $page ?></b> / <?= $totalPages ?></span>

          <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&search=<?= $search ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="px-3 py-1 border rounded-lg hover:bg-gray-100">Next</a>
          <?php else: ?>
            <span class="px-3 py-1 border rounded-lg text-gray-400">Next</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </main>
</div>

<script>
  lucide.createIcons();

  // Pilih semua
  document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('.rowCheck').forEach(cb => cb.checked = this.checked);
  });

  // SweetAlert konfirmasi hapus
  const deleteBtn = document.getElementById('deleteSelected');
  const form = document.getElementById('logForm');
  deleteBtn.addEventListener('click', () => {
    const checked = document.querySelectorAll('.rowCheck:checked');
    if (checked.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Tidak ada data dipilih!',
        text: 'Pilih minimal satu log untuk dihapus.',
        confirmButtonColor: '#0072FF'
      });
      return;
    }
    Swal.fire({
      title: 'Yakin ingin menghapus?',
      text: `${checked.length} log aktivitas akan dihapus permanen.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e11d48',
      cancelButtonColor: '#22c55e',
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) form.submit();
    });
  });

  // Hapus param "status=deleted" setelah reload
  if (window.location.search.includes("status=deleted")) {
    const newUrl = window.location.origin + window.location.pathname;
    window.history.replaceState({}, document.title, newUrl);
  }
</script>

<?php include '../../includes/footer.php'; ?>

<!-- RESPONSIVE FIX -->
<style>
@media (max-width:768px){
  .ml-64{margin-left:0!important;}
  body{overflow-x:hidden;}
  main{padding:1rem!important;}
  table{display:block;width:100%;overflow-x:auto;white-space:nowrap;}
  #closeSidebar{position:absolute;top:50%;right:20px;transform:translateY(-50%);z-index:70;}
  #sidebar{width:75%!important;max-width:280px!important;z-index:90;}
  #openSidebar{z-index:80;}
  #overlay{backdrop-filter:blur(3px);}
}
</style>

</body>
</html>
