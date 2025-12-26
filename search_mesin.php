<?php
include('config/dbcon.php');

if (isset($_GET['term'])) {
    $term = mysqli_real_escape_string($con, $_GET['term']);
    $query = "SELECT kode_mesin, nama_mesin FROM data_mesin WHERE kode_mesin LIKE '%$term%' OR nama_mesin LIKE '%$term%'";
    $result = mysqli_query($con, $query);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'value' => $row['kode_mesin'],
            'label' => $row['kode_mesin'] . ' - ' . $row['nama_mesin']
        ];
    }
    
    echo json_encode($data);
}
?>
