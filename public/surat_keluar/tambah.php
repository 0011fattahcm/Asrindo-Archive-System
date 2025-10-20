<?php
ob_start();
session_start();
include '../../includes/auth_check.php';
include '../../config/koneksi.php';
include '../../includes/sidebar.php';
include '../../includes/topbar.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Surat Keluar | E-Archive PT Asrindo</title>
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
          <h1 class="text-2xl font-bold text-gray-800">Tambah Surat Keluar</h1>
          <p class="text-gray-500 text-sm">Isi formulir di bawah untuk menambahkan surat keluar baru</p>
        </div>
        <a href="index.php"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow transition flex items-center gap-2">
          <i data-lucide="arrow-left"></i> Kembali
        </a>
      </div>

      <!-- FORM -->
      <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-8 max-w-5xl mx-auto transition-all duration-300 hover:shadow-xl">
        <form action="proses_tambah.php" method="POST" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-2 gap-6">

          <!-- NOMOR SURAT -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Nomor Surat</label>
            <input type="text" name="nomor_surat" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- TANGGAL -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Tanggal Surat</label>
            <input type="date" name="tanggal_surat" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-1">Tanggal Keluar</label>
            <input type="date" name="tanggal_keluar" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- PENERIMA -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Penerima</label>
            <input type="text" name="penerima" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- PERIHAL -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Perihal</label>
            <input type="text" name="perihal" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
          </div>

          <!-- RINGKASAN -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Ringkasan Surat</label>
            <textarea name="ringkasan" rows="3"
              class="w-full rounded-lg border border-gray-300 bg-gray-50 focus:bg-white
              focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all"
              placeholder="Tuliskan ringkasan isi surat secara singkat..."></textarea>
          </div>

          <!-- DEPARTEMEN -->
          <div>
            <label for="departemen_id" class="block text-gray-700 font-medium mb-1">Departemen</label>
            <select id="departemen_id" name="departemen_id"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
              <option value="">-- Pilih Departemen --</option>
              <?php
              $departemen = mysqli_query($conn, "SELECT * FROM departemen");
              while ($row = mysqli_fetch_assoc($departemen)) {
                echo "<option value='{$row['id']}'>{$row['nama_departemen']}</option>";
              }
              ?>
            </select>
          </div>

          <!-- LAMPIRAN -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Lampiran Surat</label>

            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-600 mb-1">📁 Upload File</label>
                <input type="file" name="lampiran" id="lampiran"
                  accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png"
                  class="w-full rounded-lg border border-gray-300 bg-gray-50
                  focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all">
              </div>
            </div>

            <p class="text-xs text-gray-500 mt-2">Atau ambil gambar langsung dari kamera di bawah.</p>

            <!-- KAMERA -->
            <div class="mt-4 bg-gray-50 border rounded-lg p-4">
              <video id="video" class="w-full max-h-96 rounded-lg border border-gray-300" autoplay></video>
              <canvas id="canvas" class="hidden"></canvas>
              <div class="flex gap-3 mt-3">
                <button type="button" id="startCamera"
                  class="bg-emerald-500 hover:bg-emerald-600 text-white font-semibold px-4 py-2 rounded-lg">
                  Aktifkan Kamera
                </button>
                <button type="button" id="capturePhoto"
                  class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg hidden">
                  Ambil Foto
                </button>
              </div>
              <img id="capturedImage" class="hidden mt-4 rounded-lg shadow-md border border-gray-300" width="220">
              <input type="hidden" name="lampiran_camera" id="lampiran_camera">
            </div>
          </div>

          <!-- BUTTON -->
          <div class="col-span-2 flex justify-end gap-3 pt-6">
            <a href="index.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-5 py-2.5 rounded-lg transition-all">Batal</a>
            <button type="submit"
              class="bg-gradient-to-r from-[#00AEEF] to-[#0072FF] hover:from-[#0094E0] hover:to-[#005FCC]
              text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all flex items-center gap-2">
              <i data-lucide="save" class="w-5 h-5"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <?php include '../../includes/footer.php'; ?>

  <script>lucide.createIcons();</script>

  <script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const capturedImage = document.getElementById('capturedImage');
    const startCamera = document.getElementById('startCamera');
    const capturePhoto = document.getElementById('capturePhoto');
    const inputLampiranCamera = document.getElementById('lampiran_camera');
    let stream = null;

    startCamera.addEventListener('click', async () => {
      try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
        video.srcObject = stream;
        startCamera.classList.add('hidden');
        capturePhoto.classList.remove('hidden');
      } catch (err) {
        alert("Kamera tidak dapat diakses: " + err.message);
      }
    });

    capturePhoto.addEventListener('click', () => {
      const ctx = canvas.getContext('2d');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      const dataUrl = canvas.toDataURL('image/jpeg');
      capturedImage.src = dataUrl;
      capturedImage.classList.remove('hidden');
      inputLampiranCamera.value = dataUrl;
      stream.getTracks().forEach(track => track.stop());
      video.classList.add('hidden');
      capturePhoto.classList.add('hidden');
    });
  </script>

  <!-- RESPONSIVE -->
  <style>
  @media (max-width:768px){
    .ml-64{margin-left:0!important;}
    body{overflow-x:hidden;}
    main{padding:1rem!important;}
    .grid{grid-template-columns:1fr!important;}
    .flex.justify-between.items-center{flex-direction:column;align-items:flex-start;gap:1rem;}
    video{max-height:300px!important;}
    #closeSidebar{position:absolute;top:50%;right:20px;transform:translateY(-50%);z-index:70;}
    #sidebar{width:75%!important;max-width:280px!important;z-index:90;}
    #openSidebar{z-index:80;}
    #overlay{backdrop-filter:blur(3px);}
  }
  </style>
</body>
</html>
