<?php
include 'config/dbcon.php'; // koneksi database

// Ambil parameter filter
$tanggal_mulai = isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '';
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'harian';

// Default: ambil 7 hari terakhir kalau kosong
if (empty($tanggal_mulai) || empty($tanggal_akhir)) {
    $tanggal_akhir = date('Y-m-d');
    $tanggal_mulai = date('Y-m-d', strtotime($tanggal_akhir . ' -6 days'));
}

// Sesuaikan grouping berdasarkan kategori
switch ($kategori) {
    case 'mingguan':
        $select_periode = "YEARWEEK(tanggal, 1)";
        $label_periode = "CONCAT(YEAR(tanggal), '-W', WEEK(tanggal, 1))";
        break;
    case 'bulanan':
        $select_periode = "DATE_FORMAT(tanggal, '%Y-%m')";
        $label_periode = "DATE_FORMAT(tanggal, '%M %Y')";
        break;
    default: // harian
        $select_periode = "DATE(tanggal)";
        $label_periode = "DATE(tanggal)";
        break;
}

// Query data dari pemakaian_part
$query = "
    SELECT 
        $label_periode AS periode,
        SUM(jumlah) AS total
    FROM pemakaian_part
    WHERE tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'
    GROUP BY $select_periode
    ORDER BY MIN(tanggal) ASC
";

$result = mysqli_query($con, $query);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "periode" => $row['periode'],
        "total" => $row['total']
    ];
}

// Output JSON
header('Content-Type: application/json');
echo json_encode($data);
