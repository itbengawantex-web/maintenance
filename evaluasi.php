<?php
session_start();
include('config/dbcon.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');


// Konfigurasi pagination
$limit = 40; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman - 1) * $limit;

$where = [];

if (!empty($_GET['kode_mesin'])) {
    $kode_mesin = mysqli_real_escape_string($con, $_GET['kode_mesin']);
    $where[] = "kode_mesin LIKE '%$kode_mesin%'";
}

if (!empty($_GET['tanggal_mulai']) && !empty($_GET['tanggal_akhir'])) {
    $tanggal_mulai = mysqli_real_escape_string($con, $_GET['tanggal_mulai']);
    $tanggal_akhir = mysqli_real_escape_string($con, $_GET['tanggal_akhir']);
    $where[] = "tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
}

if (!empty($_GET['kerusakan'])) {
    $kerusakan = mysqli_real_escape_string($con, $_GET['kerusakan']);
    $where[] = "kerusakan LIKE '%$kerusakan%'";
}

if (!empty($_GET['blok'])) {
    $blok = mysqli_real_escape_string($con, $_GET['blok']);
    $where[] = "kode_mesin IN (SELECT kode_mesin FROM data_mesin WHERE lokasi = '$blok')";
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Hitung total data
$total_query = mysqli_query($con, "SELECT COUNT(*) as total FROM evaluasi $where_clause");
if (!$total_query) {
    die("Query Error (total): " . mysqli_error($con));
}
$total_data = mysqli_fetch_assoc($total_query)['total'];
$pages = ceil($total_data / $limit);

// Ambil data
$query = "SELECT * 
          FROM evaluasi
          $where_clause 
          ORDER BY tanggal DESC 
          LIMIT $offset, $limit";
$result = mysqli_query($con, $query);
if (!$result) {
    die("Query Error (data): " . mysqli_error($con));
}

?>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">


    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Data Fishbone</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Fishbone</li>
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
                      <a href="tambaheval.php" class="btn btn-primary mr-2">Tambah Log </a>

                      <input type="text" name="kode_mesin" class="form-control form-control-sm mr-2" placeholder="Kode Mesin" style="width: 150px;" value="<?= isset($_GET['kode_mesin']) ? $_GET['kode_mesin'] : '' ?>" />

                      <select name="blok" class="form-control form-control-sm mr-2" style="width: 150px;">
                          <option value="">-- Pilih Blok --</option>
                          <option value="CL BLOK 1" <?= isset($_GET['blok']) && $_GET['blok'] == 'CL BLOK 1' ? 'selected' : '' ?>>CL BLOK 1</option>
                          <option value="CL BLOK 2" <?= isset($_GET['blok']) && $_GET['blok'] == 'CL BLOK 2' ? 'selected' : '' ?>>CL BLOK 2</option>
                          <option value="CL BLOK 3" <?= isset($_GET['blok']) && $_GET['blok'] == 'CL BLOK 3' ? 'selected' : '' ?>>CL BLOK 3</option>
                          <option value="CL BLOK 4" <?= isset($_GET['blok']) && $_GET['blok'] == 'CL BLOK 4' ? 'selected' : '' ?>>CL BLOK 4</option>
                          <option value="FINISHING" <?= isset($_GET['blok']) && $_GET['blok'] == 'FINISHING' ? 'selected' : '' ?>>FINISHING</option>
                          <option value="EXTRUDER" <?= isset($_GET['blok']) && $_GET['blok'] == 'EXTRUDER' ? 'selected' : '' ?>>EXTRUDER</option>
                      </select>


                      <input type="text" name="kerusakan" class="form-control form-control-sm mr-2" placeholder="Kerusakan" style="width: 200px;" value="<?= isset($_GET['kerusakan']) ? $_GET['kerusakan'] : '' ?>" />

                      <input type="date" name="tanggal_mulai" class="form-control form-control-sm mr-2" style="width: 140px;" value="<?= isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '' ?>" />

                      <input type="date" name="tanggal_akhir" class="form-control form-control-sm mr-2" style="width: 140px;" value="<?= isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '' ?>" />

                      <input type="hidden" name="halaman" value="<?= $_GET['halaman'] ?? 1 ?>">

                      <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
                  </form>

                    </div>
                    
                    
                    <!-- /.card-header -->
<div class="card-body">
  <table class="table table-bordered table-striped" style="width:100%; text-align:center;">
    <thead>
      <tr>
        <th rowspan="2" style="vertical-align:middle;">No</th>
        <th rowspan="2" style="vertical-align:middle;">Tanggal</th>
        <th rowspan="2" style="vertical-align:middle;">No Mesin</th>
        <th rowspan="2" style="vertical-align:middle;">Kerusakan</th>
        <th colspan="5" style="vertical-align:middle; text-align:center;">4M + 1E Analisis Akar Masalah</th>
        <th rowspan="2" style="vertical-align:middle;">Counter Measure</th>
        <th rowspan="2" style="vertical-align:middle;">Status</th>
        <th rowspan="2" style="vertical-align:middle;">Foto</th>
        <th rowspan="2" style="vertical-align:middle;">Aksi</th>
      </tr>
      <tr>
        <th>Man</th>
        <th>Method</th>
        <th>Material</th>
        <th>Machine</th>
        <th>Environment</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $no = $offset + 1;
      while ($row = mysqli_fetch_assoc($result)) {
          // Buat badge status
          if (strtoupper($row['status']) == 'NOT OK') {
              $status_badge = "<span class='badge bg-danger px-2 py-1' style='font-size: 0.9rem; min-width:70px;'>NOT OK</span>";
          } elseif (strtoupper($row['status']) == 'OK') {
              $status_badge = "<span class='badge bg-success px-2 py-1' style='font-size: 0.9rem; min-width:70px;'>OK</span>";
          } else {
              $status_badge = "<span class='badge bg-primary px-2 py-1' style='font-size: 0.9rem; min-width:70px;'>{$row['status']}</span>";
          }
      ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= !empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-' ?></td>
          <td><?= $row['kode_mesin'] ?></td>
          <td><?= $row['kerusakan'] ?></td>
          <td><?= $row['man'] ?></td>
          <td><?= $row['methode'] ?></td>
          <td><?= $row['material'] ?></td>
          <td><?= $row['mesin'] ?></td>
          <td><?= $row['environment'] ?></td>
          <td><?= $row['countermeasure'] ?></td>
          <td><?= $status_badge ?></td>
          <td><?php if ($row['foto']) : ?>
  <a href="uploads/fishbone/<?= $row['foto']; ?>" target="_blank">
    <img src="uploads/fishbone/<?= $row['foto']; ?>" width="80">
  </a>
<?php else : ?>
  -
<?php endif; ?></td>
          <td>
            <!-- Tombol Edit -->
            <button type='button' class='btn btn-sm btn-success' data-toggle='modal' data-target='#editModal<?= $row['no_evaluasi'] ?>'>
              <i class='fas fa-edit'></i>
            </button>
          </td>
        </tr>

        <!-- Modal Edit -->
        <div class='modal fade' id='editModal<?= $row['no_evaluasi'] ?>' tabindex='-1' role='dialog' aria-labelledby='editModalLabel<?= $row['no_evaluasi'] ?>' aria-hidden='true'>
          <div class='modal-dialog modal-lg' role='document'>
            <div class='modal-content'>
              <form action='proses_edit1.php' method='post' enctype="multipart/form-data">
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
                    <div class='col-md-3 mb-2'>
                      <label for="status">Status</label>
                      <select name="status" class="form-control" required>
                        <option value="<?= $row['status'] ?>"><?= $row['status'] ?></option>
                        <option value="OK">OK</option>
                        <option value="NOT OK">NOT OK</option>
                      </select>
                  </div>
                  <div class='col-md-12 mb-2'>
                    <label for="foto">Upload Foto</label>
                     <input type="file" name="foto" class="form-control">
                    <small class="text-muted">
                      Format: JPG, PNG, JPEG | Max 2MB
                    </small>
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
                                $limit = 5; // jumlah nomor halaman yang ditampilkan
                                $start = max(1, $halaman - floor($limit / 2));
                                $end = min($pages, $start + $limit - 1);

                                // Adjust kalau posisi di awal/akhir
                                if ($end - $start + 1 < $limit) {
                                    $start = max(1, $end - $limit + 1);
                                }

                                // Tombol Prev
                                if ($halaman > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?halaman=' . ($halaman - 1) . '">&laquo;</a></li>';
                                } else {
                                    echo '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
                                }

                                // Tampilkan halaman pertama + ellipsis
                                if ($start > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?halaman=1">1</a></li>';
                                    if ($start > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }

                                // Halaman tengah
                                for ($i = $start; $i <= $end; $i++) {
                                    $active = ($i == $halaman) ? 'active' : '';
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?halaman=' . $i . '">' . $i . '</a></li>';
                                }

                                // Tampilkan halaman terakhir + ellipsis
                                if ($end < $pages) {
                                    if ($end < $pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?halaman=' . $pages . '">' . $pages . '</a></li>';
                                }

                                // Tombol Next
                                if ($halaman < $pages) {
                                    echo '<li class="page-item"><a class="page-link" href="?halaman=' . ($halaman + 1) . '">&raquo;</a></li>';
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


<?php
include('includes/footer.php');
?>