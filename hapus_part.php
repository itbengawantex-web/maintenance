<?php
session_start();
include('config/dbcon.php');

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $query = "DELETE FROM part_mesin WHERE id_part='$id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['status'] = "Data Part berhasil dihapus!";
        header("Location: data_part.php");
        exit();
    } else {
        $_SESSION['status'] = "Gagal menghapus data!";
        header("Location: data_part.php");
        exit();
    }

} else {
    $_SESSION['status'] = "Data tidak valid!";
    header("Location: data_part.php");
    exit();
}
?>