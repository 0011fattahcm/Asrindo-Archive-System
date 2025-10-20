<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/sidebar.php';
include '../../includes/topbar.php';

// Pastikan ID ada
if (!isset($_GET['id'])) {
  echo "<script>alert('ID surat tidak ditemukan!'); window.location.href='index.php';</script>";
  exit;
}

$id = $_GET['id'];

// Ambil data surat masuk
$query = mysqli_query($conn, "
  SELECT sm.*, d.nama_departemen
  FROM surat_masuk sm
  LEFT JOIN departemen d ON sm.departemen_id = d.id
  WHERE sm.id = '$id'
");

if (!$query || mysqli_num_rows($query) == 0) {
  echo "<script>alert('Data surat tidak ditemukan!'); window.location.href='index.php';</script>";
  exit;
}

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Surat Masuk | E-Archive PT Asrindo</title>
  <link rel="stylesheet" href="../../src/output.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#f4f7fe] min-h-screen">
  <?php include '../../includes/loader.php'; ?>

  <main class="ml-64 p-6">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Detail Surat Masuk</h1>
        <p class="text-gray-500 text-sm">Informasi lengkap mengenai surat yang dipilih</p>
      </div>
      <a href="index.php"
         class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition flex items-center gap-2">
        <i data-lucide="arrow-left"></i> Kembali
      </a>
    </div>

    <!-- CARD UTAMA -->
    <div class="bg-white shadow-md rounded-xl p-8 border border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kolom kiri -->
        <div>
          <p class="text-gray-500 text-sm">Nomor Surat</p>
          <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['nomor_surat']); ?></p>

          <p class="text-gray-500 text-sm">Tanggal Surat</p>
          <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['tanggal_surat']); ?></p>

          <p class="text-gray-500 text-sm">Tanggal Terima</p>
          <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['tanggal_terima']); ?></p>

          <p class="text-gray-500 text-sm">Pengirim</p>
          <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['pengirim']); ?></p>

          <p class="text-gray-500 text-sm">Departemen</p>
          <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['nama_departemen']); ?></p>
        </div>

        <!-- Kolom kanan -->
        <div>
          <p class="text-gray-500 text-sm">Perihal</p>
          <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['perihal']); ?></p>

          <p class="text-gray-500 text-sm">Ringkasan Surat</p>
          <p class="text-gray-800 leading-relaxed mb-4">
            <?= nl2br(htmlspecialchars($data['ringkasan'] ?? '-')); ?>
          </p>

          <p class="text-gray-500 text-sm">Tanggal Input</p>
          <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['created_at']); ?></p>
        </div>
      </div>

      <!-- PREVIEW LAMPIRAN -->
      <div class="mt-10 border-t pt-6">
        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
          <i data-lucide="paperclip"></i> Lampiran
        </h2>

        <?php if (!empty($data['lampiran'])): ?>
          <?php
            $filePath = '../../uploads/surat_masuk/' . $data['lampiran'];
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
                <div class="flex gap-3 mt-4 flex-wrap">
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
                <i data-lucide="x-circle" class="w-5 h-5"></i> File tidak ditemukan di folder <b>uploads/surat_masuk</b>.
              </div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="bg-gray-50 border rounded-lg p-6 text-center text-gray-500">
            <i data-lucide="alert-circle" class="w-10 h-10 mx-auto mb-3 text-gray-400"></i>
            Tidak ada lampiran untuk surat ini.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <?php include '../../includes/footer.php'; ?>

  <script>lucide.createIcons();</script>

  <!-- RESPONSIVE MOBILE FIX -->
  <style>
  @media (max-width: 768px) {
    .ml-64 { margin-left: 0 !important; }
    main { padding: 1rem !important; }
    body { overflow-x: hidden; }

    iframe { height: 400px !important; }

    .grid {
      grid-template-columns: 1fr !important;
    }

    .flex.justify-between.items-center {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;
    }
  }
  /* ✅ Perapihan posisi tombol X tanpa ubah tampilan mobile */
@media (max-width: 768px) {
  #closeSidebar {
    position: absolute;
    top: 1.5rem; /* sejajar dengan logo */
    right: 1.25rem;
    transform: none;
  }
}
  </style>
</body>
</html>
