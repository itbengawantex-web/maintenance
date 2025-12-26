<?php
include('includes/header.php');
include('config/dbcon.php');

// Konfigurasi pagination
$limit = 40; // Jumlah data per halaman
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman - 1) * $limit;
$parts = [];
$part_query = mysqli_query($con, "SELECT id_part, nama_part FROM part_mesin ORDER BY nama_part ASC");
while ($row = mysqli_fetch_assoc($part_query)) {
    $parts[] = $row;
}
$where = [];

// Filter kode_mesin
if (!empty($_GET['kode_mesin'])) {
    $kode_mesin = mysqli_real_escape_string($con, $_GET['kode_mesin']);
    $where[] = "ld.kode_mesin LIKE '%$kode_mesin%'";
}

// Filter tanggal
if (!empty($_GET['tanggal_mulai']) && !empty($_GET['tanggal_akhir'])) {
    $tanggal_mulai = mysqli_real_escape_string($con, $_GET['tanggal_mulai']);
    $tanggal_akhir = mysqli_real_escape_string($con, $_GET['tanggal_akhir']);
    $where[] = "ld.tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_akhir'";
}

// Filter blok/lokasi
if (!empty($_GET['blok'])) {
    $blok = mysqli_real_escape_string($con, $_GET['blok']);
    $where[] = "dm.lokasi = '$blok'";
}

// Filter kriteria
if (!empty($_GET['kriteria'])) {
    $kriteria = mysqli_real_escape_string($con, $_GET['kriteria']);
    $where[] = "ld.kriteria LIKE '%$kriteria%'";
}

// Filter status
if (!empty($_GET['status'])) {
    $status = mysqli_real_escape_string($con, $_GET['status']);
    $where[] = "ld.status = '$status'";
}

// ✅ Filter part mesin
if (!empty($_GET['id_part'])) {
    $id_part = mysqli_real_escape_string($con, $_GET['id_part']);
    $where[] = "p.id_part = '$id_part'";
}

// Build WHERE SQL
$where_sql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// Hitung total data (pagination)
$total_query = mysqli_query($con, "
    SELECT COUNT(*) as total 
    FROM log_downtime ld
    JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    $where_sql
") or die("SQL Error (total): " . mysqli_error($con));


$total_data = mysqli_fetch_assoc($total_query)['total'];
$pages = ceil($total_data / $limit);

// Ambil data sesuai filter + pagination
$query = "
    SELECT ld.*, dm.lokasi
    FROM log_downtime ld
    LEFT JOIN data_mesin dm ON ld.kode_mesin = dm.kode_mesin
    $where_sql
    ORDER BY ld.tanggal DESC
    LIMIT $offset, $limit
";

$result = mysqli_query($con, $query) or die("SQL Error (data): " . mysqli_error($con));



?>


<div class="container" style="max-width: 98%;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                              <form method="GET" class="d-flex gap-2 align-items-center mb-0">
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

                                <input type="date" name="tanggal_mulai" class="form-control form-control-sm mr-2" style="width: 140px;" value="<?= isset($_GET['tanggal_mulai']) ? $_GET['tanggal_mulai'] : '' ?>" />

                                <input type="date" name="tanggal_akhir" class="form-control form-control-sm mr-2" style="width: 140px;" value="<?= isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : '' ?>" />

                                <!-- Input baru untuk pencarian kriteria -->
                                <input type="text" name="kriteria" class="form-control form-control-sm mr-2" placeholder="Cari Kriteria" style="width: 150px;" value="<?= isset($_GET['kriteria']) ? $_GET['kriteria'] : '' ?>" />
                                <select name="status" class="form-control form-control-sm mr-2" style="width: 150px;">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="Major" <?= isset($_GET['status']) && $_GET['status'] == 'Major' ? 'selected' : '' ?>>Major</option>
                                    <option value="Minor" <?= isset($_GET['status']) && $_GET['status'] == 'Minor' ? 'selected' : '' ?>>Minor</option>
                                </select>


                                <button type="submit" class="btn btn-sm btn-primary">Tampilkan</button>
                            </form>


                              <a href="export_excel.php?<?= http_build_query($_GET) ?>" target="_blank" class="btn btn-sm btn-success" style="margin-left: auto;">
                                  Export Excel
                              </a>
                          </div>
                                                      
                            <!-- /.card-header -->
                            <div class="card-body">
                           <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Mesin</th>
                                        <th>Tanggal</th>
                                        <th>Nomor WO</th>
                                        <th>NIK Mekanik</th>
                                        <th>Kriteria Kerusakan</th>
                                        <th>Action MTC/UTY</th>
                                        <th>Jam Mulai</th>
                                        <th>Jam Selesai</th>
                                        <th>Total Menit</th>
                                        <th>Total Kerusakan</th>
                                        <th>Status</th>
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
                                                <td>{$row['kode_mesin']}</td>
                                                <td>" . (!empty($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-') . "</td>
                                                <td>{$row['nomor_wo']}</td>
                                                <td>{$row['nik_mekanik']}</td>
                                                <td>{$row['kriteria']}</td>
                                                <td>{$row['tindakan']}</td>
                                                <td>{$row['jam_mulai']}</td>
                                                <td>{$row['jam_selesai']}</td>
                                                <td>{$durasi_menit}</td>
                                                <td></td>
                                                <td>{$status_badge}</td>
                                            </tr>";
                                            $no++;
                                        }
                                        ?>
                                </tbody>
                            </table>

                                <!-- Navigasi pagination -->
                            <div class="card-footer clearfix">
                                <ul class="pagination pagination-sm m-0 float-right">
                                    <?php
                                    $query_string = $_GET;
                                    $limit = 5; // jumlah nomor halaman yang mau ditampilkan
                                    $start = max(1, $halaman - floor($limit / 2));
                                    $end = min($pages, $start + $limit - 1);

                                    // Adjust supaya range selalu penuh
                                    if ($end - $start + 1 < $limit) {
                                        $start = max(1, $end - $limit + 1);
                                    }

                                    // Tombol Prev
                                    if ($halaman > 1) {
                                        $query_string['halaman'] = $halaman - 1;
                                        $prev_url = '?' . http_build_query($query_string);
                                        echo '<li class="page-item"><a class="page-link" href="' . $prev_url . '">&laquo;</a></li>';
                                    } else {
                                        echo '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
                                    }

                                    // Halaman pertama + ellipsis
                                    if ($start > 1) {
                                        $query_string['halaman'] = 1;
                                        $first_url = '?' . http_build_query($query_string);
                                        echo '<li class="page-item"><a class="page-link" href="' . $first_url . '">1</a></li>';

                                        if ($start > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }

                                    // Halaman tengah
                                    for ($i = $start; $i <= $end; $i++) {
                                        $query_string['halaman'] = $i;
                                        $url = '?' . http_build_query($query_string);
                                        $active = ($halaman == $i) ? 'active' : '';
                                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . $url . '">' . $i . '</a></li>';
                                    }

                                    // Halaman terakhir + ellipsis
                                    if ($end < $pages) {
                                        if ($end < $pages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }

                                        $query_string['halaman'] = $pages;
                                        $last_url = '?' . http_build_query($query_string);
                                        echo '<li class="page-item"><a class="page-link" href="' . $last_url . '">' . $pages . '</a></li>';
                                    }

                                    // Tombol Next
                                    if ($halaman < $pages) {
                                        $query_string['halaman'] = $halaman + 1;
                                        $next_url = '?' . http_build_query($query_string);
                                        echo '<li class="page-item"><a class="page-link" href="' . $next_url . '">&raquo;</a></li>';
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