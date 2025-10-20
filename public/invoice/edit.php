<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/sidebar.php';
include '../../includes/topbar.php';

// Pastikan ID ada
if (!isset($_GET['id'])) {
  header("Location: index.php");
  exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM invoice WHERE id='$id'");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Invoice | E-Archive PT Asrindo</title>
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
          <h1 class="text-2xl font-bold text-gray-800">Edit Invoice</h1>
          <p class="text-gray-500 text-sm">Ubah data invoice yang sudah ada</p>
        </div>
        <a href="index.php"
          class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition flex items-center gap-2">
          <i data-lucide="arrow-left"></i> Kembali
        </a>
      </div>

      <!-- FORM -->
      <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-8 max-w-5xl mx-auto transition-all duration-300 hover:shadow-xl">
        <form action="proses_edit.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <input type="hidden" name="id" value="<?= $data['id']; ?>">

          <!-- Nomor Invoice -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Nomor Invoice</label>
            <input type="text" name="nomor_invoice" value="<?= htmlspecialchars($data['nomor_invoice']); ?>" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- Tanggal -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Tanggal Invoice</label>
            <input type="date" name="tanggal" value="<?= $data['tanggal']; ?>" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- Klien -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Nama Klien</label>
            <input type="text" name="klien" value="<?= htmlspecialchars($data['klien']); ?>" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- ===============================
               FIELD BARU DARI DATABASE
          ================================ -->
          <div class="col-span-2 border-t pt-4">
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Detail Transaksi</h2>
          </div>

          <!-- Nama Transaksi -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Nama Transaksi</label>
            <input type="text" name="nama_transaksi" value="<?= htmlspecialchars($data['nama_transaksi']); ?>" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- Harga Satuan -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Harga Satuan (Rp)</label>
            <input type="number" step="0.01" name="harga_satuan" id="harga_satuan" value="<?= $data['harga_satuan']; ?>" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- Kuantitas -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Kuantitas</label>
            <input type="number" step="0.01" name="kuantitas" id="kuantitas" value="<?= $data['kuantitas']; ?>" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- Total Harga -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Total Harga</label>
            <input type="text" name="total_harga" id="total_harga"
              value="Rp <?= number_format($data['total_harga'], 2, ',', '.'); ?>" readonly
              class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5">
          </div>

          <!-- Potongan Harga -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Potongan Harga (Rp)</label>
            <input type="number" step="0.01" name="potongan_harga" id="potongan_harga" value="<?= $data['potongan_harga']; ?>"
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- DPP -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">DPP</label>
            <input type="text" name="dpp" id="dpp"
              value="Rp <?= number_format($data['dpp'], 2, ',', '.'); ?>" readonly
              class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5">
          </div>

          <!-- PPN -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">PPN</label>
            <div class="flex gap-2">
              <select name="ppn_persen" id="ppn_persen"
                class="border border-gray-300 rounded-lg px-3 py-2.5 bg-gray-50 focus:ring-2 focus:ring-blue-400 focus:border-blue-500">
                <option value="0.11" <?= ($data['dpp']>0 && round($data['ppn']/$data['dpp'],2)==0.11)?'selected':''; ?>>11%</option>
                <option value="0.12" <?= ($data['dpp']>0 && round($data['ppn']/$data['dpp'],2)==0.12)?'selected':''; ?>>12%</option>
              </select>
              <input type="text" name="ppn" id="ppn"
                value="Rp <?= number_format($data['ppn'], 2, ',', '.'); ?>" readonly
                class="flex-1 rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5">
            </div>
          </div>

          <!-- Jumlah -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Jumlah (Rp)</label>
            <input type="number" step="0.01" name="jumlah" value="<?= htmlspecialchars($data['jumlah']); ?>" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- Status -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Status Pembayaran</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
              <option value="Belum Dibayar" <?= $data['status'] == 'Belum Dibayar' ? 'selected' : '' ?>>Belum Dibayar</option>
              <option value="Lunas" <?= $data['status'] == 'Lunas' ? 'selected' : '' ?>>Lunas</option>
              <option value="Overdue" <?= $data['status'] == 'Overdue' ? 'selected' : '' ?>>Overdue</option>
            </select>
          </div>

          <!-- LAMPIRAN -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-2">Lampiran Saat Ini</label>
            <?php
            $filePath = "../../uploads/invoice/" . $data['lampiran'];
            if (!empty($data['lampiran']) && file_exists($filePath)) {
              $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
              if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                echo "<img src='$filePath' alt='Lampiran' class='rounded-lg border mb-3 w-64'>";
              } elseif ($ext === 'pdf') {
                echo "<iframe src='$filePath' class='w-full h-64 border rounded-lg mb-3'></iframe>";
              } else {
                echo "<p class='text-sm text-gray-600 mb-3'>Lampiran: {$data['lampiran']}</p>";
              }
            } else {
              echo "<p class='text-red-500 text-sm mb-3'>Tidak ada lampiran.</p>";
            }
            ?>

            <label class="block text-gray-700 font-medium mb-1">Ganti Lampiran (Opsional)</label>
            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-600 mb-1">📁 Upload File Baru</label>
                <input type="file" name="lampiran" id="lampiran"
                  accept=".pdf,.jpg,.jpeg,.png"
                  class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white 
                  focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
              </div>
            </div>

            <p class="text-xs text-gray-500 mt-2">Atau ambil gambar langsung dari kamera di bawah.</p>

            <div class="mt-4 bg-gray-50 border rounded-lg p-4">
              <video id="video" class="w-full max-h-96 rounded-lg border border-gray-300" autoplay></video>
              <canvas id="canvas" class="hidden"></canvas>
              <div class="flex flex-wrap gap-3 mt-3">
                <button type="button" id="startCamera"
                  class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-4 py-2 rounded-lg">
                  Aktifkan Kamera
                </button>
                <button type="button" id="capturePhoto"
                  class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg hidden">
                  Ambil Foto
                </button>
              </div>
              <img id="capturedImage" class="hidden mt-4 rounded-lg shadow-md border border-gray-300 w-full sm:w-64">
              <input type="hidden" name="lampiran_camera" id="lampiran_camera">
            </div>
          </div>

          <!-- Tombol -->
          <div class="col-span-2 flex justify-end gap-3 pt-6">
            <a href="index.php"
              class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-5 py-2.5 rounded-lg transition-all">Batal</a>
            <button type="submit"
              class="bg-gradient-to-r from-[#00AEEF] to-[#0072FF] hover:from-[#0094E0] hover:to-[#005FCC]
                     text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all flex items-center gap-2">
              <i data-lucide="save" class="w-5 h-5"></i> Update
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <?php include '../../includes/footer.php'; ?>
  <script>lucide.createIcons();</script>

  <!-- HITUNG OTOMATIS -->
  <script>
    const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka);
    const harga = document.getElementById('harga_satuan');
    const qty = document.getElementById('kuantitas');
    const potongan = document.getElementById('potongan_harga');
    const total = document.getElementById('total_harga');
    const dpp = document.getElementById('dpp');
    const ppn = document.getElementById('ppn');
    const ppnPersen = document.getElementById('ppn_persen');

    function hitung() {
      const h = parseFloat(harga.value) || 0;
      const q = parseFloat(qty.value) || 0;
      const p = parseFloat(potongan.value) || 0;
      const totalHarga = h * q;
      const dppVal = totalHarga - p;
      const ppnVal = dppVal * parseFloat(ppnPersen.value);

      total.value = formatRupiah(totalHarga);
      dpp.value = formatRupiah(dppVal);
      ppn.value = formatRupiah(ppnVal);
    }

    [harga, qty, potongan, ppnPersen].forEach(e => e.addEventListener('input', hitung));
  </script>

  <!-- RESPONSIVE FIX -->
  <style>
  @media (max-width: 768px) {
    .ml-64 { margin-left: 0 !important; }
    body { overflow-x: hidden; }
    main { padding: 1rem !important; }
    .grid { grid-template-columns: 1fr !important; }
    .flex.justify-between.items-center { flex-direction: column; align-items: flex-start; gap: 1rem; }
    video { max-height: 280px !important; }
    #closeSidebar { position: absolute; top: 50%; right: 20px; transform: translateY(-50%); z-index: 70; }
    #sidebar { width: 75% !important; max-width: 280px !important; z-index: 90; }
    #openSidebar { z-index: 80; }
    #overlay { backdrop-filter: blur(3px); }
  }
  </style>
</body>
</html>
