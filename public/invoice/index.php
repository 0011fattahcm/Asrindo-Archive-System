<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/sidebar.php';
include '../../includes/topbar.php';

// ======================
// FILTER & PAGINATION
// ======================
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$where = [];
if ($search != '') {
  $where[] = "(nomor_invoice LIKE '%$search%' OR klien LIKE '%$search%')";
}
if ($startDate != '' && $endDate != '') {
  $where[] = "(tanggal BETWEEN '$startDate' AND '$endDate')";
}
$whereSQL = count($where) > 0 ? 'WHERE deleted_at IS NULL AND ' . implode(' AND ', $where) : 'WHERE deleted_at IS NULL';

$limitOptions = [20, 50, 100];
$limit = isset($_GET['limit']) && in_array($_GET['limit'], $limitOptions) ? (int)$_GET['limit'] : 20;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM invoice $whereSQL";
$countResult = mysqli_query($conn, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Ambil data
$query = "SELECT * FROM invoice $whereSQL ORDER BY tanggal DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice | E-Archive PT Asrindo</title>
  <link rel="icon" href="../assets/img/logo.png">
  <link rel="stylesheet" href="../../src/output.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#f4f7fe] min-h-screen text-gray-800">
  <?php include '../../includes/loader.php'; ?>

  <div class="ml-64 flex flex-col min-h-screen transition-all duration-300">
    <main class="flex-1 p-6 md:p-8">
      <!-- HEADER -->
      <div class="flex justify-between items-center mb-8 flex-wrap gap-3">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Invoice</h1>
          <p class="text-gray-500 text-sm">Kelola seluruh invoice perusahaan</p>
        </div>
        <a href="tambah.php"
          class="bg-gradient-to-r from-[#00AEEF] to-[#0072FF] hover:from-[#0094E0] hover:to-[#005FCC]
                 text-white font-semibold px-4 py-2.5 rounded-lg shadow transition-all flex items-center gap-2">
          <i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Invoice
        </a>
      </div>

      <!-- FILTER -->
      <div class="flex flex-wrap justify-between items-center mb-6 gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-2">
          <input type="text" name="search" placeholder="Cari invoice..."
            value="<?= htmlspecialchars($search) ?>"
            class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 w-64">

          <div class="flex items-center gap-2">
            <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            <span class="text-gray-500 text-sm">s/d</span>
            <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
          </div>

          <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-all">
            Filter
          </button>
        </form>
      </div>

      <!-- EXPORT EXCEL -->
      <div class="w-full bg-white rounded-xl shadow p-5 mb-6 border border-gray-100">
        <form action="../laporan/export_invoice.php" method="get"
          class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex flex-wrap items-center gap-3">
            <div>
              <label class="text-sm font-medium text-gray-600">Dari Tanggal</label>
              <input type="date" name="start_date" required
                class="mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 transition">
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Sampai</label>
              <input type="date" name="end_date" required
                class="mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 transition">
            </div>
          </div>
          <button type="submit"
            class="flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-blue-600 hover:from-emerald-600 hover:to-blue-700
                   text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all duration-200 hover:scale-[1.02]">
            <i data-lucide="file-spreadsheet" class="w-5 h-5"></i> Export Excel
          </button>
        </form>
      </div>

      <!-- ALERT -->
      <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success'): ?>
          <div class="auto-hide flex items-center justify-between px-4 py-3 rounded-md mb-4 bg-emerald-100 border border-emerald-400 text-emerald-700">
            <p><strong>Berhasil!</strong> Invoice berhasil ditambahkan.</p>
            <button onclick="this.parentElement.remove()" class="font-bold text-lg leading-none">&times;</button>
          </div>
        <?php elseif ($_GET['status'] == 'error'): ?>
          <div class="auto-hide flex items-center justify-between px-4 py-3 rounded-md mb-4 bg-yellow-100 border border-yellow-400 text-yellow-700">
            <p><strong>Gagal!</strong> Terjadi kesalahan, coba lagi.</p>
            <button onclick="this.parentElement.remove()" class="font-bold text-lg leading-none">&times;</button>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <!-- TABEL -->
      <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700 min-w-[700px]">
          <thead class="bg-gradient-to-r from-[#00AEEF] to-[#0072FF] text-white">
            <tr>
              <th class="py-3 px-4">No</th>
              <th class="py-3 px-4">Nomor Invoice</th>
              <th class="py-3 px-4">Tanggal</th>
              <th class="py-3 px-4">Klien</th>
              <th class="py-3 px-4">Jumlah</th>
              <th class="py-3 px-4 text-center">Status</th>
              <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php
            $no = $offset + 1;
            if (mysqli_num_rows($result) > 0):
              while ($row = mysqli_fetch_assoc($result)):
                $statusClass = [
                  'Belum Dibayar' => 'bg-yellow-100 text-yellow-700',
                  'Lunas' => 'bg-emerald-100 text-emerald-700',
                  'Overdue' => 'bg-red-100 text-red-700'
                ];
                $class = $statusClass[$row['status']] ?? 'bg-gray-100 text-gray-700';
            ?>
              <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4"><?= $no++; ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($row['nomor_invoice']); ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($row['tanggal']); ?></td>
                <td class="py-3 px-4"><?= htmlspecialchars($row['klien']); ?></td>
                <td class="py-3 px-4">Rp <?= number_format($row['jumlah'], 2, ',', '.'); ?></td>
                <td class="py-3 px-4 text-center">
                  <span class="px-3 py-1 rounded-lg font-medium <?= $class; ?>"><?= $row['status']; ?></span>
                </td>
                <td class="py-3 px-4 text-center">
                  <div class="flex justify-center items-center gap-4">
                    <a href="detail.php?id=<?= $row['id']; ?>" class="text-emerald-600 hover:text-emerald-800" title="Detail">
                      <i data-lucide="eye"></i>
                    </a>
                    <a href="edit.php?id=<?= $row['id']; ?>" class="text-blue-600 hover:text-blue-800" title="Edit">
                      <i data-lucide="pencil"></i>
                    </a>
                    <a href="#" onclick="event.preventDefault(); showConfirm('Yakin ingin menghapus invoice ini?', function(){ window.location.href='hapus.php?id=<?= $row['id']; ?>'; });"
                       class="text-red-600 hover:text-red-800" title="Hapus">
                      <i data-lucide="trash-2"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="7" class="text-center text-gray-500 py-4">Belum ada data invoice</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="flex flex-wrap justify-between items-center mt-6 text-sm text-gray-600 gap-4">
        <div>
          Menampilkan <b><?= $offset + 1 ?></b>–<b><?= min($offset + $limit, $totalRows) ?></b> dari <b><?= $totalRows ?></b> invoice
        </div>
        <div class="flex items-center gap-3 flex-wrap">
          <form method="GET" class="flex items-center gap-2">
            <label for="limit">Tampilkan</label>
            <select name="limit" id="limit" class="border border-gray-300 rounded-md px-2 py-1 text-sm"
              onchange="this.form.submit()">
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
              <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>&search=<?= $search ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>"
                 class="px-3 py-1 border rounded-lg hover:bg-gray-100">Prev</a>
            <?php else: ?>
              <span class="px-3 py-1 border rounded-lg text-gray-400">Prev</span>
            <?php endif; ?>

            <span>Halaman <b><?= $page ?></b> / <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
              <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>&search=<?= $search ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>"
                 class="px-3 py-1 border rounded-lg hover:bg-gray-100">Next</a>
            <?php else: ?>
              <span class="px-3 py-1 border rounded-lg text-gray-400">Next</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>

  <?php include '../../includes/footer.php'; ?>
  <script>lucide.createIcons();</script>

  <!-- RESPONSIVE FIX -->
  <style>
  @media (max-width:768px){
    .ml-64{margin-left:0!important;}
    body{overflow-x:hidden;}
    main{padding:1rem!important;}
    table{display:block;width:100%;overflow-x:auto;white-space:nowrap;}
    .flex.justify-between.items-center{flex-direction:column;align-items:flex-start;gap:1rem;}
    #closeSidebar{position:absolute;top:50%;right:20px;transform:translateY(-50%);z-index:70;}
    #sidebar{width:75%!important;max-width:280px!important;z-index:90;}
    #openSidebar{z-index:80;}
    #overlay{backdrop-filter:blur(3px);}
  }
  </style>
</body>
</html>
