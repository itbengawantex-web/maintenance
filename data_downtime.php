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
    <div class="modal fade" id="TbhDowntimeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="code.php" method="POST" autocomplete="off">
        <div class="modal-body">
            <div class="form-group">
                <label for="">Kode Downtime</label>
                <input type="text" name="kodedowntime" class="form-control" placeholder="Kode Downtime" required>
            </div>
            <div class="form-group">
                <label for="">Jenis Mesin</label>
                <input type="text" name="jenismesin" class="form-control" placeholder="Jenis Mesin" required>
            </div>
            <div class="form-group">
                <label for="">Kriteria</label>
                <input type="text" name="kriteria" class="form-control" placeholder="Kriteria" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" name="TbhDowntime" class="btn btn-primary">Submit</button>
        </div>
        </div>
        </form>
    </div>
    </div>

        <!-- Content Header (Page header) -->
        <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Data Downtime</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Data Downtime</li>
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
            <div class="col-md-6">
                <div class="card">
                <div class="card-header">
                    <a href="#" class ="btn btn-primary"  data-toggle="modal" data-target="#TbhDowntimeModal">Tambah Data</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Downtime</th>
                                <th>Jenis Mesin</th>
                                <th>Kriteria</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include('config/dbcon.php');

                            $batas = 20; // jumlah data per halaman
                            $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
                            $mulai = ($halaman > 1) ? ($halaman * $batas) - $batas : 0;

                            // Hitung total data
                            $result = mysqli_query($con, "SELECT * FROM data_downtime");
                            $total = mysqli_num_rows($result);
                            $pages = ceil($total / $batas);

                            // Ambil data sesuai halaman
                            $query = "SELECT * FROM data_downtime LIMIT $mulai, $batas";
                            $query_run = mysqli_query($con, $query);
                            $no = $mulai + 1;

                            if (mysqli_num_rows($query_run) > 0) {
                                while ($row = mysqli_fetch_assoc($query_run)) {
                                    echo "<tr>
                                        <td>{$no}</td>
                                        <td>{$row['kode_downtime']}</td>
                                        <td>{$row['jenis_mesin']}</td>
                                        <td>{$row['kriteria']}</td>
                                        
                                    </tr>";
                                    $no++;
                                }
                            } else {
                                echo "<tr><td colspan='5'>Tidak ada data ditemukan</td></tr>";
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