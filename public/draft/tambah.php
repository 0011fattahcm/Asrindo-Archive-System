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
  <title>Tambah Draft Dokumen | E-Archive PT Asrindo</title>
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
          <h1 class="text-2xl font-bold text-gray-800">Tambah Draft Dokumen</h1>
          <p class="text-gray-500 text-sm">Isi formulir di bawah untuk menambahkan draft dokumen baru</p>
        </div>
      </div>

      <!-- FORM CARD -->
      <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-8 max-w-4xl mx-auto transition-all duration-300 hover:shadow-xl">
        <form action="proses_tambah.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          <!-- JUDUL -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Judul Dokumen</label>
            <input type="text" name="judul" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 
              focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all duration-150">
          </div>

          <!-- DESKRIPSI -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3"
              class="w-full rounded-lg border border-gray-300 bg-gray-50 
              focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all duration-150"
              placeholder="Tuliskan deskripsi singkat mengenai dokumen ini..."></textarea>
          </div>

          <!-- STATUS -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Status Draft</label>
            <select name="status" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 
              focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all duration-150">
              <option value="draft">Draft</option>
              <option value="final">Final</option>
            </select>
          </div>

          <div></div>

          <!-- UPLOAD FILE -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Lampiran Dokumen (Opsional)</label>
            <input type="file" name="lampiran" id="lampiran"
              accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
              class="w-full rounded-lg border border-gray-300 bg-gray-50 
              focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all duration-150">
            <p class="text-xs text-gray-500 mt-1">Format yang didukung: PDF, Word, Excel, Gambar</p>
          </div>

          <!-- OPSI FOTO DARI KAMERA -->
          <div class="col-span-2">
            <p class="text-gray-700 font-medium mb-1">Atau ambil foto langsung dari kamera</p>
            <div class="bg-gray-50 border rounded-lg p-4">
              <video id="video" class="w-full max-h-80 rounded-lg border border-gray-300" autoplay></video>
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

          <!-- TOMBOL SIMPAN -->
          <div class="col-span-2 flex justify-end gap-3 pt-6">
            <a href="index.php"
              class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-5 py-2.5 rounded-lg transition-all duration-150">Batal</a>
            <button type="submit"
              class="bg-gradient-to-r from-[#00AEEF] to-[#0072FF] hover:from-[#0094E0] hover:to-[#005FCC]
                     text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all duration-150 flex items-center gap-2">
              <i data-lucide="save" class="w-5 h-5"></i> Simpan Draft
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <?php include '../../includes/footer.php'; ?>
  <script>lucide.createIcons();</script>

  <!-- WebRTC Kamera -->
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
      const context = canvas.getContext('2d');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      context.drawImage(video, 0, 0, canvas.width, canvas.height);
      const dataUrl = canvas.toDataURL('image/jpeg');
      capturedImage.src = dataUrl;
      capturedImage.classList.remove('hidden');
      inputLampiranCamera.value = dataUrl;
      stream.getTracks().forEach(track => track.stop());
      video.classList.add('hidden');
      capturePhoto.classList.add('hidden');
    });
  </script>

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
