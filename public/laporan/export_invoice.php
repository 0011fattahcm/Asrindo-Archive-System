<?php
require '../../vendor/autoload.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

$start = $_GET['start_date'] ?? '';
$end   = $_GET['end_date'] ?? '';

if (!$start || !$end) {
    die("❌ Harap pilih rentang tanggal terlebih dahulu.");
}

// ===== Ambil data lengkap dari tabel invoice =====
$query = "
    SELECT nomor_invoice, tanggal, klien, nama_transaksi, harga_satuan, kuantitas, total_harga, 
           potongan_harga, dpp, ppn, jumlah, status
    FROM invoice
    WHERE deleted_at IS NULL AND (tanggal BETWEEN '$start' AND '$end')
    ORDER BY tanggal ASC
";
$result = mysqli_query($conn, $query);

// ===== Inisialisasi Spreadsheet =====
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Invoice');

// ===== Judul =====
$sheet->mergeCells('A1:L1');
$sheet->setCellValue('A1', 'LAPORAN INVOICE');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// ===== Header Kolom =====
$headers = [
    'No', 'Nomor Invoice', 'Tanggal', 'Klien', 'Nama Transaksi', 'Harga Satuan (Rp)',
    'Kuantitas', 'Total Harga (Rp)', 'Potongan (Rp)', 'DPP (Rp)', 'PPN', 'Status'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '3', $header);
    $sheet->getStyle($col . '3')->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0072FF']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);
    $col++;
}

// ===== Isi Data =====
$row = 4;
$no = 1;
while ($data = mysqli_fetch_assoc($result)) {
    // Hitung PPN Persentase
    $ppnPersen = '';
    if ($data['dpp'] > 0) {
        $ratio = round($data['ppn'] / $data['dpp'], 2);
        if ($ratio == 0.11) $ppnPersen = '11%';
        elseif ($ratio == 0.12) $ppnPersen = '12%';
    }

    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", $data['nomor_invoice']);
    $sheet->setCellValue("C$row", $data['tanggal']);
    $sheet->setCellValue("D$row", $data['klien']);
    $sheet->setCellValue("E$row", $data['nama_transaksi']);
    $sheet->setCellValue("F$row", number_format($data['harga_satuan'], 0, ',', '.'));
    $sheet->setCellValue("G$row", $data['kuantitas']);
    $sheet->setCellValue("H$row", number_format($data['total_harga'], 0, ',', '.'));
    $sheet->setCellValue("I$row", number_format($data['potongan_harga'], 0, ',', '.'));
    $sheet->setCellValue("J$row", number_format($data['dpp'], 0, ',', '.'));
    $sheet->setCellValue("K$row", "Rp " . number_format($data['ppn'], 0, ',', '.') . " ($ppnPersen)");
    $sheet->setCellValue("L$row", $data['status']);

    // Style border & alignment
    foreach (range('A', 'L') as $col) {
        $sheet->getStyle($col . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_LEFT]
        ]);
    }

    $row++;
}

// ===== Atur Lebar Kolom =====
foreach (range('A', 'L') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// ===== Nama File =====
$filename = "Laporan_Invoice_{$start}_sd_{$end}.xlsx";

// ===== Log Aktivitas =====
$aksi = 'Export Laporan Invoice';
$keterangan = "Admin ID $admin_id mengekspor laporan Invoice dari tanggal $start hingga $end.";
logAktivitas($conn, $aksi, $keterangan);

// ===== Output ke browser =====
if (ob_get_length()) ob_end_clean();
header_remove("Set-Cookie");
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
