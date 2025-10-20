<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/sidebar.php';
include '../../includes/topbar.php';

// Pastikan ID dikirim
if (!isset($_GET['id'])) {
  echo "<script>alert('ID invoice tidak ditemukan!'); window.location.href='index.php';</script>";
  exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM invoice WHERE id='$id'");
if (!$query || mysqli_num_rows($query) == 0) {
  echo "<script>alert('Data invoice tidak ditemukan!'); window.location.href='index.php';</script>";
  exit;
}
$data = mysqli_fetch_assoc($query);

// Tentukan persentase PPN
$ppnPersen = 0;
if ($data['dpp'] > 0) {
  $rasio = round($data['ppn'] / $data['dpp'], 2);
  if ($rasio == 0.11) $ppnPersen = 11;
  elseif ($rasio == 0.12) $ppnPersen = 12;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Invoice | E-Archive PT Asrindo</title>
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
          <h1 class="text-2xl font-bold text-gray-800">Detail Invoice</h1>
          <p class="text-gray-500 text-sm">Informasi lengkap mengenai invoice</p>
        </div>
        <a href="index.php"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition flex items-center gap-2">
          <i data-lucide="arrow-left"></i> Kembali
        </a>
      </div>

      <!-- CARD DETAIL -->
      <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-8 transition-all duration-300 hover:shadow-xl">
        
        <!-- INFO DASAR -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div>
            <p class="text-gray-500 text-sm">Nomor Invoice</p>
            <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['nomor_invoice']); ?></p>

            <p class="text-gray-500 text-sm">Tanggal</p>
            <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['tanggal']); ?></p>

            <p class="text-gray-500 text-sm">Klien</p>
            <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['klien']); ?></p>
          </div>

          <div>
            <p class="text-gray-500 text-sm">Jumlah Total</p>
            <p class="font-semibold text-gray-800 mb-4">Rp <?= number_format($data['jumlah'], 2, ',', '.'); ?></p>

            <p class="text-gray-500 text-sm">Status Pembayaran</p>
            <?php if ($data['status'] == 'Lunas'): ?>
              <span class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 font-semibold px-3 py-1.5 rounded-lg mb-4">
                <i data-lucide="check-circle" class="w-4 h-4"></i> LUNAS
              </span>
            <?php elseif ($data['status'] == 'Belum Dibayar'): ?>
              <span class="inline-flex items-center gap-2 bg-yellow-100 text-yellow-700 font-semibold px-3 py-1.5 rounded-lg mb-4">
                <i data-lucide="clock" class="w-4 h-4"></i> BELUM DIBAYAR
              </span>
            <?php else: ?>
              <span class="inline-flex items-center gap-2 bg-red-100 text-red-700 font-semibold px-3 py-1.5 rounded-lg mb-4">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i> OVERDUE
              </span>
            <?php endif; ?>

            <p class="text-gray-500 text-sm mt-4">Tanggal Input</p>
            <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['created_at']); ?></p>
          </div>
        </div>

        <!-- DETAIL TRANSAKSI -->
        <div class="border-t pt-6 mt-2">
          <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
            <i data-lucide="file-text"></i> Detail Transaksi
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <p class="text-gray-500 text-sm">Nama Transaksi</p>
              <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['nama_transaksi']); ?></p>

              <p class="text-gray-500 text-sm">Harga Satuan</p>
              <p class="font-semibold text-gray-800 mb-4">Rp <?= number_format($data['harga_satuan'], 2, ',', '.'); ?></p>

              <p class="text-gray-500 text-sm">Kuantitas</p>
              <p class="font-semibold text-gray-800 mb-4"><?= $data['kuantitas']; ?></p>

              <p class="text-gray-500 text-sm">Total Harga</p>
              <p class="font-semibold text-gray-800 mb-4">Rp <?= number_format($data['total_harga'], 2, ',', '.'); ?></p>
            </div>

            <div>
              <p class="text-gray-500 text-sm">Potongan Harga</p>
              <p class="font-semibold text-gray-800 mb-4">Rp <?= number_format($data['potongan_harga'], 2, ',', '.'); ?></p>

              <p class="text-gray-500 text-sm">DPP</p>
              <p class="font-semibold text-gray-800 mb-4">Rp <?= number_format($data['dpp'], 2, ',', '.'); ?></p>

              <p class="text-gray-500 text-sm">PPN <?= $ppnPersen > 0 ? '(' . $ppnPersen . '%)' : ''; ?></p>
              <p class="font-semibold text-gray-800 mb-4">Rp <?= number_format($data['ppn'], 2, ',', '.'); ?></p>
            </div>
          </div>
        </div>

        <!-- LAMPIRAN -->
        <div class="mt-10 border-t pt-6">
          <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
            <i data-lucide="paperclip"></i> Lampiran
          </h2>

          <?php if (!empty($data['lampiran'])): ?>
            <?php
              $filePath = '../../uploads/invoice/' . $data['lampiran'];
              $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            ?>

            <div class="bg-gray-50 border rounded-lg p-4">
              <p class="text-sm text-gray-700 mb-3 flex items-center gap-2">
                <i data-lucide="file"></i> <?= htmlspecialchars($data['lampiran']); ?>
              </p>

              <?php if (file_exists($filePath)): ?>
                <?php if ($ext === 'pdf'): ?>
                  <iframe src="<?= $filePath; ?>" class="w-full h-[500px] rounded-lg border"></iframe>
                <?php elseif (in_array($ext, ['jpg', 'jpeg', 'png'])): ?>
                  <img src="<?= $filePath; ?>" alt="Lampiran" class="rounded-lg shadow w-full mb-4">
                  <a href="<?= $filePath; ?>" download
                     class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
                     <i data-lucide='download'></i> Unduh Gambar
                  </a>
                <?php else: ?>
                  <div class="flex gap-3 mt-4">
                    <a href="<?= $filePath; ?>" target="_blank"
                       class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-lg transition">
                       <i data-lucide='eye'></i> Buka di Tab Baru
                    </a>
                    <a href="<?= $filePath; ?>" download
                       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
                       <i data-lucide='download'></i> Unduh File
                    </a>
                  </div>
                <?php endif; ?>
              <?php else: ?>
                <div class="text-red-500 text-sm flex items-center gap-2">
                  <i data-lucide="x-circle" class="w-5 h-5"></i> File tidak ditemukan di folder <b>uploads/invoice</b>.
                </div>
              <?php endif; ?>
            </div>
          <?php else: ?>
            <div class="bg-gray-50 border rounded-lg p-6 text-center text-gray-500">
              <i data-lucide="alert-circle" class="w-10 h-10 mx-auto mb-3 text-gray-400"></i>
              Tidak ada lampiran untuk invoice ini.
            </div>
          <?php endif; ?>
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
    .grid{grid-template-columns:1fr!important;}
    .flex.justify-between.items-center{flex-direction:column;align-items:flex-start;gap:1rem;}
    iframe,img{max-height:350px!important;}
    #closeSidebar{position:absolute;top:50%;right:20px;transform:translateY(-50%);z-index:70;}
    #sidebar{width:75%!important;max-width:280px!important;z-index:90;}
    #openSidebar{z-index:80;}
    #overlay{backdrop-filter:blur(3px);}
  }
  </style>
</body>
</html>
