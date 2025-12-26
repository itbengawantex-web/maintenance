<?php
session_start();
include "/config/dbcon.php";
include "/includes/header.php";
include "/includes/topbar.php";
include "/includes/sidebar.php";


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

if (!empty($_GET['nik_mekanik'])) {
    $nik_mekanik = mysqli_real_escape_string($con, $_GET['nik_mekanik']);
    $where[] = "nik_mekanik = '$nik_mekanik'"; // sudah diperbaiki
}

if (!empty($_GET['blok'])) {
    $blok = mysqli_real_escape_string($con, $_GET['blok']);
    $where[] = "kode_mesin IN (SELECT kode_mesin FROM data_mesin WHERE lokasi = '$blok')";
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Hitung total data
$total_query = mysqli_query($con, "SELECT COUNT(*) as total FROM log_downtime $where_clause");
if (!$total_query) {
    die("Query Error (total): " . mysqli_error($con));
}
$total_data = mysqli_fetch_assoc($total_query)['total'];
$pages = ceil($total_data / $limit);

// Ambil data
$query = "SELECT * 
          FROM log_downtime
          $where_clause 
          ORDER BY tanggal DESC 
          LIMIT $offset, $limit";
$result = mysqli_query($con, $query);
if (!$result) {
    die("Query Error (data): " . mysqli_error($con));
}

?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">


    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Log Downtime</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">downtime</li>
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
                      <a href="tambahlog.php" class="btn btn-primary mr-2">Tambah Log </a>

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

                      <!-- 🔽 Tambahan Filter NIK Mekanik -->
                      <input type="text" name="nik_mekanik" class="form-control form-control-sm mr-2" placeholder="NIK Mekanik" style="width: 150px;" value="<?= isset($_GET['nik_mekanik']) ? $_GET['nik_mekanik'] : '' ?>" />

                      <input type="date" name="tanggal_mulai" class="form-control form-control-sm mr-2" style="width: 140px;" value="<?= isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '' ?>" />

                      <input type="date" name="tanggal_akhir" class="form-control form-control-sm mr-2" style="width: 140px;" value="<?= isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '' ?>" />

                      <input type="hidden" name="halaman" value="<?= $_GET['halaman'] ?? 1 ?>">

                      <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
                  </form>

                    </div>
                    
                    
                    <!-- /.card-header -->
                    <div class="card-body">
                      <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>No Mesin</th>
                        <th>No WO</th>
                        <th>Amano Prod</th>
                        <th>Amano MTC</th>
                        <th>Keterangan Kerusakan</th>
                        <th>Action MTC/UTY</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Menit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                      <?php
    $no = $offset + 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $jam_mulai = new DateTime($row['jam_mulai']);
        $jam_selesai = new DateTime($row['jam_selesai']);

        // Jika jam_selesai lebih kecil dari jam_mulai (lewat tengah malam)
        if ($jam_selesai < $jam_mulai) {
            $jam_selesai->modify('+1 day');
        }

        $durasi = $jam_mulai->diff($jam_selesai);
        $durasi_menit = ($durasi->h * 60) + $durasi->i;
        
        $status_badge = '';
if ($row['status'] == 'Major') {
    $status_badge = "<span class='badge bg-danger px-2 py-1' style='font-size: 0.9rem;'>Major</span>";
} elseif ($row['status'] == 'Minor') {
    $status_badge = "<span class='badge bg-success px-2 py-1' style='font-size: 0.9rem;'>Minor</span>";
} else {
    $status_badge = "<span class='badge bg-secondary px-2 py-1' style='font-size: 0.9rem;'>{$row['status']}</span>";
}
        echo "<tr>
            <td>$no</td>
            <td>{$row['tanggal']}</td>
            <td>{$row['kode_mesin']}</td>
            <td>{$row['nomor_wo']}</td>
            <td>{$row['nik_prod']}</td>
            <td>{$row['nik_mekanik']}</td>
            <td>{$row['kriteria']}</td>
            <td>{$row['tindakan']}</td>
            <td>{$row['jam_mulai']}</td>
            <td>{$row['jam_selesai']}</td>
            <td>{$durasi_menit}</td>
            <td>{$status_badge}</td>
            <td>
                <button type='button' class='btn btn-sm btn-primary' data-toggle='modal' data-target='#editModal{$row['id_log']}'>
                    <i class='fas fa-edit'></i>
                </button>
               <button type='button' class='btn btn-sm btn-success' data-toggle='modal' data-target='#statusModal{$row['id_log']}'>
                  <i class='fas fa-exchange-alt'></i>
              </button>
            </td>
        </tr>";
        $no++;

        echo "
<div class='modal fade' id='editModal{$row['id_log']}' tabindex='-1' aria-labelledby='editModalLabel{$row['id_log']}' aria-hidden='true'>
  <div class='modal-dialog modal-lg'>
    <div class='modal-content'>
      <form action='proses_edit.php' method='post'>
        <div class='modal-header'>
          <h5 class='modal-title'>Edit Log Downtime</h5>
          <button type='button' class='btn-close' data-dismiss='modal' aria-label='Close'></button>
        </div>
        <div class='modal-body'>
          <input type='hidden' name='id_log' value='{$row['id_log']}'>
          <input type='hidden' name='halaman' value='" . ($_GET['halaman'] ?? 1) . "'>
          <div class='row'>
            <div class='col-md-6 mb-2'>
              <label>Tanggal</label>
              <input type='date' name='tanggal' class='form-control' value='{$row['tanggal']}'>
            </div>
            <div class='col-md-6 mb-2'>
              <label>Kode Mesin</label>
              <input type='text' name='kode_mesin' class='form-control' value='{$row['kode_mesin']}'>
            </div>
            <div class='col-md-6 mb-2'>
              <label>Nomor WO</label>
              <input type='text' name='nomor_wo' class='form-control' value='{$row['nomor_wo']}'>
            </div>
            <div class='col-md-6 mb-2'>
              <label>Amano MTC</label>
              <input type='text' name='nik_mekanik' class='form-control' value='{$row['nik_mekanik']}'>
            </div>
            <div class='col-md-6 mb-2'>
              <label>Amano Prod</label>
              <input type='text' name='nik_prod' class='form-control' value='{$row['nik_prod']}'>
            </div>
            <div class='col-md-6 mb-2'>
              <label>Kriteria</label>
              <input type='text' name='kriteria' class='form-control' value='{$row['kriteria']}'>
            </div>
            <div class='col-md-6 mb-2'>
              <label>Jam Mulai</label>
              <input type='text' name='jam_mulai' class='form-control'  value='{$row['jam_mulai']}'>
            </div>
            <div class='col-md-6 mb-2'>
              <label>Jam Selesai</label>
              <input type='text' name='jam_selesai' class='form-control'  value='{$row['jam_selesai']}'>
            </div>
            <div class='col-md-12 mb-2'>
              <label>Tindakan</label>
              <input type='text' name='tindakan' class='form-control' value='{$row['tindakan']}'>
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

    <!-- Modal untuk ubah status -->
    <div class='modal fade' id='statusModal{$row['id_log']}' tabindex='-1' role='dialog' aria-labelledby='statusModalLabel{$row['id_log']}' aria-hidden='true'>
      <div class='modal-dialog modal-dialog-centered' role='document'>
        <div class='modal-content'>
          <div class='modal-header'>
            <h5 class='modal-title' id='statusModalLabel{$row['id_log']}'>Ubah Status</h5>
            <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
              <span aria-hidden='true'>&times;</span>
            </button>
          </div>
          <form action='edit_status.php' method='POST'>
            <div class='modal-body'>
              <input type='hidden' name='id_log' value='{$row['id_log']}'>
              <input type='hidden' name='halaman' value='" . ($_GET['halaman'] ?? 1) . "'>
              <p>Ubah status downtime untuk mesin <strong>{$row['kode_mesin']}</strong> tanggal <strong>{$row['tanggal']}</strong>:</p>
              <select class='form-control' name='status'>
                <option value='Major' " . ($row['status'] == 'Major' ? 'selected' : '') . ">Major</option>
                <option value='Minor' " . ($row['status'] == 'Minor' ? 'selected' : '') . ">Minor</option>
              </select>
            </div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-secondary' data-dismiss='modal'>Batal</button>
              <button type='submit' name='ubah_status' class='btn btn-primary'>Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    ";


    }
    
?>
                    </tbody>
                  </table>
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