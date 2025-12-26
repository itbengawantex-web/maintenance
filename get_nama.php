<?php
include 'config/dbcon.php'; // koneksi ke database

if (isset($_GET['nik'])) {
    $nik = mysqli_real_escape_string($con, $_GET['nik']);
    $query = "SELECT emp_name FROM employee WHERE emp_nik = '$nik'";
    $result = mysqli_query($con, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo $row['emp_name'];
    } else {
        echo 'Tidak ditemukan';
    }
}
?>