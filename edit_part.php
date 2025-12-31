<?php
include('config/dbcon.php');

$id_part   = $_POST['id_part'];
$nama_part = $_POST['nama_part'];
$halaman   = $_POST['halaman'] ?? 1;

$query = "UPDATE part_mesin SET nama_part='$nama_part' WHERE id_part='$id_part'";

if (mysqli_query($con, $query)) {
    header("Location: data_part.php?status=updated&halaman=$halaman");
    exit();
} else {
    echo mysqli_error($con);
}