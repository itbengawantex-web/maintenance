<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/header.php');
include('config/dbcon.php'); // atau sesuaikan dengan file koneksimu

$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
$end_date   = $_GET['end_date'] ?? date('Y-m-d');
if ($start_date > $end_date) {
    $temp = $start_date;
    $start_date = $end_date;
    $end_date = $temp;
}
// 1. Total Downtime 7 Hari Terakhir
$q1 = mysqli_query($con, "
    SELECT COUNT(*) as total 
    FROM log_downtime 
    WHERE tanggal BETWEEN '$start_date' AND '$end_date'
");
$total_downtime = mysqli_fetch_assoc($q1)['total'];


// Hitung rata-rata durasi downtime (dengan koreksi tengah malam)
$q2 = mysqli_query($con, "
    SELECT jam_mulai, jam_selesai 
    FROM log_downtime 
    WHERE tanggal BETWEEN '$start_date' AND '$end_date'
");

$total_menit = 0;
$total_data = 0;

while ($row = mysqli_fetch_assoc($q2)) {
    if (!empty($row['jam_mulai']) && !empty($row['jam_selesai'])) {
        $jam_mulai = new DateTime($row['jam_mulai']);
        $jam_selesai = new DateTime($row['jam_selesai']);

        // Koreksi jika jam selesai lebih kecil dari jam mulai (lewat tengah malam)
        if ($jam_selesai < $jam_mulai) {
            $jam_selesai->modify('+1 day');
        }

        $durasi = $jam_mulai->diff($jam_selesai);
        $durasi_menit = ($durasi->h * 60) + $durasi->i;

        $total_menit += $durasi_menit;
        $total_data++;
    }
}

$avg_duration = $total_data > 0 ? round($total_menit / $total_data, 1) : 0;




// 3. Jumlah Teknisi Aktif 7 Hari Terakhir
$q3 = mysqli_query($con, "
    SELECT COUNT(DISTINCT nik_mekanik) AS teknisi
    FROM log_downtime
    WHERE tanggal BETWEEN '$start_date' AND '$end_date'
");
$teknisi = 0;
if ($q3) {
    $row = mysqli_fetch_assoc($q3);
    $teknisi = $row['teknisi'] ?? 0;
}

// Cari NIK mekanik yang paling banyak muncul, kecuali NIK '0' atau kosong
$mekanik_query = mysqli_query($con, "
    SELECT ld.nik_mekanik, COUNT(*) AS total_kerusakan
    FROM log_downtime ld
    WHERE ld.tanggal BETWEEN '$start_date' AND '$end_date'
    AND ld.nik_mekanik <> '0'
    AND ld.nik_mekanik <> ''
    GROUP BY ld.nik_mekanik
    ORDER BY total_kerusakan DESC
    LIMIT 1
");

$mekanik_top = mysqli_fetch_assoc($mekanik_query);

$nik_terbanyak = $mekanik_top['nik_mekanik'] ?? '-';
$total_kerusakan_mekanik = $mekanik_top['total_kerusakan'] ?? 0;
$total_menit_mekanik = 0;

if ($nik_terbanyak && $nik_terbanyak !== '-') {
    // Hitung total menit kerja mekanik itu (dengan koreksi jam selesai lewat tengah malam)
    $menit_q = mysqli_query($con, "
        SELECT SUM(
            CASE
                WHEN STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s') <
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s')
                THEN TIMESTAMPDIFF(MINUTE, 
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'),
                     DATE_ADD(STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 DAY)
                )
                ELSE TIMESTAMPDIFF(MINUTE, 
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'),
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s')
                )
            END
        ) AS total_menit
        FROM log_downtime
        WHERE nik_mekanik = '$nik_terbanyak'
        AND tanggal BETWEEN '$start_date' AND '$end_date'
    ");

    if ($menit_row = mysqli_fetch_assoc($menit_q)) {
        $total_menit_mekanik = $menit_row['total_menit'] ?? 0;
    }
}
// 5. Mesin extruder dengan Downtime Terbanyak
$extruder = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT ld.kode_mesin, COUNT(*) AS total_kerusakan
    FROM log_downtime ld
    JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    WHERE dm.lokasi = 'extruder'
    AND ld.tanggal BETWEEN '$start_date' AND '$end_date'
    GROUP BY ld.kode_mesin
    ORDER BY total_kerusakan DESC
    LIMIT 1
"));

$mesin_extruder = $extruder['kode_mesin'] ?? '-';
$total_extruder = $extruder['total_kerusakan'] ?? 0;
$total_menit5 = 0;

if ($mesin_extruder && $mesin_extruder !== '-') {
    $kode_mesin = mysqli_real_escape_string($con, $mesin_extruder);

    $query = "
        SELECT SUM(
            CASE
                WHEN STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s') <
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s')
                THEN TIMESTAMPDIFF(MINUTE, 
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'),
                     DATE_ADD(STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 DAY)
                )
                ELSE TIMESTAMPDIFF(MINUTE, 
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'),
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s')
                )
            END
        ) AS total_menit5
        FROM log_downtime
        WHERE kode_mesin = '$kode_mesin'
        AND tanggal BETWEEN '$start_date' AND '$end_date'
    ";

    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_menit5 = $row['total_menit5'] ?? 0;
    }
}


// 5. Mesin finishing dengan Downtime Terbanyak
$finishing = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT ld.kode_mesin, COUNT(*) AS total_kerusakan
    FROM log_downtime ld
    JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    WHERE dm.lokasi = 'finishing'
    AND ld.tanggal BETWEEN '$start_date' AND '$end_date'
    GROUP BY ld.kode_mesin
    ORDER BY total_kerusakan DESC
    LIMIT 1
"));

$mesin_finishing = $finishing['kode_mesin'] ?? '-';
$total_finishing = $finishing['total_kerusakan'] ?? 0;
$total_menit0 = 0;

if ($mesin_finishing && $mesin_finishing !== '-') {
    $kode_mesin = mysqli_real_escape_string($con, $mesin_finishing);

    $query = "
        SELECT SUM(
            CASE
                WHEN STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s') <
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s')
                THEN TIMESTAMPDIFF(MINUTE, 
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'),
                     DATE_ADD(STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 DAY)
                )
                ELSE TIMESTAMPDIFF(MINUTE, 
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'),
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s')
                )
            END
        ) AS total_menit0
        FROM log_downtime
        WHERE kode_mesin = '$kode_mesin'
        AND tanggal BETWEEN '$start_date' AND '$end_date'
    ";

    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_menit0 = $row['total_menit0'] ?? 0;
    }
}


// Total kerusakan CL Blok 1
$blok1 = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT ld.kode_mesin, COUNT(*) AS total_kerusakan
    FROM log_downtime ld
    JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    WHERE dm.lokasi = 'CL Blok 1'
    AND ld.tanggal BETWEEN '$start_date' AND '$end_date'
    GROUP BY ld.kode_mesin
    ORDER BY total_kerusakan DESC
    LIMIT 1
"));

$mesin_blok1 = $blok1['kode_mesin'] ?? '-';
$total_blok1 = $blok1['total_kerusakan'] ?? 0;
$total_menit1 = 0;

if ($mesin_blok1) {
    $kode_mesin = mysqli_real_escape_string($con, $mesin_blok1);

    $query = "
        SELECT SUM(
            CASE
                WHEN STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s') <
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s')
                THEN TIMESTAMPDIFF(MINUTE, 
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'),
                     DATE_ADD(STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 DAY)
                )
                ELSE TIMESTAMPDIFF(MINUTE, 
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'),
                     STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s')
                )
            END
        ) AS total_menit1
        FROM log_downtime
        WHERE kode_mesin = '$kode_mesin'
        AND tanggal BETWEEN '$start_date' AND '$end_date'
    ";

    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_menit1 = $row['total_menit1'] ?? 0;
    }
}

// Total kerusakan CL Blok 2
$blok2 = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT ld.kode_mesin, COUNT(*) AS total_kerusakan
    FROM log_downtime ld
    JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    WHERE dm.lokasi = 'CL Blok 2'
    AND ld.tanggal BETWEEN '$start_date' AND '$end_date'
    GROUP BY ld.kode_mesin
    ORDER BY total_kerusakan DESC
    LIMIT 1
"));

$mesin_blok2 = $blok2['kode_mesin'] ?? '-';
$total_blok2 = $blok2['total_kerusakan'] ?? 0;
$total_menit2 = 0;

if ($mesin_blok2 && $mesin_blok2 != '-') {
    $kode_mesin = mysqli_real_escape_string($con, $mesin_blok2);

    $query = "
        SELECT SUM(
            CASE
                WHEN STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s') 
                     < STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s')
                THEN TIMESTAMPDIFF(
                        MINUTE, 
                        STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'), 
                        DATE_ADD(STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 DAY)
                    )
                ELSE TIMESTAMPDIFF(
                        MINUTE, 
                        STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'), 
                        STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s')
                    )
            END
        ) AS total_menit2
        FROM log_downtime 
        WHERE kode_mesin = '$kode_mesin' 
        AND tanggal BETWEEN '$start_date' AND '$end_date'
        AND jam_mulai IS NOT NULL AND jam_selesai IS NOT NULL
        AND jam_mulai <> '' AND jam_selesai <> ''
    ";

    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_menit2 = $row['total_menit2'] ?? 0;
    }
}

// Total kerusakan CL Blok 3
$blok3 = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT ld.kode_mesin, COUNT(*) AS total_kerusakan
    FROM log_downtime ld
    JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    WHERE dm.lokasi = 'CL Blok 3'
    AND ld.tanggal BETWEEN '$start_date' AND '$end_date'
    GROUP BY ld.kode_mesin
    ORDER BY total_kerusakan DESC
    LIMIT 1
"));

$mesin_blok3 = $blok3['kode_mesin'] ?? '-';
$total_blok3 = $blok3['total_kerusakan'] ?? 0;
$total_menit3 = 0;

if ($mesin_blok3 && $mesin_blok3 != '-') {
    $kode_mesin = mysqli_real_escape_string($con, $mesin_blok3);

    $query = "
        SELECT SUM(
            CASE
                WHEN STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s') 
                     < STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s')
                THEN TIMESTAMPDIFF(
                        MINUTE, 
                        STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'), 
                        DATE_ADD(STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 DAY)
                    )
                ELSE TIMESTAMPDIFF(
                        MINUTE, 
                        STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'), 
                        STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s')
                    )
            END
        ) AS total_menit3
        FROM log_downtime 
        WHERE kode_mesin = '$kode_mesin' 
        AND tanggal BETWEEN '$start_date' AND '$end_date'
        AND jam_mulai IS NOT NULL AND jam_selesai IS NOT NULL
        AND jam_mulai <> '' AND jam_selesai <> ''
    ";

    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_menit3 = $row['total_menit3'] ?? 0;
    }
}

