<?php
session_start();
include('config/dbcon.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');



$filterParams = http_build_query([
    'kode_mesin'    => $_GET['kode_mesin'] ?? '',
    'blok'          => $_GET['blok'] ?? '',
    'nik_mekanik'   => $_GET['nik_mekanik'] ?? '',
    'tanggal_mulai' => $_GET['tanggal_mulai'] ?? '',
    'tanggal_akhir' => $_GET['tanggal_akhir'] ?? ''
]);

/* =======================
   PAGINATION
======================= */
$perPage = 40;
$halaman = isset($_GET['halaman']) ? max((int)$_GET['halaman'], 1) : 1;
$offset  = ($halaman - 1) * $perPage;

/* =======================
   FILTER INPUT (AMAN)
======================= */
$kode_mesin    = mysqli_real_escape_string($con, $_GET['kode_mesin'] ?? '');
$blok          = mysqli_real_escape_string($con, $_GET['blok'] ?? '');
$nik_mekanik   = mysqli_real_escape_string($con, $_GET['nik_mekanik'] ?? '');
$tanggal_mulai = mysqli_real_escape_string($con, $_GET['tanggal_mulai'] ?? '');
$tanggal_akhir = mysqli_real_escape_string($con, $_GET['tanggal_akhir'] ?? '');

/* =======================
   DEFAULT TANGGAL
======================= */
if ($tanggal_mulai === '' && $tanggal_akhir === '') {
    $tanggal_mulai = date('Y-m-01');
    $tanggal_akhir = date('Y-m-t');
}

/* =======================
   BUILD WHERE (DINAMIS)
======================= */
$where = [];
$where[] = "tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";

if ($kode_mesin !== '') {
    $where[] = "kode_mesin LIKE '%$kode_mesin%'";
}

if ($blok !== '') {
    $where[] = "blok = '$blok'";
}

if ($nik_mekanik !== '') {
    $where[] = "nik_mekanik = '$nik_mekanik'";
}

$whereSQL = 'WHERE ' . implode(' AND ', $where);

/* =======================
   TOTAL DATA
======================= */
$totalQuery = mysqli_query(
    $con,
    "SELECT COUNT(*) AS total FROM log_downtime $whereSQL"
);
$totalData = mysqli_fetch_assoc($totalQuery)['total'];
$pages = ceil($totalData / $perPage);

/* =======================
   DATA TABEL
======================= */
$query = "
    SELECT *
    FROM log_downtime
    $whereSQL
    ORDER BY tanggal DESC
    LIMIT $perPage OFFSET $offset
";

$result = mysqli_query($con, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($con));
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
                    
                    <form method="GET" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" 
      class="d-flex gap-2 align-items-center mb-0">

    <a href="tambahlog.php" class="btn btn-primary mr-2">Tambah Log</a>

    <input type="text" name="kode_mesin"
           class="form-control form-control-sm mr-2"
           placeholder="Kode Mesin"
           style="width: 150px;"
           value="<?= $_GET['kode_mesin'] ?? '' ?>">

    <select name="blok" class="form-control form-control-sm mr-2" style="width: 150px;">
        <option value="">-- Pilih Blok --</option>
        <?php
        $blokList = ['CL BLOK 1','CL BLOK 2','CL BLOK 3','CL BLOK 4','FINISHING','EXTRUDER'];
        foreach ($blokList as $b) {
            $selected = (($_GET['blok'] ?? '') === $b) ? 'selected' : '';
            echo "<option value='$b' $selected>$b</option>";
        }
        ?>
    </select>

    <input type="text" name="nik_mekanik"
           class="form-control form-control-sm mr-2"
           placeholder="NIK Mekanik"
           style="width: 150px;"
           value="<?= $_GET['nik_mekanik'] ?? '' ?>">

    <input type="date" name="tanggal_mulai"
           class="form-control form-control-sm mr-2"
           style="width: 140px;"
           value="<?= $_GET['tanggal_mulai'] ?? '' ?>">

    <input type="date" name="tanggal_akhir"
           class="form-control form-control-sm mr-2"
           style="width: 140px;"
           value="<?= $_GET['tanggal_akhir'] ?? '' ?>">

    <!-- 🔴 WAJIB: reset ke halaman 1 saat filter -->
    <input type="hidden" name="halaman" value="1">

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
        echo "
<tr>
    <td>{$no}</td>
    <td>" . (!empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-') . "</td>
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
$kode_mesin    = $_GET['kode_mesin'] ?? '';
$blok          = $_GET['blok'] ?? '';
$nik_mekanik   = $_GET['nik_mekanik'] ?? '';
$tgl_mulai     = $_GET['tanggal_mulai'] ?? '';
$tgl_akhir     = $_GET['tanggal_akhir'] ?? '';
$halaman       = $_GET['halaman'] ?? 1;
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
        <input type='hidden' name='halaman' value='{$halaman}'>
        <input type='hidden' name='kode_mesin_filter' value='{$kode_mesin}'>
        <input type='hidden' name='blok' value='{$blok}'>
        <input type='hidden' name='nik_mekanik' value='{$nik_mekanik}'>
        <input type='hidden' name='tanggal_mulai' value='{$tgl_mulai}'>
        <input type='hidden' name='tanggal_akhir' value='{$tgl_akhir}'>
          
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
          <input type='hidden' name='halaman' value='{$halaman}'>
          <input type='hidden' name='kode_mesin' value='{$kode_mesin}'>
          <input type='hidden' name='blok' value='{$blok}'>
          <input type='hidden' name='nik_mekanik' value='{$nik_mekanik}'>
          <input type='hidden' name='tanggal_mulai' value='{$tgl_mulai}'>
          <input type='hidden' name='tanggal_akhir' value='{$tgl_akhir}'>
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
                           <?php if ($pages > 1) { ?>
<div class="card-footer clearfix">
    <ul class="pagination pagination-sm m-0 float-right">

        <!-- PREV -->
        <?php if ($halaman > 1) { ?>
            <li class="page-item">
                <a class="page-link" href="?halaman=<?= $halaman - 1 ?>&<?= $filterParams ?>">
                    &laquo;
                </a>
            </li>
        <?php } else { ?>
            <li class="page-item disabled">
                <span class="page-link">&laquo;</span>
            </li>
        <?php } ?>

        <!-- NOMOR HALAMAN -->
        <?php
        $limit = 5; // jumlah halaman yang ditampilkan
        $start = max(1, $halaman - floor($limit / 2));
        $end   = min($pages, $start + $limit - 1);

        if ($end - $start + 1 < $limit) {
            $start = max(1, $end - $limit + 1);
        }

        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $halaman) ? 'active' : '';
        ?>
            <li class="page-item <?= $active ?>">
                <a class="page-link" href="?halaman=<?= $i ?>&<?= $filterParams ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php } ?>

        <!-- NEXT -->
        <?php if ($halaman < $pages) { ?>
            <li class="page-item">
                <a class="page-link" href="?halaman=<?= $halaman + 1 ?>&<?= $filterParams ?>">
                    &raquo;
                </a>
            </li>
        <?php } else { ?>
            <li class="page-item disabled">
                <span class="page-link">&raquo;</span>
            </li>
        <?php } ?>

    </ul>
</div>
<?php } ?>



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