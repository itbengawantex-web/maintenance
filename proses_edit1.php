<?php
include('config/dbcon.php');

$no_evaluasi     = $_POST['no_evaluasi'];
$tanggal         = $_POST['tanggal'];
$kode_mesin      = $_POST['kode_mesin'];
$kerusakan       = $_POST['kerusakan'];
$man             = $_POST['man'];
$methode         = $_POST['methode'];
$material        = $_POST['material'];
$mesin           = $_POST['mesin'];
$environment     = $_POST['environment'];
$countermeasure  = $_POST['countermeasure'];
$status          = $_POST['status'];
$halaman         = $_POST['halaman'] ?? 1;

/* =============================
   AMBIL FOTO LAMA
   ============================= */
$qOld = mysqli_query($con, "SELECT foto FROM evaluasi WHERE no_evaluasi='$no_evaluasi'");
$dataOld = mysqli_fetch_assoc($qOld);
$foto_lama = $dataOld['foto'] ?? null;

/* =============================
   HANDLE FOTO BARU
   ============================= */
$foto_sql = ""; // default tidak update foto

if (!empty($_FILES['foto']['name'])) {

    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png'];

    if (!in_array($ext, $allowed)) {
        header("Location: evaluasi.php?status=error_format&halaman=$halaman");
        exit();
    }

    if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
        header("Location: evaluasi.php?status=error_size&halaman=$halaman");
        exit();
    }

    // Bersihkan kode mesin
    $kode_mesin_safe = preg_replace('/[^A-Za-z0-9\-]/', '_', $kode_mesin);

    // Nama foto baru
    $foto_baru = $kode_mesin_safe . '_' . time() . '.' . $ext;

    // Upload
    move_uploaded_file(
        $_FILES['foto']['tmp_name'],
        "uploads/fishbone/" . $foto_baru
    );

    // Hapus foto lama
    if (!empty($foto_lama) && file_exists("uploads/fishbone/" . $foto_lama)) {
        unlink("uploads/fishbone/" . $foto_lama);
    }

    // set query update foto
    $foto_sql = ", foto='$foto_baru'";
}

/* =============================
   UPDATE DATA
   ============================= */
$query = "UPDATE evaluasi SET 
    tanggal='$tanggal',
    kode_mesin='$kode_mesin',
    kerusakan='$kerusakan',
    man='$man',
    methode='$methode',
    material='$material',
    mesin='$mesin',
    environment='$environment',
    countermeasure='$countermeasure',
    status='$status'
    $foto_sql
    WHERE no_evaluasi='$no_evaluasi'";

if (mysqli_query($con, $query)) {
    header("Location: evaluasi.php?status=berhasil&halaman=$halaman");
} else {
    echo "Error: " . mysqli_error($con);
}
?>
