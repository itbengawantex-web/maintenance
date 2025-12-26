<?php
include('config/dbcon.php');

$no_evaluasi = $_POST['no_evaluasi'];
$tanggal = $_POST['tanggal'];
$kode_mesin = $_POST['kode_mesin'];
$kerusakan = $_POST['kerusakan'];
$man = $_POST['man'];
$methode = $_POST['methode'];
$material = $_POST['material'];
$mesin = $_POST['mesin'];
$environment = $_POST['environment'];
$countermeasure = $_POST['countermeasure'];
$halaman = $_POST['halaman'] ?? 1;


$query = "UPDATE evaluasi SET 
    tanggal='$tanggal',
    kode_mesin='$kode_mesin',
    kerusakan='$kerusakan',
    man='$man',
    methode='$methode',
    material='$material',
    mesin='$mesin',
    environment='$environment',
    countermeasure='$countermeasure'

    WHERE no_evaluasi='$no_evaluasi'";

if (mysqli_query($con, $query)) {
    header("Location: evaluasi.php?status=berhasil&halaman=$halaman");
} else {
    echo "Error: " . mysqli_error($con);
}
?>
