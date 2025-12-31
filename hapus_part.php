<?php
include('config/dbcon.php');

$id      = $_GET['id'];
$halaman = $_GET['halaman'] ?? 1;

$query = "DELETE FROM part_mesin WHERE id_part='$id'";

if (mysqli_query($con, $query)) {
    header("Location: data_part.php?status=deleted&halaman=$halaman");
    exit();
} else {
    echo mysqli_error($con);
}