<?php
include('config/dbcon.php');


$log_part = $_POST['log_part'];
$tanggal = $_POST['tanggal'];
$kode_mesin = $_POST['kode_mesin'];
$nik_mekanik = $_POST['nik_mekanik'];
$id_part = $_POST['id_part'];
$nama_part = $_POST['nama_part'];
$jumlah = $_POST['jumlah'];

$query = "UPDATE pemakaian_part 
          SET tanggal='$tanggal',
              kode_mesin='$kode_mesin',
              nik_mekanik='$nik_mekanik',
              id_part='$id_part',
              jumlah='$jumlah'
          WHERE log_part='$log_part'";

if (mysqli_query($con, $query)) {
    header("Location: pemakaian_part.php?status=updated");
    exit();
} else {
    echo "Error: " . mysqli_error($con);
}
