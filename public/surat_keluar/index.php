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
  $where[] = "(nomor_surat LIKE '%$search%' OR penerima LIKE '%$search%' OR perihal LIKE '%$search%')";
}
if ($startDate != '' && $endDate != '') {
  $where[] = "(tanggal_surat BETWEEN '$startDate' AND '$endDate')";
}
$whereSQL = count($where) > 0 ? 'WHERE deleted_at IS NULL AND ' . implode(' AND ', $where) : 'WHERE deleted_at IS NULL';

$limitOptions = [20, 50, 100];
$limit = isset($_GET['limit']) && in_array($_GET['limit'], $limitOptions) ? (int)$_GET['limit'] : 20;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$countQuery = "SELECT COUNT(*) AS total FROM surat_keluar $whereSQL";
$countResult = mysqli_query($conn, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Ambil data sesuai filter & pagination
$query = "SELECT surat_keluar.*, departemen.nama_departemen 
          FROM surat_keluar 
          LEFT JOIN departemen ON surat_keluar.departemen_id = departemen.id 
          $whereSQL 
          ORDER BY surat_keluar.tanggal_surat DESC 
          LIMIT $offset, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Surat Keluar | E-Archive PT Asrindo</title>
  <link rel="icon" href="../assets/img/logo.png">
  <link rel="stylesheet" href="../../src/output.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#f4f7fe] min-h-screen text-gray-800">
  <?php include '../../includes/loader.php'; ?>

  <div class="ml-64 flex flex-col min-h-screen transition-all duration-300">
    <main class="flex-1 p-6 md:p-8">
      <!-- HEADER -->
      <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Surat Keluar</h1>
          <p class="text-gray-500 text-sm">Kelola semua arsip surat keluar perusahaan</p>
        </div>
        <a href="tambah.php"
           class="bg-gradient-to-r from-[#00AEEF] to-[#0072FF] hover:from-[#0094E0] hover:to-[#005FCC] 
           text-white font-semibold px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2">
          <i data-lucide="plus-circle" class="w-5 h-5"></i> Tambah Surat
        </a>
      </div>

      <!-- FILTER -->
      <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5 mb-6">
        <form method="GET" action="" class="flex flex-wrap items-center gap-3">
          <input type="text" name="search" placeholder="Cari surat..." value="<?= htmlspecialchars($search) ?>"
                 class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 w-64 flex-grow md:flex-none">

          <div class="flex items-center gap-2 flex-wrap">
            <input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
            <span class="text-gray-500 text-sm">s/d</span>
            <input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>"
                   class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
          </div>

          <button type="submit"
                  class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm transition">
            Filter
          </button>
        </form>
      </div>

      <!-- EXPORT -->
      <div class="w-full bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
        <form action="../laporan/export_surat_keluar.php" method="get"
              class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex flex-wrap items-center gap-3">
            <div>
              <label class="text-sm font-medium text-gray-600">Dari Tanggal</label>
              <input type="date" name="start_date" required
                     class="mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">Sampai</label>
              <input type="date" name="end_date" required
                     class="mt-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
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
        <?php
        $alert = [
          'success' => ['#E6F6ED','#22C55E','#166534','Surat berhasil ditambahkan.'],
          'error'   => ['#FEF3C7','#F59E0B','#92400E','Terjadi kesalahan, coba lagi.']
        ];
        if (isset($alert[$_GET['status']])) {
          [$bg, $border, $text, $msg] = $alert[$_GET['status']];
        ?>
          <div class="auto-hide flex items-center justify-between px-4 py-3 rounded-md mb-4"
               style="background-color:<?= $bg ?>;border:1px solid <?= $border ?>;color:<?= $text ?>;">
            <div class="flex items-center gap-2">
              <i data-lucide="info" class="w-5 h-5"></i>
              <p><strong><?= ucfirst($_GET['status']) ?>!</strong> <?= $msg ?></p>
            </div>
            <button onclick="this.parentElement.remove()" class="font-bold text-lg leading-none">&times;</button>
          </div>
        <?php } ?>
      <?php endif; ?>

      <!-- TABEL -->
      <div class="bg-white rounded-xl shadow-md overflow-x-auto border border-gray-100">
        <table class="w-full text-sm text-left text-gray-700 min-w-[700px]">
          <thead class="bg-gradient-to-r from-[#00AEEF] to-[#0072FF] text-white">
            <tr>
              <th class="py-3 px-4 font-semibold">No</th>
              <th class="py-3 px-4 font-semibold">Nomor Surat</th>
              <th class="py-3 px-4 font-semibold">Tanggal Surat</th>
              <th class="py-3 px-4 font-semibold">Penerima</th>
              <th class="py-3 px-4 font-semibold">Perihal</th>
              <th class="py-3 px-4 font-semibold">Departemen</th>
              <th class="py-3 px-4 font-semibold text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php
            $no = $offset + 1;
            if (mysqli_num_rows($result) > 0):
              while ($row = mysqli_fetch_assoc($result)): ?>
                <tr class="hover:bg-gray-50 transition">
                  <td class="py-3 px-4"><?= $no++; ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['nomor_surat']); ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['tanggal_surat']); ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['penerima']); ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['perihal']); ?></td>
                  <td class="py-3 px-4"><?= htmlspecialchars($row['nama_departemen']); ?></td>
                  <td class="py-3 px-4 text-center">
                    <div class="flex justify-center items-center gap-4">
                      <a href="detail.php?id=<?= $row['id']; ?>" class="text-emerald-600 hover:text-emerald-800" title="Lihat Detail"><i data-lucide="eye"></i></a>
                      <a href="edit.php?id=<?= $row['id']; ?>" class="text-blue-600 hover:text-blue-800" title="Edit Surat"><i data-lucide="pencil"></i></a>
                      <a href="#" onclick="event.preventDefault(); showConfirm('Yakin ingin menghapus surat ini?', function(){ window.location.href='hapus.php?id=<?= $row['id']; ?>'; });" class="text-red-600 hover:text-red-800" title="Hapus Surat"><i data-lucide="trash-2"></i></a>
                    </div>
                  </td>
                </tr>
              <?php endwhile; else: ?>
              <tr><td colspan="7" class="py-4 text-center text-gray-500">Belum ada data surat keluar</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- PAGINATION -->
      <div class="flex justify-between items-center mt-6 text-sm text-gray-600 flex-wrap gap-3">
        <div>
          Menampilkan <b><?= $offset + 1 ?></b>–<b><?= min($offset + $limit, $totalRows) ?></b> dari
          <b><?= $totalRows ?></b> surat
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
          </form>

          <div class="flex items-center gap-2">
            <?php if ($page > 1): ?>
              <a href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>" class="px-3 py-1 border rounded-lg hover:bg-gray-100">Prev</a>
            <?php else: ?>
              <span class="px-3 py-1 border rounded-lg text-gray-400">Prev</span>
            <?php endif; ?>

            <span>Halaman <b><?= $page ?></b> / <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
              <a href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>" class="px-3 py-1 border rounded-lg hover:bg-gray-100">Next</a>
            <?php else: ?>
              <span class="px-3 py-1 border rounded-lg text-gray-400">Next</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>

  <?php include '../../includes/footer.php'; ?>

  <script>
    lucide.createIcons();
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
  @media (max-width: 768px) {
    .ml-64 { margin-left: 0 !important; }
    body { overflow-x: hidden; }
    main { padding: 1rem !important; }

    table { display: block; width: 100%; overflow-x: auto; white-space: nowrap; }

    .flex.justify-between.items-center { flex-direction: column; align-items: flex-start; }

    #closeSidebar {
      position: absolute;
      top: 50%;
      right: 20px;
      transform: translateY(-50%);
      z-index: 70;
    }
    #sidebar { width: 75% !important; max-width: 280px !important; z-index: 90; }
    #openSidebar { z-index: 80; }
    #overlay { backdrop-filter: blur(3px); }
  }
  </style>
</body>
</html>
