<?php
session_start();
include('config/dbcon.php');
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

// Konfigurasi pagination
$limit = 40; 
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman - 1) * $limit;

$parts = [];
$part_query = "SELECT id_part, nama_part FROM part_mesin ORDER BY nama_part ASC";
$part_result = mysqli_query($con, $part_query);

if ($part_result && mysqli_num_rows($part_result) > 0) {
    while ($row = mysqli_fetch_assoc($part_result)) {
        $parts[] = $row;
    }
}

$where = [];

// Filter kode mesin
if (!empty($_GET['kode_mesin'])) {
    $kode_mesin = mysqli_real_escape_string($con, $_GET['kode_mesin']);
    $where[] = "p.kode_mesin LIKE '%$kode_mesin%'";
}

// Filter blok
if (!empty($_GET['blok'])) {
    $blok = mysqli_real_escape_string($con, $_GET['blok']);
    $where[] = "p.kode_mesin IN (SELECT kode_mesin FROM data_mesin WHERE lokasi = '$blok')";
}

// Filter tanggal
if (!empty($_GET['tanggal_mulai']) && !empty($_GET['tanggal_akhir'])) {
    $tanggal_mulai = mysqli_real_escape_string($con, $_GET['tanggal_mulai']);
    $tanggal_akhir = mysqli_real_escape_string($con, $_GET['tanggal_akhir']);
    $where[] = "p.tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
}


// Filter kode part
if (!empty($_GET['id_part'])) {
    $id_part = mysqli_real_escape_string($con, $_GET['id_part']);
    $where[] = "p.id_part = '$id_part'";
}

