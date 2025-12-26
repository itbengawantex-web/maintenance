<?php
include('config/dbcon.php');

if (isset($_GET['term'])) {
    $term = mysqli_real_escape_string($con, $_GET['term']);
    $query = "SELECT id_part, nama_part FROM part_mesin WHERE id_part LIKE '%$term%' OR nama_part LIKE '%$term%'";
    $result = mysqli_query($con, $query);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'value' => $row['id_part'],
            'label' => $row['id_part'] . ' - ' . $row['nama_part']
        ];
    }
    
    echo json_encode($data);
}
?>