// Total kerusakan CL Blok 4
$blok4 = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT ld.kode_mesin, COUNT(*) AS total_kerusakan
    FROM log_downtime ld
    JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    WHERE dm.lokasi = 'CL Blok 4'
    AND ld.tanggal BETWEEN '$start_date' AND '$end_date'
    GROUP BY ld.kode_mesin
    ORDER BY total_kerusakan DESC
    LIMIT 1
"));

$mesin_blok4 = $blok4['kode_mesin'] ?? '-';
$total_blok4 = $blok4['total_kerusakan'] ?? 0;
$total_menit = 0;

if ($mesin_blok4 && $mesin_blok4 != '-') {
    $kode_mesin = mysqli_real_escape_string($con, $mesin_blok4);

    $query = "
        SELECT SUM(
            CASE
                WHEN STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s') 
                     < STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s')
                THEN TIMESTAMPDIFF(
                        MINUTE, 
                        STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'), 
                        DATE_ADD(STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s'), INTERVAL 1 DAY)
                    )
                ELSE TIMESTAMPDIFF(
                        MINUTE, 
                        STR_TO_DATE(CONCAT(tanggal, ' ', jam_mulai), '%Y-%m-%d %H:%i:%s'), 
                        STR_TO_DATE(CONCAT(tanggal, ' ', jam_selesai), '%Y-%m-%d %H:%i:%s')
                    )
            END
        ) AS total_menit
        FROM log_downtime 
        WHERE kode_mesin = '$kode_mesin' 
        AND tanggal BETWEEN '$start_date' AND '$end_date'
        AND jam_mulai IS NOT NULL AND jam_selesai IS NOT NULL
        AND jam_mulai <> '' AND jam_selesai <> ''
    ";

    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total_menit = $row['total_menit'] ?? 0;
    }
}


