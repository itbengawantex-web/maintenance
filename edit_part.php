<?php
session_start();
include('config/dbcon.php');

if (isset($_POST['id_part']) && isset($_POST['nama_part'])) {

    $id = $_POST['id_part'];
    $nama = $_POST['nama_part'];

    $query = "UPDATE part_mesin SET nama_part='$nama' WHERE id_part='$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['status'] = "Data Part berhasil diupdate!";
        header("Location: data_part.php");
        exit();
    } else {
        $_SESSION['status'] = "Gagal update data!";
        header("Location: data_part.php");
        exit();
    }

} else {
    $_SESSION['status'] = "Data tidak valid!";
    header("Location: data_part.php");
    exit();
}
?>