<?php
include('config/dbcon.php');

if (isset($_GET['term'])) {
    $term = mysqli_real_escape_string($con, $_GET['term']);
    $query = "SELECT DISTINCT kriteria FROM data_downtime WHERE kriteria LIKE '%$term%'";
    $result = mysqli_query($con, $query);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'value' => $row['kriteria'], // Nilai yang akan diisi ke input
            'label' => $row['kriteria'] // Teks yang ditampilkan di saran
        ];
    }
    
    echo json_encode($data); // Mengembalikan data dalam format JSON
}
?>