if (isset($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['start_date']) && !empty($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
} else {
    // Ambil tanggal terakhir dari database
    $result = mysqli_query($con, "SELECT MAX(tanggal) AS max_tanggal FROM log_downtime");
    $data = mysqli_fetch_assoc($result);
    $tanggal_terakhir = $data['max_tanggal'] ?? date('Y-m-d');

    // Hitung tanggal 7 hari sebelumnya
    $start_date = date('Y-m-d', strtotime($tanggal_terakhir . ' -6 days')); // 6 hari sebelumnya + hari ini = 7 hari total
    $end_date = $tanggal_terakhir;
}
?>

<div class="container-fluid">
  <!-- Filter Tanggal Global + Export -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" class="form-inline m-0">
  <input type="date" name="start_date" id="filter-tanggal-mulai1" class="form-control form-control-sm mr-2"
    value="<?= htmlspecialchars($start_date) ?>">

  <input type="date" name="end_date" id="filter-tanggal-akhir1" class="form-control form-control-sm mr-2"
    value="<?= htmlspecialchars($end_date) ?>">

  <button type="submit" id="filter-global-btn" class="btn btn-sm btn-primary">Filter</button>
</form>
    <!-- <button onclick="exportToPDF()" class="btn btn-danger">Export Dashboard ke PDF</button> -->
  </div>


        <!-- Small boxes (Stat box) -->
        <div class="row d-flex flex-wrap justify-content-start">
          <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
            <!-- small box -->
            <div class="small-box bg-gradient-warning">
              <div class="inner">
                <h3><?= $total_downtime ?></h3>
                <p>Total Downtime (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)</p>

              </div>
              <div class="icon">
                <i class="fas fa-tools"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
           <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
            <div class="small-box bg-gradient-success">
              <div class="inner">
                <h3><?= $avg_duration ?><sup style="font-size: 14px"> Menit</sup></h3>
                <p>Rata - rata Durasi (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
              <!-- ./col -->
           <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
            <!-- small box -->
            <div class="small-box bg-gradient-info">
              <div class="inner">
                <h3><?= $teknisi ?></h3>
                <p>Teknisi Aktif (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
           <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
            <!-- small box -->
            <div class="small-box bg-gradient-dark">
              <div class="inner">
                <h3><?= $nik_terbanyak ?> <small>(<?= $total_kerusakan_mekanik ?>x) <?= $total_menit_mekanik ?> Min</small></h3>
                <p>Mekanik Terbanyak (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)<br>
                  
              </div>
              <div class="icon">
                <i class="fas fa-user-cog"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
           <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
            <!-- small box -->
            <div class="small-box bg-gradient-danger">
              <div class="inner">
                <h3><?= $mesin_finishing ?> <small>(<?= $total_finishing ?>x) <?= $total_menit0 ?> Min</small></h3>
                <p>Terbanyak Finishing (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)</p>
              </div>
              <div class="icon">
                <i class="fas fa-cogs"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        
        <!-- /.row -->
      </div><!-- /.container-fluid -->

      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class= "row d-flex flex-wrap justify-content-start">
          <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
          <div class="small-box bg-gradient-primary">
            <div class="inner">
              <h3><?= $mesin_blok1 ?> <small>(<?= $total_blok1 ?>x) <?= $total_menit1 ?> Min</small></h3>
              <p>Terbanyak CL Blok 1 (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)</p>
            </div>
            <div class="icon">
              <i class="fas fa-cogs"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
          <!-- ./col -->
          <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
            <div class="small-box bg-gradient-secondary">
              <div class="inner">
                <h3><?= $mesin_blok2 ?> <small>(<?= $total_blok2 ?>x) <?= $total_menit2 ?> Min</small></h3>
                <p>Terbanyak CL Blok 2 (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)</p>
              </div>
              <div class="icon">
                <i class="fas fa-cogs"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
              <!-- ./col -->
          <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
            <!-- small box -->
            <div class="small-box bg-gradient-purple">
              <div class="inner">
                <h3><?= $mesin_blok3 ?> <small> (<?= $total_blok3 ?>x) <?= $total_menit3 ?> Min</small></h3>
                <p>Terbanyak CL Blok 3 (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)</p>
              </div>
              <div class="icon">
                <i class="fas fa-cogs"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
            <!-- small box -->
            <div class="small-box bg-gradient-indigo">
              <div class="inner">
                <h3><?= $mesin_blok4 ?><small>(<?= $total_blok4 ?>x) <?= $total_menit ?> Min</small></h3>
                <p>Terbanyak CL Blok 4 (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)</p>
              </div>
              <div class="icon">
                <i class="fas fa-cogs"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
           <div class="p-1" style="flex: 0 0 20%; max-width: 20%;">
            <!-- small box -->
            <div class="small-box bg-gradient-maroon">
              <div class="inner">
                <h3><?= $mesin_extruder ?> <small>(<?= $total_extruder ?>x) <?= $total_menit5 ?> Min</small></h3>
                <p>Terbanyak Extruder (<?= date('d M', strtotime($start_date)) ?> - <?= date('d M', strtotime($end_date)) ?>)</p>
              </div>
              <div class="icon">
                <i class="fas fa-cogs"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        
        </div>
        
        
        <!-- /.row -->
      </div><!-- /.container-fluid -->
      <div class="row">
          <!-- Kartu pertama -->
          <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h3 class="card-title m-0">
                    <i class="far fa-chart-bar"></i> Kerusakan per Hari
                    </h3>
                        <div class="d-flex gap-2 align-items-center">
                        <input type="date" id="tanggalMulai" class="form-control form-control-sm mr-2"  />
                        <input type="date" id="tanggalAkhir" class="form-control form-control-sm mr-2" />
                        <select id="chartFilter" class="form-control form-control-sm" style="width: 150px;">
                            <option value="harian" selected>Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan">Bulanan</option>
                        </select>
                        </div>
                </div>
                </div>
                <div class="chart-container"  style="overflow-x: auto;">
                <canvas id="downtimeChart" style="height: 550px;"></canvas>
                </div>

            </div>
         </div>

            <!-- Kartu kedua -->
           <div class="col-md-6">
    <div class="card card-success card-outline">
      <div class="card-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
          <h3 class="card-title m-0">
            <i class="fas fa-cogs"></i> Pemakaian Part
          </h3>
          <div class="d-flex align-items-center">
            <input type="date" id="tanggalMulaiPart" class="form-control form-control-sm mr-2" />
            <input type="date" id="tanggalAkhirPart" class="form-control form-control-sm mr-2" />
            <select id="chartFilterPart" class="form-control form-control-sm" style="width: 150px;">
              <option value="harian" selected>Harian</option>
              <option value="mingguan">Mingguan</option>
              <option value="bulanan">Bulanan</option>
            </select>
          </div>
        </div>
      </div>
      <div class="chart-container" style="overflow-x: auto;">
        <canvas id="partChart" style="height: 550px;"></canvas>

            </div>
        </div>
    </div>
</div>
</div>
<script>



let chart, chart2, chart3;

const periodePlugin = {
  id: 'periodeLabel',
  afterDraw(chart) {
    const { ctx, chartArea } = chart;
    ctx.save();
    ctx.font = 'bold 14px sans-serif';
    ctx.fillStyle = '#000';
    ctx.textAlign = 'center';
    const centerX = chartArea.left + chartArea.width / 2;
    const bottomY = chart.height - 10; // posisi di bawah chart
    ctx.fillText('PERIODE', centerX, bottomY);
    ctx.restore();
  }
};

function loadChartData(jenis, start = '', end = '') {
  $.ajax({
    url: 'chart_downtime.php',
    type: 'GET',
    data: { jenis, start, end },
    dataType: 'json',
    success: function (res) {
      const ctx = document.getElementById('downtimeChart').getContext('2d');

      const labels = [...res.labels, ''];
      const data = [...res.data.map(x => Number(x)), 0];

      if (window.chart) window.chart.destroy();
      window.chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Jumlah Downtime',
            data: data,
            backgroundColor: labels.map(label => label === '' ? 'rgba(0,0,0,0)' : 'rgba(54, 162, 235, 0.7)'),
            borderColor: labels.map(label => label === '' ? 'rgba(0,0,0,0)' : 'rgba(54, 162, 235, 1)'),
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: {
            padding: {
              bottom: 30 // ruang untuk tulisan "Periode"
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0,
                stepSize: 1
              }
            }
          },
          plugins: {
            title: {
              display: false,
              text: ''
            }
          }
        },
        plugins: [periodePlugin] // ⬅️ aktifkan plugin untuk label "Periode"
      });
    }
  });
}

