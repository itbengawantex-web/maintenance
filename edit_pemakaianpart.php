<?php
include('config/dbcon.php');

$log_part    = $_POST['log_part'];
$tanggal     = $_POST['tanggal'];
$kode_mesin  = $_POST['kode_mesin'];
$nik_mekanik = $_POST['nik_mekanik'];
$id_part     = $_POST['id_part'];
$jumlah      = $_POST['jumlah'];

$query = "UPDATE pemakaian_part 
          SET tanggal='$tanggal',
              kode_mesin='$kode_mesin',
              nik_mekanik='$nik_mekanik',
              id_part='$id_part',
              jumlah='$jumlah'
          WHERE log_part='$log_part'";

if (mysqli_query($con, $query)) {

    // ✅ Ambil SEMUA filter & halaman dari POST
    $params = $_POST;

    // ❌ buang field yang bukan filter
    unset(
        $params['log_part'],
        $params['tanggal'],
        $params['kode_mesin'],
        $params['nik_mekanik'],
        $params['id_part'],
        $params['jumlah']
    );

    // ✅ susun ulang query string
    $queryString = http_build_query($params);

    header("Location: pemakaian_part.php?status=updated&" . $queryString);
    exit();

} else {
    echo "Error: " . mysqli_error($con);
}
