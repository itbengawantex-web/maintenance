<?php
include 'config/dbcon.php';

// Ambil parameter dari AJAX
$start = $_GET['start'] ?? '';
$end   = $_GET['end'] ?? '';

// Cari tanggal terakhir di DB (pastikan tidak lebih dari hari ini)
$sql_latest = "SELECT MAX(tanggal) as latest_date 
               FROM log_downtime 
               WHERE tanggal <= CURDATE()";
$res_latest = mysqli_query($con, $sql_latest);
$row_latest = mysqli_fetch_assoc($res_latest);
$latest_date = $row_latest['latest_date'] ?? date('Y-m-d');

// Jika tidak ada filter, pakai default 7 hari terakhir
if (empty($start) || empty($end)) {
    $end = $latest_date;
    $start = date('Y-m-d', strtotime("$end -6 days"));
}

$where = "WHERE tanggal BETWEEN '$start' AND '$end'";

// Query sesuai kebutuhan chart teknisi
$query = mysqli_query($con, "
    SELECT nik_mekanik, COUNT(*) as total
    FROM log_downtime
    $where
    GROUP BY nik_mekanik
    ORDER BY total DESC
");

$labels = [];
$data   = [];

while ($row = mysqli_fetch_assoc($query)) {
    $labels[] = $row['nik_mekanik'];
    $data[]   = $row['total'];
}

echo json_encode([
    'labels' => $labels,
    'data'   => $data,
    'start'  => $start,
    'end'    => $end,
    'latest_date' => $latest_date
]);