$(document).ready(function () {
  let ctx = document.getElementById('partChart').getContext('2d');
  let partChart;

  function loadPartChart() {
    let tanggalMulai = $('#tanggalMulaiPart').val();
    let tanggalAkhir = $('#tanggalAkhirPart').val();
    let kategori = $('#chartFilterPart').val();

    $.ajax({
      url: 'chart_part.php',
      method: 'GET',
      data: {
        tanggal_mulai: tanggalMulai,
        tanggal_akhir: tanggalAkhir,
        kategori: kategori
      },
      dataType: 'json',
      success: function (data) {
  let labels = data.map(item => item.periode);
  let values = data.map(item => parseInt(item.total));

  // Tambahkan dummy agar mirip chart downtime
  labels.push('');
  values.push(0);

  if (partChart) {
    partChart.destroy();
  }

  partChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
  label: 'Pemakaian Part',
  data: values,
  backgroundColor: labels.map(l => l === '' ? 'rgba(0,0,0,0)' : 'rgba(153, 102, 255, 0.6)'), // ungu transparan
  borderColor: labels.map(l => l === '' ? 'rgba(0,0,0,0)' : 'rgba(153, 102, 255, 1)'),   // ungu solid
  borderWidth: 1
}]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      layout: {
        padding: { bottom: 30 } // ruang buat tulisan PERIODE
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            precision: 0,
            stepSize: 1
          }
        }
      },
      plugins: {
        title: { display: false }
      }
    },
    plugins: [periodePlugin] // ⬅️ sama kayak chart downtime
  });
}
    });
  }

  // Load awal
  loadPartChart();

  // Reload kalau filter berubah
  $('#tanggalMulaiPart, #tanggalAkhirPart, #chartFilterPart').on('change', function () {
    loadPartChart();
  });
});


