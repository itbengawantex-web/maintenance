<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Koneksi database (ganti sesuai kebutuhan)
$koneksi = new mysqli("localhost", "root", "", "btxprddb"); // Ganti "nama_database"

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'Kode Mesin');
$sheet->setCellValue('B1', 'Tanggal');
$sheet->setCellValue('C1', 'NIK Mekanik');
$sheet->setCellValue('D1', 'Kriteria');
$sheet->setCellValue('E1', 'Jam Mulai');
$sheet->setCellValue('F1', 'Jam Selesai');
$sheet->setCellValue('G1', 'Menit');
$sheet->setCellValue('H1', 'Status');

// Query data
$sql = "SELECT kode_mesin, tanggal, nik_mekanik, kriteria, jam_mulai, jam_selesai, 
        TIMESTAMPDIFF(MINUTE, jam_mulai, jam_selesai) AS menit, status 
        FROM log_downtime";

$result = $koneksi->query($sql);
$row = 2;

if ($result->num_rows > 0) {
    while ($data = $result->fetch_assoc()) {
        $sheet->setCellValue('A' . $row, $data['kode_mesin']);
        $sheet->setCellValue('B' . $row, $data['tanggal']);
        $sheet->setCellValue('C' . $row, $data['nik_mekanik']);
        $sheet->setCellValue('D' . $row, $data['kriteria']);
        $sheet->setCellValue('E' . $row, $data['jam_mulai']);
        $sheet->setCellValue('F' . $row, $data['jam_selesai']);
        $sheet->setCellValue('G' . $row, $data['menit']);
        $sheet->setCellValue('H' . $row, $data['status']);
        $row++;
    }
}

$writer = new Xlsx($spreadsheet);

// Output ke browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="dashboard_downtime.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
