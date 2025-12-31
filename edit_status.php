<?php
include('config/dbcon.php');

if (isset($_POST['ubah_status'])) {

    $id_log  = $_POST['id_log'];
    $status  = $_POST['status'];

    // ambil filter
    $params = http_build_query([
        'status'         => 'berhasil',
        'kode_mesin'     => $_POST['kode_mesin'] ?? '',
        'blok'           => $_POST['blok'] ?? '',
        'nik_mekanik'    => $_POST['nik_mekanik'] ?? '',
        'tanggal_mulai'  => $_POST['tanggal_mulai'] ?? '',
        'tanggal_akhir'  => $_POST['tanggal_akhir'] ?? '',
        'halaman'        => $_POST['halaman'] ?? 1
    ]);

    $query = "UPDATE log_downtime 
              SET status = '$status' 
              WHERE id_log = '$id_log'";

    if (mysqli_query($con, $query)) {
        header("Location: downtime.php?$params");
        exit;
    } else {
        echo "Gagal mengubah status: " . mysqli_error($con);
    }
}
?>
