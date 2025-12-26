<?php
require 'vendor/autoload.php';
include('config/dbcon.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Header untuk file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=report_downtime_" . date('Ymd_His') . ".xls");

// Ambil data filter dari URL
// Konfigurasi pagination


$where = [];

if (!empty($_GET['kode_mesin'])) {
    $kode_mesin = mysqli_real_escape_string($con, $_GET['kode_mesin']);
    $where[] = "ld.kode_mesin LIKE '%$kode_mesin%'";
}

if (!empty($_GET['tanggal_mulai']) && !empty($_GET['tanggal_akhir'])) {
    $tanggal_mulai = mysqli_real_escape_string($con, $_GET['tanggal_mulai']);
    $tanggal_akhir = mysqli_real_escape_string($con, $_GET['tanggal_akhir']);
    $where[] = "ld.tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
}

if (!empty($_GET['blok'])) {
    $blok = mysqli_real_escape_string($con, $_GET['blok']);
    $where[] = "dm.lokasi = '$blok'";
}

// ✅ Tambahkan pencarian berdasarkan kriteria
if (!empty($_GET['kriteria'])) {
    $kriteria = mysqli_real_escape_string($con, $_GET['kriteria']);
    $where[] = "ld.kriteria LIKE '%$kriteria%'";
}

// Bangun string WHERE
$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// Hitung total data untuk pagination
$total_query = mysqli_query($con, "
    SELECT COUNT(*) as total 
    FROM log_downtime ld
    JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    $where_sql
");


// Ambil data untuk halaman saat ini
$query = "
    SELECT ld.*, dm.lokasi 
    FROM log_downtime ld
    LEFT JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    $where_sql
    ORDER BY ld.tanggal DESC

";
$result = mysqli_query($con, $query);

?>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Mesin</th>
            <th>Blok</th>
            <th>Tanggal</th>
            <th>Nomor WO</th>
            <th>NIK Mekanik</th>
            <th>Kriteria Kerusakan</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Total Menit</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
$no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $jam_mulai = new DateTime($row['jam_mulai']);
            $jam_selesai = new DateTime($row['jam_selesai']);

            if ($jam_selesai < $jam_mulai) {
                $jam_selesai->modify('+1 day');
            }

            $durasi = $jam_mulai->diff($jam_selesai);
            $durasi_menit = ($durasi->h * 60) + $durasi->i;

            echo "<tr>
                <td>$no</td>
                <td>{$row['kode_mesin']}</td>
                <td>{$row['lokasi']}</td>
                <td>{$row['tanggal']}</td>
                <td>{$row['nomor_wo']}</td>
                <td>{$row['nik_mekanik']}</td>
                <td>{$row['kriteria']}</td>
                <td>{$row['jam_mulai']}</td>
                <td>{$row['jam_selesai']}</td>
                <td>{$durasi_menit}</td>
                <td>{$row['status']}</td>
            </tr>";
 $no++; 
        }
        ?>
    </tbody>
</table>
