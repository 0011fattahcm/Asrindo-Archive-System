<?php
require '../../vendor/autoload.php';
include '../../config/koneksi.php';
include '../../includes/log_helper.php'; // pastikan sudah ada fungsi logAktivitas()

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

// --- Query data ---
$query = "
    SELECT nomor_surat, tanggal_surat, tanggal_terima, pengirim, perihal, ringkasan, departemen_id
    FROM surat_masuk
    WHERE deleted_at IS NULL AND (tanggal_surat BETWEEN '$start' AND '$end')
    ORDER BY tanggal_surat ASC
";
$result = mysqli_query($conn, $query);

// --- Generate Excel ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Surat Masuk');

$sheet->mergeCells('A1:G1');
$sheet->setCellValue('A1', 'LAPORAN SURAT MASUK');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$headers = ['No', 'Nomor Surat', 'Tgl Surat', 'Tgl Terima', 'Pengirim', 'Perihal', 'Ringkasan'];
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

$row = 4;
$no = 1;
while ($data = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", $data['nomor_surat']);
    $sheet->setCellValue("C$row", $data['tanggal_surat']);
    $sheet->setCellValue("D$row", $data['tanggal_terima']);
    $sheet->setCellValue("E$row", $data['pengirim']);
    $sheet->setCellValue("F$row", $data['perihal']);
    $sheet->setCellValue("G$row", $data['ringkasan']);
    foreach (range('A', 'G') as $col) {
        $sheet->getStyle($col . $row)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
    }
    $row++;
}

foreach (range('A', 'G') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

$filename = "Laporan_Surat_Masuk_{$start}_sd_{$end}.xlsx";

// --- Log Aktivitas ---
$aksi = 'Export Laporan Surat Masuk';
$keterangan = "Admin ID $admin_id mengekspor laporan Surat Masuk dari tanggal $start hingga $end.";
logAktivitas($conn, $aksi, $keterangan);

// Bersihkan output buffer biar gak korup
if (ob_get_length()) ob_end_clean();
header_remove("Set-Cookie");

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

?>
