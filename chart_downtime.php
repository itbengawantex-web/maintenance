<?php
include('config/dbcon.php'); // Sesuaikan path ke file koneksi

// Fungsi bantu untuk konversi WEEK ke rentang tanggal
function weekToDateRange($yearweek) {
    $year = substr($yearweek, 0, 4);
    $week = substr($yearweek, 4, 2);

    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $bulan = [
        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei',
        'Jun' => 'Juni', 'Jul' => 'Juli', 'Aug' => 'Agustus', 'Sep' => 'September',
        'Oct' => 'Oktober', 'Nov' => 'November', 'Dec' => 'Desember'
    ];

    $start_day = $dto->format('j');
    $start_month = $bulan[$dto->format('M')];

    $dto->modify('+6 days');
    $end_day = $dto->format('j');
    $end_month = $bulan[$dto->format('M')];
    $year = $dto->format('Y');

    if ($start_month == $end_month) {
        return "$start_day - $end_day $end_month $year";
    } else {
        return "$start_day $start_month - $end_day $end_month $year";
    }
}

// Ambil parameter dari URL
$jenis = $_GET['jenis'] ?? 'harian';
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

$tanggal = [];
$jumlah = [];

// Filter WHERE jika ada tanggal
$where = '';
if (!empty($start) && !empty($end)) {
    $start_escaped = mysqli_real_escape_string($con, $start);
    $end_escaped = mysqli_real_escape_string($con, $end);
    $where = "WHERE tanggal BETWEEN '$start_escaped' AND '$end_escaped'";
}

// Query sesuai jenis
if ($jenis == 'harian') {
    $query = mysqli_query($con, "
        SELECT DATE(tanggal) AS tgl, COUNT(*) AS total 
        FROM log_downtime 
        $where
        GROUP BY DATE(tanggal) 
        ORDER BY tgl DESC 
        LIMIT 7
    ");
    while ($row = mysqli_fetch_assoc($query)) {
        $tanggal[] = date('d M Y', strtotime($row['tgl']));
        $jumlah[] = (int)$row['total']; // konversi ke integer
    }

} elseif ($jenis == 'mingguan') {
    $query = mysqli_query($con, "
        SELECT YEARWEEK(tanggal, 1) AS minggu, COUNT(*) AS total
        FROM log_downtime
        $where
        GROUP BY minggu
        ORDER BY minggu DESC
        LIMIT 7
    ");
    while ($row = mysqli_fetch_assoc($query)) {
        $tanggal[] = weekToDateRange($row['minggu']);
        $jumlah[] = (int)$row['total']; // konversi ke integer
    }

} elseif ($jenis == 'bulanan') {
    $query = mysqli_query($con, "
        SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, COUNT(*) AS total
        FROM log_downtime
        $where
        GROUP BY bulan
        ORDER BY bulan DESC
        LIMIT 7
    ");
    while ($row = mysqli_fetch_assoc($query)) {
        $bulanNama = DateTime::createFromFormat('Y-m', $row['bulan'])->format('F Y');
        $map = [
            'January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei',
            'June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober',
            'November'=>'November','December'=>'Desember'
        ];
        $tanggal[] = strtr($bulanNama, $map);
        $jumlah[] = (int)$row['total']; // konversi ke integer
    }
}

// Kembalikan data ke frontend
echo json_encode([
    'labels' => array_reverse($tanggal),
    'data' => array_reverse($jumlah)
]);
?>
