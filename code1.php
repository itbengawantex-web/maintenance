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

        $tanggal        = $_POST['tanggal'];
    $kode_mesin     = $_POST['kode_mesin'];
    $kerusakan      = $_POST['kerusakan'];
    $man            = $_POST['man'];
    $methode        = $_POST['methode'];
    $material       = $_POST['material'];
    $mesin          = $_POST['mesin'];
    $environment    = $_POST['environment'];
    $countermeasure= $_POST['countermeasure'];
    $status         = $_POST['status'];

    // =============================
    // UPLOAD FOTO
    // =============================
    $foto = NULL;

if (!empty($_FILES['foto']['name'])) {

    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png'];

    if (!in_array($ext, $allowed)) {
        $_SESSION['status'] = "Format foto tidak valid!";
        $_SESSION['status_code'] = "danger";
        header("Location: tambaheval.php");
        exit();
    }

    if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
        $_SESSION['status'] = "Ukuran foto maksimal 2MB!";
        $_SESSION['status_code'] = "danger";
        header("Location: tambaheval.php");
        exit();
    }

    // 🔥 bersihkan kode mesin
    $kode_mesin_safe = preg_replace('/[^A-Za-z0-9\-]/', '_', $kode_mesin);

    // 🔥 nama file = kode_mesin_timestamp.ext
    $foto = $kode_mesin_safe . '_' . time() . '.' . $ext;

    move_uploaded_file(
        $_FILES['foto']['tmp_name'],
        "uploads/fishbone/" . $foto
    );
}

    // =============================
    // INSERT DATA
    // =============================
    $query = "INSERT INTO evaluasi 
    (tanggal, kode_mesin, kerusakan, man, methode, material, mesin, environment, countermeasure, status, foto)
    VALUES
    ('$tanggal','$kode_mesin','$kerusakan','$man','$methode','$material','$mesin','$environment','$countermeasure','$status','$foto')";

    if (mysqli_query($con, $query)) {
        $_SESSION['status'] = "Data fishbone berhasil disimpan";
        $_SESSION['status_code'] = "success";
    } else {
        $_SESSION['status'] = "Gagal menyimpan data";
        $_SESSION['status_code'] = "danger";
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
