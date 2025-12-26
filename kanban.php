<?php
session_start();
include('config/dbcon.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');



$limit   = 20;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset  = ($halaman - 1) * $limit;

$where = [];

$params = $_GET;
unset($params['halaman']); // hapus halaman 1 dulu

function buildUrl($hal) {
    global $params;
    $params['halaman'] = $hal;
    return '?' . http_build_query($params);
}


if (!empty($_GET['tanggal_mulai']) && !empty($_GET['tanggal_akhir'])) {
    $tm = mysqli_real_escape_string($con, $_GET['tanggal_mulai']);
    $ta = mysqli_real_escape_string($con, $_GET['tanggal_akhir']);
    $where[] = "k.tanggal BETWEEN '$tm' AND '$ta'";
}

if (!empty($_GET['nama_mesin'])) {
    $nm = mysqli_real_escape_string($con, $_GET['nama_mesin']);
    $where[] = "k.nama_mesin = '$nm'";
}



$where_clause = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";


$totalQ = "
    SELECT COUNT(*) AS total 
    FROM kanban_downtime k
    $where_clause
";

$total_query = mysqli_query($con, $totalQ);
if (!$total_query) die("Query Error total: " . mysqli_error($con));

$total_data = mysqli_fetch_assoc($total_query)['total'];
$pages = ceil($total_data / $limit);



$query = "
    SELECT k.*
FROM kanban_downtime k
$where_clause
ORDER BY k.tanggal DESC
LIMIT $offset, $limit
";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Query Error data: " . mysqli_error($con));
}

$totalQ2 = "
    SELECT COUNT(*) AS total 
    FROM kanban_downtime k
    $where_clause
";

$total_query2 = mysqli_query($con, $totalQ2);
if (!$total_query2) die("Query Error total2: " . mysqli_error($con));

$total_data2 = mysqli_fetch_assoc($total_query2)['total'];

/* Jumlah halaman khusus tabel 2 */
$limit2  = 20;  // bebas diganti
$halaman2 = isset($_GET['halaman2']) ? (int)$_GET['halaman2'] : 1;
$offset2  = ($halaman2 - 1) * $limit2;

$pages2 = ceil($total_data2 / $limit2);

$query2 = "
    SELECT 
        k.tanggal,
        k.nama_mesin,

        -- TECHNICAL TIME = Waktu Selesai PM - Waktu Mulai PM
        TIMESTAMPDIFF(
            MINUTE, 
            k.waktu_mulai_pm, 
            k.waktu_selesai_pm
        ) AS technical_time,

        -- Idle = Waktu Start Up - Waktu Selesai PM
        TIMESTAMPDIFF(
            MINUTE,
            k.waktu_selesai_pm,
            k.waktu_start_up
        ) AS idle_time,

        -- Ramp-up = Waktu Normal Run - Waktu Start Up
        TIMESTAMPDIFF(
            MINUTE,
            k.waktu_start_up,
            k.waktu_normal_run
        ) AS ramp_up_time,

        -- TOTAL
        (
            TIMESTAMPDIFF(MINUTE, k.waktu_mulai_pm, k.waktu_selesai_pm) +
            TIMESTAMPDIFF(MINUTE, k.waktu_selesai_pm, k.waktu_start_up) +
            TIMESTAMPDIFF(MINUTE, k.waktu_start_up, k.waktu_normal_run)
        ) AS total_downtime

    FROM kanban_downtime k
    $where_clause
    ORDER BY k.tanggal DESC
    LIMIT $offset2, $limit2
";
$result2 = mysqli_query($con, $query2);

?>

<style>
.kanban-table th,
.kanban-table td {
    border: 2px solid #958e8eff !important; /* garis hitam tebal */
}