function updateChartData() {
  const jenis = $('#chartFilter').val();
  const start = $('#tanggalMulai').val();
  const end = $('#tanggalAkhir').val();
  loadChartData(jenis, start, end);
}

function loadChartTeknisi(start = '', end = '') {
  $.ajax({
    url: 'chart_teknisi.php',
    type: 'GET',
    data: { start, end },
    dataType: 'json',
    success: function (res) {
      const ctx2 = document.getElementById('bar-chart-mini2').getContext('2d');

      const labels = [...res.labels, ''];
      const data = [...res.data.map(x => Number(x)), 0];
      const colors = labels.map(label => label === '' ? 'rgba(0,0,0,0)' : 'rgba(255, 99, 132, 0.7)');
      const borders = labels.map(label => label === '' ? 'rgba(0,0,0,0)' : 'rgba(255, 99, 132, 1)');

      // Plugin hanya untuk tulisan "amano"
      const amanoPlugin = {
        id: 'amanoLabel',
        afterDraw(chart) {
          const { ctx, chartArea } = chart;
          ctx.save();
          ctx.font = 'bold 14px sans-serif';
          ctx.fillStyle = '#000';
          ctx.textAlign = 'center';
          const centerX = chartArea.left + chartArea.width / 2;
          const bottomY = chart.height - 10;
          ctx.fillText('AMANO', centerX, bottomY);
          ctx.restore();
        }
      };

      if (window.chart2) window.chart2.destroy();
      window.chart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Jumlah Pekerjaan',
            data: data,
            backgroundColor: colors,
            borderColor: borders,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: {
            padding: {
              bottom: 30
            }
          },
          scales: {
            x: {
              ticks: {
                autoSkip: false,
                minRotation: 45
              }
            },
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function (context) {
                  return context.label === '' ? '' : `${context.dataset.label}: ${context.raw}`;
                }
              }
            },
            title: {
              display: false,
              text: ''
            }
          }
        },
        plugins: [amanoPlugin] // ⬅️ Gunakan hanya plugin "amano" di sini
      });
    }
  });
}

