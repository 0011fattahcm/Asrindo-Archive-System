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
$query = mysqli_query($conn, "SELECT * FROM draft WHERE id='$id'");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Draft Dokumen | E-Archive PT Asrindo</title>
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
          <h1 class="text-2xl font-bold text-gray-800">Edit Draft Dokumen</h1>
          <p class="text-gray-500 text-sm">Ubah data draft dokumen yang sudah ada</p>
        </div>
      </div>

      <!-- FORM CARD -->
      <div class="bg-white shadow-lg rounded-2xl border border-gray-100 p-8 max-w-5xl mx-auto transition-all duration-300 hover:shadow-xl">
        <form action="proses_edit.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <input type="hidden" name="id" value="<?= $data['id']; ?>">

          <!-- Judul -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Judul Dokumen</label>
            <input type="text" name="judul" value="<?= htmlspecialchars($data['judul']); ?>" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 
              focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all duration-150">
          </div>

          <!-- Deskripsi -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="3" required
              class="w-full rounded-lg border border-gray-300 bg-gray-50 
              focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all duration-150"
              placeholder="Tuliskan deskripsi dokumen..."><?= htmlspecialchars($data['deskripsi']); ?></textarea>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-gray-700 font-medium mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
              <option value="draft" <?= $data['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
              <option value="final" <?= $data['status'] == 'final' ? 'selected' : '' ?>>Final</option>
            </select>
          </div>

          <div></div>

          <!-- ==================== LAMPIRAN ==================== -->
          <div class="col-span-2">
            <label class="block text-gray-700 font-medium mb-1">Lampiran Saat Ini</label>
            <?php
            $filePath = "../../uploads/draft/" . $data['lampiran'];
            if (!empty($data['lampiran']) && file_exists($filePath)) {
              $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
              if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                echo "<img src='$filePath' alt='Lampiran' class='rounded-lg border mb-3 w-64'>";
              } elseif ($ext == 'pdf') {
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
                  accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                  class="w-full rounded-lg border border-gray-300 bg-gray-50 
                  focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-400 px-4 py-2.5 transition-all duration-150">
              </div>
            </div>

            <p class="text-xs text-gray-500 mt-1">Atau ambil gambar langsung dari kamera di bawah.</p>

            <!-- Kamera -->
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

  <!-- Kamera -->
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