$where_clause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Hitung total data untuk pagination
$total_query = mysqli_query($con, "
    SELECT COUNT(*) as total 
    FROM pemakaian_part p
    LEFT JOIN part_mesin d ON p.id_part = d.id_part
    LEFT JOIN data_mesin m ON p.kode_mesin = m.kode_mesin
    $where_clause
");
if (!$total_query) {
    die("Query error: " . mysqli_error($con));
}
$total_data = mysqli_fetch_assoc($total_query)['total'];
$pages = ceil($total_data / $limit);

// Query data
$query = "
SELECT p.log_part, p.tanggal, p.nik_mekanik, p.kode_mesin, p.id_part, p.jumlah,
       d.nama_part, m.nama_mesin
FROM pemakaian_part p
LEFT JOIN part_mesin d ON p.id_part = d.id_part
LEFT JOIN data_mesin m ON p.kode_mesin = m.kode_mesin
$where_clause
ORDER BY p.tanggal DESC
LIMIT $offset, $limit";
$result = mysqli_query($con, $query);

// Query total jumlah part (berdasarkan filter)
$query_total = "
SELECT SUM(p.jumlah) as total_jumlah
FROM pemakaian_part p
$where_clause";
$result_total = mysqli_query($con, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_jumlah = $row_total['total_jumlah'] ?? 0;
?>


<!-- Content Wrapper -->
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Pemakaian Sparepart</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Pemakaian Sparepart</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container" style="max-width: 98%;">
    <div class="row">
      <div class="col-md-12">
        <div class="card">

          <div class="card-header">
            <form method="GET" class="d-flex gap-2 align-items-center mb-0">
              <input type="text" name="kode_mesin" class="form-control form-control-sm mr-2" 
                placeholder="Kode Mesin" style="width: 150px;"
                value="<?= isset($_GET['kode_mesin']) ? $_GET['kode_mesin'] : '' ?>" />

              <select name="blok" class="form-control form-control-sm mr-2" style="width: 150px;">
                <option value="">-- Pilih Blok --</option>
                <option value="CL BLOK 1" <?= isset($_GET['blok']) && $_GET['blok'] == 'CL BLOK 1' ? 'selected' : '' ?>>CL BLOK 1</option>
                <option value="CL BLOK 2" <?= isset($_GET['blok']) && $_GET['blok'] == 'CL BLOK 2' ? 'selected' : '' ?>>CL BLOK 2</option>
                <option value="CL BLOK 3" <?= isset($_GET['blok']) && $_GET['blok'] == 'CL BLOK 3' ? 'selected' : '' ?>>CL BLOK 3</option>
                <option value="CL BLOK 4" <?= isset($_GET['blok']) && $_GET['blok'] == 'CL BLOK 4' ? 'selected' : '' ?>>CL BLOK 4</option>
                <option value="FINISHING" <?= isset($_GET['blok']) && $_GET['blok'] == 'FINISHING' ? 'selected' : '' ?>>FINISHING</option>
                <option value="EXTRUDER" <?= isset($_GET['blok']) && $_GET['blok'] == 'EXTRUDER' ? 'selected' : '' ?>>EXTRUDER</option>
              </select>

              <input type="date" name="tanggal_mulai" class="form-control form-control-sm mr-2" 
                style="width: 140px;" value="<?= $_GET['tanggal_mulai'] ?? '' ?>" />
              <input type="date" name="tanggal_akhir" class="form-control form-control-sm mr-2" 
                style="width: 140px;" value="<?= $_GET['tanggal_akhir'] ?? '' ?>" />
              <select name="id_part" class="form-control form-control-sm mr-2" style="width: 180px;">
                  <option value="">-- Pilih Part Mesin --</option>
                     <?php foreach ($parts as $p): ?>
                  <option value="<?= $p['id_part'] ?>" <?= isset($_GET['id_part']) && $_GET['id_part'] == $p['id_part'] ? 'selected' : '' ?>>
                     <?= htmlspecialchars($p['nama_part']) ?>
                  </option>
                     <?php endforeach; ?>
              </select>
              <input type="hidden" name="halaman" value="<?= $_GET['halaman'] ?? 1 ?>">
              <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
            </form>
          </div>

          <div class="card-body">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>Kode Mesin</th>
                  <th>Amano MTC</th>
                  <th>Kode Part</th>
                  <th>Nama Part</th>
                  <th>Jumlah</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $no = $offset + 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>" . (!empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-') . "</td>
                        <td>{$row['kode_mesin']}</td>
                        <td>{$row['nik_mekanik']}</td>
                        <td>{$row['id_part']}</td>
                        <td>{$row['nama_part']}</td>
                        <td>{$row['jumlah']}</td>
                        <td>
                          <button type='button' class='btn btn-sm btn-primary'
                              data-toggle='modal'
                              data-target='#editModal{$row['log_part']}'>
                              <i class='fas fa-edit'></i>
                          </button>
                      </td>
                    
                    </tr>";
                    $no++;
                }
                ?>
              </tbody>
              <?php
mysqli_data_seek($result, 0); // Reset pointer result kalau diperlukan
while ($row = mysqli_fetch_assoc($result)) {
?>
<div class="modal fade" id="editModal<?php echo $row['log_part']; ?>" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Edit Data Part</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="edit_pemakaianpart.php" method="POST">
        <div class="modal-body">

          <input type="hidden" name="log_part" value="<?php echo $row['log_part']; ?>">

          <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" 
                   value="<?php echo $row['tanggal']; ?>">
          </div>

          <div class="form-group">
            <label>Kode Mesin</label>
            <input type="text" name="kode_mesin" class="form-control"
                   value="<?php echo $row['kode_mesin']; ?>">
          </div>

          <div class="form-group">
            <label>Amano / NIK Mekanik</label>
            <input type="text" name="nik_mekanik" class="form-control"
                   value="<?php echo $row['nik_mekanik']; ?>">
          </div>

          <div class="form-group">
            <label>Kode Part</label>
            <input type="text" name="id_part" class="form-control"
                   value="<?php echo $row['id_part']; ?>">
          </div>

          <div class="form-group">
            <label>Jumlah</label>
            <input type="number" name="jumlah" class="form-control"
                   value="<?php echo $row['jumlah']; ?>">
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>

      </form>

    </div>
  </div>
</div>
<?php } ?>

              <tfoot>
        <tr>
            <th colspan="6" class="text-right">Total Pemakaian Part:</th>
            <th><?= $total_jumlah ?></th>
        </tr>
    </tfoot>
            </table>

            <!-- Navigasi pagination -->
            <div class="card-footer clearfix">
              <ul class="pagination pagination-sm m-0 float-right">
                <?php
                $limit_page = 5; // jumlah nomor halaman yg tampil
                $start = max(1, $halaman - floor($limit_page / 2));
                $end = min($pages, $start + $limit_page - 1);

                if ($end - $start + 1 < $limit_page) {
                    $start = max(1, $end - $limit_page + 1);
                }

                // Tombol Prev
                if ($halaman > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?halaman=' . ($halaman - 1) . '">&laquo;</a></li>';
                } else {
                    echo '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
                }

                // Halaman pertama + ellipsis
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

                // Halaman terakhir + ellipsis
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

<?php include('includes/footer.php'); ?>