const kerusakanPlugin = {
  id: 'kerusakanLabel',
  afterDraw(chart) {
    const { ctx, chartArea } = chart;
    ctx.save();
    ctx.font = 'bold 14px sans-serif';
    ctx.fillStyle = '#000';
    ctx.textAlign = 'center';
    const centerX = chartArea.left + chartArea.width / 2;
    const bottomY = chart.height - 10; // posisi di bawah chart
    ctx.fillText('KERUSAKAN MESIN', centerX, bottomY);
    ctx.restore();
  }
};

function loadChartKerusakan(jenis_mesin = '', start = '', end = '') {
  $.ajax({
    url: 'get_data_kerusakan.php',
    type: 'GET',
    data: { jenis_mesin, start, end },
    dataType: 'json',
    success: function (res) {
      if (!start && !end) {
        $('#filter-tanggal-mulai').val(res.start_date);
        $('#filter-tanggal-akhir').val(res.end_date);
      }

      const ctx3 = document.getElementById('chart-kerusakan-mesin').getContext('2d');
      if (chart3) chart3.destroy();

      const dummyLabel = '';
      const dummyValue = 0;
      const labels = [...res.labels, dummyLabel];
      const data = [...res.data.map(x => Number(x)), dummyValue];

      const backgroundColors = [...res.data.map(_ => 'rgba(255, 206, 86, 0.7)'), 'rgba(0, 0, 0, 0)'];
      const borderColors = [...res.data.map(_ => 'rgba(255, 206, 86, 1)'), 'rgba(0, 0, 0, 0)'];

      chart3 = new Chart(ctx3, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Frekuensi Kerusakan',
            data: data,
            backgroundColor: backgroundColors,
            borderColor: borderColors,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          layout: {
            padding: {
              bottom: 30 // ⬅️ lebih besar biar teks muat
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0 }
            },
            x: {
              ticks: { autoSkip: false, minRotation: 45 }
            }
          }
        },
        plugins: [kerusakanPlugin] // ⬅️ aktifkan plugin
      });
    }
  });
}

$(document).ready(function () {
  updateChartData();
  loadChartTeknisi();
  loadChartKerusakan();

  $('#chartFilter, #tanggalMulai, #tanggalAkhir').on('change', updateChartData);
  $('#filterBtn').on('click', () => loadChartTeknisi($('#start_date').val(), $('#end_date').val()));
  $('#filter-btn').on('click', () => loadChartKerusakan($('#filter-jenis-mesin').val(), $('#filter-tanggal-mulai').val(), $('#filter-tanggal-akhir').val()));
});




</script>

