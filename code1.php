<?php
session_start();
include('config/dbcon.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Tentukan aksi yang dikirim dari form
    $aksi = $_POST['aksi'] ?? '';

    /* ==========================================================
       MODE 1 : SIMPAN DATA EVALUASI
       ========================================================== */
    if ($aksi == "evaluasi") {

        $tanggal        = $_POST['tanggal'] ?? null;
        $kode_mesin     = $_POST['kode_mesin'] ?? null;
        $kerusakan      = $_POST['kerusakan'] ?? null;
        $man            = $_POST['man'] ?? null;
        $methode        = $_POST['methode'] ?? null;
        $material       = $_POST['material'] ?? null;
        $mesin          = $_POST['mesin'] ?? null;
        $environment    = $_POST['environment'] ?? null; 
        $countermeasure = $_POST['countermeasure'] ?? null;
        $status         = $_POST['status'] ?? null;

        $query = "INSERT INTO evaluasi 
            (tanggal, kode_mesin, kerusakan, man, methode, material, mesin, environment, countermeasure, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssssssss",
                $tanggal, $kode_mesin, $kerusakan, $man, $methode,
                $material, $mesin, $environment, $countermeasure, $status
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['status'] = "Data evaluasi berhasil disimpan!";
                $_SESSION['status_code'] = "success";
            } else {
                $_SESSION['status'] = "Query error evaluasi: " . mysqli_stmt_error($stmt);
                $_SESSION['status_code'] = "error";
            }

            mysqli_stmt_close($stmt);
        }

        header("Location: tambaheval.php");
        exit();
    }

    /* ==========================================================
       MODE 2 : SIMPAN DATA KANBAN EXTRUDER
       ========================================================== */
    elseif ($aksi == "kanban") {

        $tanggal            = $_POST['tanggal'] ?? null;
        $departement        = $_POST['departement'] ?? null;
        $nama_mesin         = $_POST['nama_mesin'] ?? null;
        
        $waktu_stop_mesin   = $_POST['waktu_stop_mesin'] ?? null;
        $waktu_mulai_pm     = $_POST['waktu_mulai_pm'] ?? null;
        $waktu_selesai_pm   = $_POST['waktu_selesai_pm'] ?? null;
        
        $waktu_start_up     = $_POST['waktu_start_up'] ?? null;
        $waktu_normal_run   = $_POST['waktu_normal_run'] ?? null;
        $keterangan         = $_POST['keterangan'] ?? null;

        $query = "INSERT INTO kanban_downtime 
            (tanggal, departement, nama_mesin, waktu_stop_mesin, waktu_mulai_pm, waktu_selesai_pm,
             waktu_start_up, waktu_normal_run, keterangan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssssss",
                $tanggal, $departement, $nama_mesin, $waktu_stop_mesin, 
                $waktu_mulai_pm, $waktu_selesai_pm, $waktu_start_up, 
                $waktu_normal_run, $keterangan
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['status'] = "Data Kanban berhasil disimpan!";
                $_SESSION['status_code'] = "success";
            } else {
                $_SESSION['status'] = "Query error kanban: " . mysqli_stmt_error($stmt);
                $_SESSION['status_code'] = "error";
            }

            mysqli_stmt_close($stmt);
        }

        header("Location: tambahkanban.php");
        exit();
    }

}
?>
