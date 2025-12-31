<?php
include('config/dbcon.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // =====================
    // PARAM FILTER
    // =====================
    $params = http_build_query([
        'kode_mesin'     => $_POST['kode_mesin_filter'] ?? '',
        'blok'           => $_POST['blok'] ?? '',
        'nik_mekanik'    => $_POST['nik_mekanik_filter'] ?? '',
        'tanggal_mulai'  => $_POST['tanggal_mulai'] ?? '',
        'tanggal_akhir'  => $_POST['tanggal_akhir'] ?? '',
        'halaman'        => $_POST['halaman'] ?? 1
    ]);

    // =====================
    // DATA EDIT
    // =====================
    $id_log      = $_POST['id_log'];
    $tanggal     = $_POST['tanggal'];
    $kode_mesin  = $_POST['kode_mesin'];
    $nomor_wo    = $_POST['nomor_wo'];
    $nik_mekanik = $_POST['nik_mekanik'];
    $nik_prod    = $_POST['nik_prod'];
    $kriteria    = $_POST['kriteria'];
    $jam_mulai   = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $tindakan    = $_POST['tindakan'];

    $query = "
        UPDATE log_downtime SET 
            tanggal     = '$tanggal',
            kode_mesin  = '$kode_mesin',
            nomor_wo    = '$nomor_wo',
            nik_mekanik = '$nik_mekanik',
            nik_prod    = '$nik_prod',
            kriteria    = '$kriteria',
            jam_mulai   = '$jam_mulai',
            jam_selesai = '$jam_selesai',
            tindakan    = '$tindakan'
        WHERE id_log = '$id_log'
    ";

    if (mysqli_query($con, $query)) {
        header("Location: downtime.php?status=berhasil&$params");
        exit;
    } else {
        echo "Gagal mengubah data: " . mysqli_error($con);
    }
}