.kanban-table {
    border-collapse: collapse !important;
}
</style>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">


    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Data Kanban Extruder</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Kanban Extruder</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <div class="container" style="max-width: 98%;">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                    
                    <form method="GET" class="d-flex gap-2 align-items-center mb-0">
                      <a href="tambahkanban.php" class="btn btn-primary mr-2">Tambah Log </a>
                      <select name="nama_mesin" class="form-control form-control-sm mr-2" style="width: 300px;" >
                          <option value="">-- Pilih Mesin --</option>
                          <option value="EXT YM-1600">EXT YM-1600</option>
                          <option value="EXT YM-2100">EXT YM-2100</option>
                          <option value="EXT LOHIA">EXT LOHIA</option>
                      </select>

                      <input type="date" name="tanggal_mulai" class="form-control form-control-sm mr-2" style="width: 140px;" value="<?= isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '' ?>" />

                      <input type="date" name="tanggal_akhir" class="form-control form-control-sm mr-2" style="width: 140px;" value="<?= isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '' ?>" />

                     <input type="hidden" name="halaman" value="1">
                      <input type="hidden" name="halaman2" value="1">

                      <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
                  </form>

                    </div>
                    
                    
                    <!-- /.card-header -->
                          <div class="card-body" style="overflow-x:auto; width:100%;">
                            <table class="table table-bordered table-striped kanban-table" style="width:100%; text-align:center;">
                              <thead>
                                  <tr>
                                      <th colspan="4" style="background:#00AEEF; color:white; font-size:18px;">To Do</th>
                                      <th colspan="3" style="background:#FFF200; font-size:18px;">In Progress</th>
                                      <th colspan="1" style="background:#F9A602; font-size:18px;">Waiting For Start Up</th>
                                      <th colspan="2" style="background:#009245; color:white; font-size:18px;">Normal Run</th>
                                  </tr>

                                  <!-- HEADER LEVEL 2 -->
                                  <tr>
                                      <th style="background:#00AEEF; color:white;">No</th>
                                      <th style="background:#00AEEF; color:white;">Tanggal</th>
                                      <th style="background:#00AEEF; color:white;">Departement</th>
                                      <th style="background:#00AEEF; color:white;">Nama Mesin</th>

                                      <th style="background:#FFF200;">Waktu Stop Mesin</th>
                                      <th style="background:#FFF200;">Waktu Mulai PM/Perbaikan</th>
                                      <th style="background:#FFF200;">Waktu Selesai PM/Perbaikan</th>

                                      <th style="background:#F9A602;">Waktu Start Awal Mesin</th>

                                      <th style="background:#009245; color:white;">Waktu Mesin Normal</th>
                                      <th style="background:#009245; color:white;">Keterangan</th>
                                  </tr>
                              </thead>
                              <tbody>
                            <?php
                                $no = $offset + 1;
                                while ($row = mysqli_fetch_assoc($result)) {

                                ?>
                                <tr>
                              <td><?= $no++; ?></td>
                              <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                              <td><?= $row['departement']; ?></td>
                              <td><?= $row['nama_mesin']; ?></td>

                              <td><?= $row['waktu_stop_mesin']; ?></td>
                              <td><?= $row['waktu_mulai_pm']; ?></td>
                              <td><?= $row['waktu_selesai_pm']; ?></td>

                              <td><?= $row['waktu_start_up']; ?></td>

                              <td><?= $row['waktu_normal_run']; ?></td>
                              <td><?= $row['keterangan']; ?></td>
                          </tr>

                                  <!-- Modal Edit -->
                                  <div class='modal fade' id='editModal<?= $row['no_evaluasi'] ?>' tabindex='-1' role='dialog' aria-labelledby='editModalLabel<?= $row['no_evaluasi'] ?>' aria-hidden='true'>
                                    <div class='modal-dialog modal-lg' role='document'>
                                      <div class='modal-content'>
                                        <form action='proses_edit1.php' method='post'>
                                          <div class='modal-header'>
                                            <h5 class='modal-title' id='editModalLabel<?= $row['no_evaluasi'] ?>'>Edit Data Evaluasi</h5>
                                            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                                              <span aria-hidden='true'>&times;</span>
                                            </button>
                                          </div>
                                          <div class='modal-body'>
                                            <input type='hidden' name='no_evaluasi' value='<?= $row['no_evaluasi'] ?>'>
                                            <input type='hidden' name='halaman' value='<?= $_GET['halaman'] ?? 1 ?>'>
                                            <div class='row'>
                                              <div class='col-md-6 mb-2'>
                                                <label>Tanggal</label>
                                                <input type='date' name='tanggal' class='form-control' value='<?= $row['tanggal'] ?>'>
                                              </div>
                                              <div class='col-md-6 mb-2'>
                                                <label>Kode Mesin</label>
                                                <input type='text' name='kode_mesin' class='form-control' value='<?= $row['kode_mesin'] ?>'>
                                              </div>
                                              <div class='col-md-6 mb-2'>
                                                <label>Kerusakan</label>
                                                <input type='text' name='kerusakan' class='form-control' value='<?= $row['kerusakan'] ?>'>
                                              </div>
                                              <div class='col-md-6 mb-2'>
                                                <label>Man</label>
                                                <input type='text' name='man' class='form-control' value='<?= $row['man'] ?>'>
                                              </div>
                                              <div class='col-md-6 mb-2'>
                                                <label>Methode</label>
                                                <input type='text' name='methode' class='form-control' value='<?= $row['methode'] ?>'>
                                              </div>
                                              <div class='col-md-6 mb-2'>
                                                <label>Material</label>
                                                <input type='text' name='material' class='form-control' value='<?= $row['material'] ?>'>
                                              </div>
                                              <div class='col-md-6 mb-2'>
                                                <label>Mesin</label>
                                                <input type='text' name='mesin' class='form-control' value='<?= $row['mesin'] ?>'>
                                              </div>
                                              <div class='col-md-6 mb-2'>
                                                <label>Environment</label>
                                                <input type='text' name='environment' class='form-control' value='<?= $row['environment'] ?>'>
                                              </div>
                                              <div class='col-md-12 mb-2'>
                                                <label>Countermeasure</label>
                                                <input type='text' name='countermeasure' class='form-control' value='<?= $row['countermeasure'] ?>'>
                                              </div>
                                            </div>
                                          </div>
                                          <div class='modal-footer'>
                                            <button type='button' class='btn btn-secondary' data-dismiss='modal'>Batal</button>
                                            <button type='submit' class='btn btn-primary'>Simpan Perubahan</button>
                                          </div>
                                        </form>
                                      </div>
                                    </div>
                                  </div>

                                <?php } ?>
                              </tbody>
                            </table>
                            
                            
                          </div>

                        <!-- Navigasi pagination -->
                            <div class="card-footer clearfix">
    <ul class="pagination pagination-sm m-0 float-right">

        <?php
        $limit = 5;
        $start = max(1, $halaman - floor($limit / 2));
        $end = min($pages, $start + $limit - 1);

        if ($end - $start + 1 < $limit) {
            $start = max(1, $end - $limit + 1);
        }

        // Prev
        if ($halaman > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . buildUrl($halaman - 1) . '">&laquo;</a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
        }

        // Halaman pertama
        if ($start > 1) {
            echo '<li class="page-item"><a class="page-link" href="' . buildUrl(1) . '">1</a></li>';
            if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        // Halaman tengah
        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $halaman) ? 'active' : '';
            echo '<li class="page-item '.$active.'"><a class="page-link" href="' . buildUrl($i) . '">' . $i . '</a></li>';
        }

        // Halaman terakhir
        if ($end < $pages) {
            if ($end < $pages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            echo '<li class="page-item"><a class="page-link" href="' . buildUrl($pages) . '">' . $pages . '</a></li>';
        }

        // Next
        if ($halaman < $pages) {
            echo '<li class="page-item"><a class="page-link" href="' . buildUrl($halaman + 1) . '">&raquo;</a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
        }
        ?>

    </ul>
</div>
                      </div>
                    </div>
                </div>
                
                <div class="container" style="max-width: 98%; margin-top: 20px;">
    <div class="row">
        <div class="col-md-12">
            <div class="card">

                <div class="card-header" style="background:#009245; color:white;">
                    <h4 class="mb-0">Rekap Perhitungan Downtime</h4>
                </div>

                <div class="card-body" style="overflow-x:auto;">
                    <table class="table table-bordered kanban-table" style="width:100%; text-align:center;">
                        <thead>
                            <tr>
                                <th style="background:#009245; color:white;">No</th>
                                <th style="background:#009245; color:white;">Tanggal</th>
                                <th style="background:#009245; color:white;">Nama Mesin</th>
                                <th style="background:#009245; color:white;">Technical Time (m)</th>
                                <th style="background:#009245; color:white;">Idle/Wait Time (m)</th>
                                <th style="background:#009245; color:white;">Ramp-up Time (m)</th>
                                <th style="background:#009245; color:white;">Total Downtime (m)</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php 
                            $no = $offset2 + 1;
                            while ($row2 = mysqli_fetch_assoc($result2)) { ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= date('d-m-Y', strtotime($row2['tanggal'])); ?></td>
                                <td><?= $row2['nama_mesin']; ?></td>
                                <td><?= $row2['technical_time']; ?> menit</td>
                                <td><?= $row2['idle_time']; ?> menit</td>
                                <td><?= $row2['ramp_up_time']; ?> menit</td>
                                <td><b><?= $row2['total_downtime']; ?> menit</b></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer clearfix">
    <ul class="pagination pagination-sm m-0 float-right">
        <?php
        $limit_show = 5;
        $start2 = max(1, $halaman2 - floor($limit_show / 2));
        $end2   = min($pages2, $start2 + $limit_show - 1);

        if ($end2 - $start2 + 1 < $limit_show) {
            $start2 = max(1, $end2 - $limit_show + 1);
        }

        // Function untuk mempertahankan filter GET
        function buildLink($page) {
            $params = $_GET;
            $params['halaman2'] = $page;
            return '?' . http_build_query($params);
        }

        // Prev
        if ($halaman2 > 1) {
            echo '<li class="page-item"><a class="page-link" href="'.buildLink($halaman2 - 1).'">&laquo;</a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
        }

        // First + ellipsis
        if ($start2 > 1) {
            echo '<li class="page-item"><a class="page-link" href="'.buildLink(1).'">1</a></li>';
            if ($start2 > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }

        // Middle pages
        for ($i = $start2; $i <= $end2; $i++) {
            $active = ($i == $halaman2) ? 'active' : '';
            echo '<li class="page-item '.$active.'"><a class="page-link" href="'.buildLink($i).'">'.$i.'</a></li>';
        }

        // Last + ellipsis
        if ($end2 < $pages2) {
            if ($end2 < $pages2 - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            echo '<li class="page-item"><a class="page-link" href="'.buildLink($pages2).'">'.$pages2.'</a></li>';
        }

        // Next
        if ($halaman2 < $pages2) {
            echo '<li class="page-item"><a class="page-link" href="'.buildLink($halaman2 + 1).'">&raquo;</a></li>';
        } else {
            echo '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
        }
        ?>
    </ul>
</div>

            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
    

</div>


<?php
include('includes/footer.php');
?>