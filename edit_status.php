<?php
include('config/dbcon.php');

if (isset($_POST['ubah_status'])) {
    $id_log = $_POST['id_log'];
    $status = $_POST['status'];
    $halaman = $_POST['halaman'] ?? 1;

    $query = "UPDATE log_downtime SET status = '$status' WHERE id_log = '$id_log'";
    $result = mysqli_query($con, $query);

    if ($result) {
                $halaman = $_POST['halaman'] ?? 1;
header("Location: downtime.php?status=berhasil&halaman=$halaman");
    } else {
        echo "Gagal mengubah status: " . mysqli_error($con);
    }
}
?>