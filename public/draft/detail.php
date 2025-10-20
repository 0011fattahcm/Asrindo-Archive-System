<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/sidebar.php';
include '../../includes/topbar.php';

// Pastikan ID ada
if (!isset($_GET['id'])) {
  echo "<script>alert('ID draft tidak ditemukan!'); window.location.href='index.php';</script>";
  exit;
}

$id = $_GET['id'];

// Ambil data draft
$query = mysqli_query($conn, "SELECT * FROM draft WHERE id = '$id'");

if (!$query || mysqli_num_rows($query) == 0) {
  echo "<script>alert('Data draft tidak ditemukan!'); window.location.href='index.php';</script>";
  exit;
}

$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Detail Draft Dokumen | E-Archive PT Asrindo</title>
  <link rel="icon" href="../assets/img/logo.png">
  <link rel="stylesheet" href="../../src/output.css">
  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-[#f4f7fe] min-h-screen text-gray-800">
<?php include '../../includes/loader.php'; ?>

  <div class="ml-64 flex flex-col min-h-screen transition-all duration-300">
    <main class="flex-1 p-6 md:p-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8 flex-wrap gap-3">
        <div>
          <h1 class="text-2xl font-bold text-gray-800">Detail Draft Dokumen</h1>
          <p class="text-gray-500 text-sm">Informasi lengkap mengenai draft dokumen ini</p>
        </div>
        <a href="index.php"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition flex items-center gap-2">
          <i data-lucide="arrow-left"></i> Kembali
        </a>
      </div>

      <!-- Card utama -->
      <div class="bg-white shadow-lg rounded-xl p-8 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <p class="text-gray-500 text-sm">Judul Dokumen</p>
            <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['judul']); ?></p>

            <p class="text-gray-500 text-sm">Deskripsi</p>
            <p class="text-gray-800 leading-relaxed mb-4">
              <?= nl2br(htmlspecialchars($data['deskripsi'] ?? '-')); ?>
            </p>
          </div>

          <div>
            <p class="text-gray-500 text-sm">Status</p>
            <?php if ($data['status'] == 'final'): ?>
              <span class="inline-flex items-center gap-2 bg-emerald-100 text-emerald-700 font-semibold px-3 py-1.5 rounded-lg mb-4">
                <i data-lucide="check-circle" class="w-4 h-4"></i> FINAL
              </span>
            <?php else: ?>
              <span class="inline-flex items-center gap-2 bg-yellow-100 text-yellow-700 font-semibold px-3 py-1.5 rounded-lg mb-4">
                <i data-lucide="file-text" class="w-4 h-4"></i> DRAFT
              </span>
            <?php endif; ?>

            <p class="text-gray-500 text-sm mt-4">Tanggal Dibuat</p>
            <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['created_at']); ?></p>

            <?php if (!empty($data['updated_at'])): ?>
              <p class="text-gray-500 text-sm">Terakhir Diperbarui</p>
              <p class="font-semibold text-gray-800 mb-4"><?= htmlspecialchars($data['updated_at']); ?></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Preview Lampiran -->
        <div class="mt-10 border-t pt-6">
          <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2 mb-4">
            <i data-lucide="paperclip"></i> Lampiran
          </h2>

          <?php $lampiran = $data['lampiran'] ?? null; ?>

          <?php if (!empty($lampiran)): ?>
            <?php
              $filePath = '../../uploads/draft/' . $lampiran;
              $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            ?>

            <div class="bg-gray-50 border rounded-lg p-4">
              <p class="text-sm text-gray-700 mb-3 flex items-center gap-2">
                <i data-lucide="file"></i> <?= htmlspecialchars($lampiran); ?>
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
                  <i data-lucide="x-circle" class="w-5 h-5"></i> File tidak ditemukan di folder <b>uploads/draft</b>.
                </div>
              <?php endif; ?>
            </div>

          <?php else: ?>
            <div class="bg-gray-50 border rounded-lg p-6 text-center text-gray-500">
              <i data-lucide="alert-circle" class="w-10 h-10 mx-auto mb-3 text-gray-400"></i>
              Tidak ada lampiran untuk draft ini.
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
    iframe,img{max-height:400px!important;}
    #closeSidebar{position:absolute;top:50%;right:20px;transform:translateY(-50%);z-index:70;}
    #sidebar{width:75%!important;max-width:280px!important;z-index:90;}
    #openSidebar{z-index:80;}
    #overlay{backdrop-filter:blur(3px);}
  }
  </style>
</body>
</html>
