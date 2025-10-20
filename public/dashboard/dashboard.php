<?php
ob_start();
session_start();

// proteksi login
if (!isset($_SESSION['admin_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

// koneksi dan layout
require_once '../../config/koneksi.php';
require_once '../../includes/sidebar.php';
require_once '../../includes/topbar.php';
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | E-Archive PT Asrindo</title>
  <link rel="icon" href="../assets/img/logo.png">
  <link rel="stylesheet" href="../../src/output.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-[#f4f7fe] text-gray-800">
<?php include '../../includes/loader.php'; ?>

<main class="md:ml-64 p-8 space-y-10">

<!-- ===================== HERO SECTION ===================== -->
<section class="relative bg-gradient-to-r from-[#0A3A75] via-[#007BBD] to-[#10B981] text-white rounded-2xl shadow-xl p-8 flex flex-col lg:flex-row justify-between items-start lg:items-center overflow-hidden">
  <div class="space-y-5 max-w-2xl">
<h1 class="text-3xl font-bold mb-1">
  Selamat Datang, 
  <span class="text-yellow-300">
    <?= isset($_SESSION['nama_admin']) ? htmlspecialchars($_SESSION['nama_admin']) : 'Admin'; ?> 👋
  </span>
</h1>

    <p class="text-blue-100">Semoga harimu produktif dan penuh semangat!</p>
    <div class="h-[1px] w-20 bg-white/40 my-3"></div>
    <p class="text-blue-100 mb-4 leading-relaxed">
      Sistem <b>E-Archive PT Asrindo</b> memudahkan manajemen surat, invoice, dan dokumen internal secara digital.  
      Semua data tersimpan aman, terstruktur, dan mudah diakses kapan pun dibutuhkan.
    </p>
    <a href="../surat_masuk/index.php" class="inline-block bg-white text-[#0A3A75] font-semibold px-5 py-2 rounded-lg shadow hover:bg-blue-50 transition">
      Kelola Arsip Sekarang →
    </a>
  </div>

  <div class="text-right mt-8 lg:mt-0 lg:ml-8 flex-shrink-0">
    <div class="bg-white rounded-2xl p-5 shadow-lg text-gray-800 w-72">
      <div class="mb-5">
        <div class="flex items-center gap-2 mb-2">
          <i class="fa-solid fa-calendar-days text-[#007BBD] text-xl"></i>
          <div>
            <p class="text-xs text-gray-500 font-medium">Hari ini</p>
            <h2 id="current-date" class="text-base font-semibold text-gray-800"></h2>
          </div>
        </div>
        <div id="calendar-grid" class="mt-2 grid grid-cols-7 text-center text-gray-700 text-xs"></div>
      </div>
      <div class="border-t border-gray-200 pt-3">
        <div class="flex items-center gap-2 mb-1">
          <i class="fa-solid fa-clock text-[#007BBD] text-xl"></i>
          <p class="text-xs text-gray-500 font-medium">Jam Sekarang</p>
        </div>
        <h2 id="clock" class="text-2xl font-bold text-[#0A3A75] tracking-wider"></h2>
      </div>
    </div>
  </div>

  <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom_right,rgba(255,255,255,0.2),transparent_60%)] pointer-events-none"></div>
</section>

<script>
function renderCalendar() {
  const today = new Date();
  const currentMonth = today.getMonth();
  const currentYear = today.getFullYear();
  const monthNames = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
  const weekdays = ["Min","Sen","Sel","Rab","Kam","Jum","Sab"];
  const calendar = document.getElementById("calendar-grid");
  calendar.innerHTML = "";
  document.getElementById("current-date").innerText = `${weekdays[today.getDay()]}, ${today.getDate()} ${monthNames[currentMonth]} ${currentYear}`;
  weekdays.forEach(day => { let el=document.createElement("div"); el.classList="text-[11px] font-semibold text-[#007BBD] pb-1"; el.innerText=day; calendar.appendChild(el); });
  const firstDay = new Date(currentYear, currentMonth, 1).getDay();
  const lastDate = new Date(currentYear, currentMonth + 1, 0).getDate();
  for (let i = 0; i < firstDay; i++) calendar.appendChild(document.createElement("div"));
  for (let day = 1; day <= lastDate; day++) {
    let el=document.createElement("div"); el.innerText=day;
    el.classList="py-1.5 text-xs rounded-full hover:bg-[#007BBD]/20 cursor-pointer";
    if (day===today.getDate()) el.classList+=" bg-[#007BBD] text-white font-semibold";
    calendar.appendChild(el);
  }
}
renderCalendar();
function updateClock(){document.getElementById("clock").textContent=new Date().toLocaleTimeString("id-ID",{hour12:false});}
setInterval(updateClock,1000); updateClock();
</script>

<!-- ===================== STATISTIK DAN DEPARTEMEN BERSEBELAHAN ===================== -->
<?php
function getMonthlyCount($conn, $table, $dateField){
  $data=array_fill(1,12,0);
  $q=mysqli_query($conn,"SELECT MONTH($dateField) bulan, COUNT(*) total FROM $table WHERE deleted_at IS NULL GROUP BY bulan");
  while($r=mysqli_fetch_assoc($q)){$data[(int)$r['bulan']]=$r['total'];}
  return $data;
}
$dataMasuk=getMonthlyCount($conn,'surat_masuk','tanggal_surat');
$dataKeluar=getMonthlyCount($conn,'surat_keluar','tanggal_surat');
$dataInvoice=getMonthlyCount($conn,'invoice','tanggal');
$dataDraft=getMonthlyCount($conn,'draft','created_at');

$dept=mysqli_query($conn,"SELECT d.nama_departemen, 
(SELECT COUNT(*) FROM surat_masuk s WHERE s.departemen_id=d.id)+
(SELECT COUNT(*) FROM surat_keluar k WHERE k.departemen_id=d.id) AS total 
FROM departemen d ORDER BY total DESC LIMIT 5");
$deptLabels=[];$deptData=[];
while($r=mysqli_fetch_assoc($dept)){ $deptLabels[]=$r['nama_departemen']; $deptData[]=$r['total'];}
?>

<!-- ===================== STATISTIK & DEPARTEMEN SIDE BY SIDE ===================== -->
<section class="flex flex-col lg:flex-row gap-6">
  <!-- Statistik Arsip -->
  <div class="flex-1 bg-white rounded-2xl shadow-md hover:shadow-lg transition-all px-5 py-4">
    <div class="flex items-center gap-2 mb-3">
      <i class="fa-solid fa-chart-column text-blue-600 text-lg"></i>
      <h2 class="text-lg font-semibold text-[#0A3A75]">Statistik Arsip Per Bulan</h2>
    </div>
    <div class="h-[320px]">
      <canvas id="archiveChart"></canvas>
    </div>
  </div>

  <!-- Departemen Aktif -->
  <div class="flex-1 bg-white rounded-2xl shadow-md hover:shadow-lg transition-all px-5 py-4">
    <div class="flex items-center gap-2 mb-3">
      <i class="fa-solid fa-building text-emerald-600 text-lg"></i>
      <h2 class="text-lg font-semibold text-[#0A3A75]">Departemen Paling Aktif</h2>
    </div>
    <div class="h-[320px] flex items-center justify-center">
      <canvas id="deptChart"></canvas>
    </div>
  </div>
</section>



<!-- ===================== LOG AKTIVITAS REDESIGN ===================== -->
<section class="bg-white rounded-2xl shadow p-6">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-xl font-semibold text-primary">🧾 Aktivitas Terbaru</h2>
    <a href="../log_aktivitas/index.php" class="text-sm text-blue-600 hover:underline">Lihat Semua</a>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm border-collapse">
      <thead>
        <tr class="bg-gradient-to-r from-[#007BBD] to-[#0A3A75] text-white">
          <th class="py-3 px-4 text-left font-semibold">Aksi</th>
          <th class="py-3 px-4 text-left font-semibold">Keterangan</th>
          <th class="py-3 px-4 text-left font-semibold">Waktu</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php
        $logs = mysqli_query($conn, "SELECT * FROM log_aktivitas ORDER BY waktu DESC LIMIT 6");
        if (mysqli_num_rows($logs) > 0):
          while ($row = mysqli_fetch_assoc($logs)):
        ?>
          <tr class="hover:bg-gray-50 transition">
            <td class="py-3 px-4 text-[#007BBD] font-medium flex items-center gap-2">
              <i data-lucide="activity" class="w-4 h-4"></i> <?= htmlspecialchars($row['aksi']) ?>
            </td>
            <td class="py-3 px-4 text-gray-700"><?= htmlspecialchars($row['keterangan']) ?></td>
            <td class="py-3 px-4 text-gray-500"><?= htmlspecialchars($row['waktu']) ?></td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="3" class="py-4 text-center text-gray-500">Belum ada aktivitas</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<!-- ===================== CHARTS SCRIPT ===================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('archiveChart'), {
  type: 'bar',
  data: {
    labels:['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
    datasets:[
      {label:'Surat Masuk',data:<?=json_encode(array_values($dataMasuk))?>,backgroundColor:'rgba(37,99,235,0.8)',borderRadius:6},
      {label:'Surat Keluar',data:<?=json_encode(array_values($dataKeluar))?>,backgroundColor:'rgba(14,165,233,0.8)',borderRadius:6},
      {label:'Draft',data:<?=json_encode(array_values($dataDraft))?>,backgroundColor:'rgba(245,158,11,0.8)',borderRadius:6},
      {label:'Invoice',data:<?=json_encode(array_values($dataInvoice))?>,backgroundColor:'rgba(16,185,129,0.8)',borderRadius:6}
    ]
  },
  options:{plugins:{legend:{position:'top'}},scales:{y:{beginAtZero:true}}}
});

new Chart(document.getElementById('deptChart'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($deptLabels) ?>,
    datasets: [{
      data: <?= json_encode($deptData) ?>,
      backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
      hoverOffset: 6,
      cutout: '65%' // 🔹 perkecil radius bagian dalam (default 50%)
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false, // biar proporsional
    aspectRatio: 1.3, // 🔹 sedikit lebih kecil dari default
    plugins: {
      legend: {
        position: 'right',
        labels: {
          usePointStyle: true,
          padding: 20,
          color: '#0A3A75',
          font: { size: 13 }
        }
      }
    },
    layout: {
      padding: { top: 10, bottom: 10, left: 10, right: 10 }
    }
  }
});

</script>

<script src="https://unpkg.com/lucide@latest"></script>
<script>lucide.createIcons();</script>

</main>
<?php include '../../includes/footer.php'; ?>
</body>
</html>
