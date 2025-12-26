<?php
include 'config/dbcon.php';

$start = isset($_GET['start']) ? $_GET['start'] : '';
$end = isset($_GET['end']) ? $_GET['end'] : '';
$jenis_mesin = isset($_GET['jenis_mesin']) ? $_GET['jenis_mesin'] : '';

// Cari tanggal terakhir dari log_downtime
$sql_latest = "SELECT MAX(tanggal) as latest_date FROM log_downtime";
$res_latest = mysqli_query($con, $sql_latest);
$row_latest = mysqli_fetch_assoc($res_latest);
$latest_date = $row_latest['latest_date'] ?? date('Y-m-d');

// Kalau start & end kosong → otomatis ambil 7 hari terakhir dari latest_date
if (empty($start) || empty($end)) {
    $end = $latest_date;
    $start = date('Y-m-d', strtotime("$end -6 days"));
}

$where = "WHERE ld.tanggal BETWEEN '$start' AND '$end'";
if (!empty($jenis_mesin)) {
    $jenis_mesin_safe = mysqli_real_escape_string($con, $jenis_mesin);
    $where .= " AND dm.jenis_mesin = '$jenis_mesin_safe'";
}

$query = "
  SELECT ld.kode_mesin, COUNT(*) as total 
  FROM log_downtime ld
  JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
  $where
  GROUP BY ld.kode_mesin
  ORDER BY total DESC
  LIMIT 30
";

$result = mysqli_query($con, $query);

$data = [
  "labels" => [],
  "data" => [],
  "start_date" => $start,
  "end_date" => $end,
  "latest_date" => $latest_date
];

while ($row = mysqli_fetch_assoc($result)) {
  $data['labels'][] = $row['kode_mesin'];
  $data['data'][] = (int)$row['total'];
}

echo json_encode($data);
