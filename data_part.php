<?php
session_start();


include('includes/header.php');
include('includes/topbar.php');
$currentPage = basename($_SERVER['PHP_SELF']);
$isDataMaster = in_array($currentPage, ['data_mesin.php', 'data_downtime.php']);
include('includes/sidebar.php');
?>
<!-- Content Wrapper. Contains page content -->

<!-- /.content-header -->
    <div class="content-wrapper">
        
        <!-- Modal -->
    <div class="modal fade" id="TbhMesinModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Tambah Data Part</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="code.php" method="POST" autocomplete="off">
        <div class="modal-body">
            <div class="form-group">
                <label for="">Kode Part</label>
                <input type="text" name="kode_part" class="form-control" placeholder="Kode Part" required>
            </div>
            <div class="form-group">
                <label for="">Nama Part</label>
                <input type="text" name="nama_part" class="form-control" placeholder="Nama Part" required>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="TbhPart" class="btn btn-primary">Submit</button>
        </div>
        </div>
        </form>
    </div>
    </div>
        <!-- Content Header (Page header) -->
        <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-6">
            
            <div class="col-sm-6">
                
                <h1>Data Part Mesin</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Data Part Mesin</li>
                </ol>
            </div>
            </div>
        </div><!-- /.container-fluid -->
        </section>
    <!-- Main content -->
        <section class="content">
        <div class="container-fluid">
            <div class="row">
            <div class="col-md-12">
                <?php if (isset($_SESSION['status'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?= $_SESSION['status']; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['status']); ?>
                <?php endif; ?>
                <div class="card">
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                <div class="card-header">
                    <a href="#" class ="btn btn-primary" data-toggle="modal" data-target="#TbhMesinModal1">Tambah Data</a>
                     <!-- Tombol trigger modal
                     <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Launch demo modal
                    </button> -->

                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Part</th>
                                <th>Nama Part Mesin</th>
                                <th>Aksi aksi </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include('config/dbcon.php');

                            $batas = 20; // jumlah data per halaman
                            $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
                            $mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

                            // Hitung total data
                            $result = mysqli_query($con, "SELECT * FROM part_mesin");
                            $total = mysqli_num_rows($result);
                            $pages = ceil($total / $batas);

                            // Ambil data sesuai halaman
                            $query = "SELECT * FROM part_mesin LIMIT $mulai, $batas";
                            $query_run = mysqli_query($con, $query);
                            $no = $mulai + 1;

                            if (mysqli_num_rows($query_run) > 0) {
                                while ($row = mysqli_fetch_assoc($query_run)) {
                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['id_part']}</td>
                                        <td>{$row['nama_part']}</td>
                                        <td>
    <button type='button' class='btn btn-sm btn-primary'
        data-toggle='modal'
        data-target='#editModal{$row['id_part']}'>
        <i class='fas fa-edit'></i>
    </button>

    <button type='button' class='btn btn-sm btn-danger'
        data-toggle='modal'
        data-target='#hapusModal{$row['id_part']}'>
        <i class='fas fa-trash'></i>
    </button>
</td>

                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='5'>Tidak ada data ditemukan</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
<?php
mysqli_data_seek($query_run, 0); // reset pointer loop
while ($row = mysqli_fetch_assoc($query_run)) :
?>
<!-- Modal Edit -->
<div class="modal fade" id="editModal<?= $row['id_part']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="edit_part.php" method="POST">
            <input type="hidden" name="id_part" value="<?= $row['id_part']; ?>">
            <input type="hidden" name="halaman" value="<?= $_GET['halaman'] ?? 1 ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Part Mesin</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id_part" value="<?= $row['id_part']; ?>">

                    <div class="form-group">
                        <label>Nama Part</label>
                        <input type="text" name="nama_part" class="form-control" value="<?= $row['nama_part']; ?>" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endwhile; ?>

<?php
mysqli_data_seek($query_run, 0); 
while ($row = mysqli_fetch_assoc($query_run)) :
?>
<div class="modal fade" id="hapusModal<?= $row['id_part']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form action="hapus_part.php" method="GET">
                <input type="hidden" name="id" value="<?= $row['id_part']; ?>">
                <input type="hidden" name="halaman" value="<?= $_GET['halaman'] ?? 1 ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <p>Yakin ingin menghapus part: <b><?= $row['id_part']; ?></b> ?</p>
                    <input type="hidden" name="id" value="<?= $row['id_part']; ?>">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>

        </div>
    </div>
</div>
<?php endwhile; ?>
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

                            // Halaman pertama + ellipsis kiri
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

                            // Halaman terakhir + ellipsis kanan
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
        </section>
    </div>
</div>
<?php
include('includes/footer.php');
?>