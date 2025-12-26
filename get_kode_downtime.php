<?php
include('config/dbcon.php');
if (isset($_GET['kriteria'])) {
    $kriteria = mysqli_real_escape_string($con, $_GET['kriteria']);
    $query = "SELECT kode_downtime FROM data_downtime WHERE kriteria = '$kriteria' LIMIT 1";
    $result = mysqli_query($con, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode(['kode_downtime' => $row['kode_downtime']]);
    } else {
        echo json_encode(['kode_downtime' => null]);
    }
}
?>