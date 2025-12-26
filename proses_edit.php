<?php
include('config/dbcon.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_log = $_POST['id_log'];
    $tanggal = $_POST['tanggal'];
    $kode_mesin = $_POST['kode_mesin'];
    $nomor_wo = $_POST['nomor_wo'];
    $nik_mekanik = $_POST['nik_mekanik'];
    $nik_prod = $_POST['nik_prod'];
    $kriteria = $_POST['kriteria'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $tindakan = $_POST['tindakan'];
    $halaman = $_POST['halaman'] ?? 1;

    $query = "UPDATE log_downtime SET 
                tanggal = '$tanggal',
                kode_mesin = '$kode_mesin',
                nomor_wo = '$nomor_wo',
                nik_mekanik = '$nik_mekanik',
                nik_prod = '$nik_prod',
                kriteria = '$kriteria',
                jam_mulai = '$jam_mulai',
                jam_selesai = '$jam_selesai',
                tindakan = '$tindakan'
              WHERE id_log = '$id_log'";

    $result = mysqli_query($con, $query);

    if ($result) {
        $halaman = $_POST['halaman'] ?? 1;
header("Location: downtime.php?status=berhasil&halaman=$halaman");
    } else {
        echo "Gagal mengubah data: " . mysqli_error($con);
    }
}
?>
