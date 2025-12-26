<?php
session_start();
include('config/dbcon.php');

if (isset($_POST['TbhMesin'])) {
    $kode   = $_POST['kode'];
    $jenis  = $_POST['jenis'];
    $nama   = $_POST['nama'];
    $lokasi = $_POST['lokasi'];

    $query = "INSERT INTO data_mesin (kode_mesin, jenis_mesin, nama_mesin, lokasi)
              VALUES ('$kode', '$jenis', '$nama', '$lokasi')";
    
    $result = mysqli_query($con, $query);

    if ($result) {
        $_SESSION['status'] = "Tambah Data Mesin Sukses";
    } else {
        $_SESSION['status'] = "Tambah Data Mesin Gagal: " . mysqli_error($con);
    }

    header("Location: data_mesin.php");
    exit();
}

if (isset($_POST['TbhPart'])) {
    $kode_part   = $_POST['kode_part'];
    $nama_part  = $_POST['nama_part'];


    $query = "INSERT INTO part_mesin (id_part, nama_part)
              VALUES ('$kode_part', '$nama_part')";
    
    $result = mysqli_query($con, $query);

    if ($result) {
        $_SESSION['status'] = "Tambah Data Part Sukses";
    } else {
        $_SESSION['status'] = "Tambah Data Part Gagal: " . mysqli_error($con);
    }

    header("Location: data_part.php");
    exit();
}

if (isset($_POST['TbhDowntime'])) {
    $kodedowntime   = $_POST['kodedowntime'];
    $jenismesin  = $_POST['jenismesin'];
    $kriteria  = $_POST['kriteria'];

    $query = "INSERT INTO data_downtime (kode_downtime, jenis_mesin, kriteria)
              VALUES ('$kodedowntime', '$jenismesin', '$kriteria')";
    
    $result = mysqli_query($con, $query);

    if ($result) {
        $_SESSION['status'] = "Tambah Data Downtime Sukses";
    } else {
        $_SESSION['status'] = "Tambah Data Downtime Gagal: " . mysqli_error($con);
    }

    header("Location: data_downtime.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data downtime
    $tanggal = $_POST['tanggal'];
    $kode_mesin = $_POST['kode_mesin'];
    $nomor_wo = $_POST['nomor_wo'];
    $nik_prod = $_POST['nik_prod'];
    $nik_mekanik = $_POST['nik_mekanik'];
    $kriteria = $_POST['kriteria'];
    $tindakan = $_POST['tindakan'];
    $kode_downtime = $_POST['kode_downtime'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $status = $_POST['status'];

    // Insert ke log_downtime
    $query_insert = "INSERT INTO log_downtime (
        tanggal, kode_mesin, nomor_wo, nik_prod, nik_mekanik,
        kriteria, tindakan, kode_downtime, jam_mulai, jam_selesai, status
    ) VALUES (
        '$tanggal', '$kode_mesin', '$nomor_wo', '$nik_prod', '$nik_mekanik',
        '$kriteria','$tindakan', '$kode_downtime', '$jam_mulai', '$jam_selesai', '$status'
    )";

    $insert_result = mysqli_query($con, $query_insert);

    if ($insert_result) {
        // Loop untuk part1, part2, part3
        for ($i = 1; $i <= 3; $i++) {
            $kode_part = $_POST['kode_part' . $i] ?? '';
            $jumlah = $_POST['jumlah' . $i] ?? 0;

            if (!empty($kode_part) && $jumlah > 0) {
                $query_part = "INSERT INTO pemakaian_part (
                    tanggal, nik_mekanik, kode_mesin, id_part, jumlah
                ) VALUES (
                    '$tanggal', '$nik_mekanik', '$kode_mesin', '$kode_part', '$jumlah'
                )";
                mysqli_query($con, $query_part);
            }
        }

        $_SESSION['status'] = "Tambah Log Downtime & Part Sukses";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Tambah Log Downtime Gagal: " . mysqli_error($con);
        $_SESSION['status_code'] = "error";
    }

    header("Location: tambahlog.php");
    exit();
}

// Query SELECT tetap di luar if


?>
