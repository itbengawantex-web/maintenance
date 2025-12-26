<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>FixFlow Engineering</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="assets/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<!-- Bootstrap 4 JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/plugins/flot/jquery.flot.js"></script>
<script src="assets/plugins/flot-old/jquery.flot.resize.min.js"></script>
<script src="plugins/chart.js/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<style>


body {
  background-image: url('assets/dist/img/lufy.jpg');
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
  background-attachment: fixed;
}

/* Ini yang paling penting supaya wrapper & konten tidak nutupin background */


</style>



<body class="hold-transition layout-top-nav" >
<div class="wrapper" >

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light" style="background-color: #212529;">

  <div class="container-fluid">
    <a href="#" class="navbar-brand d-flex align-items-center" style="padding-left: 180px;">
      <img src="assets/dist/img/mtclogo.png" alt="Logo" style="height:30px;">
      <span class="ml-2 font-weight-bold text-white" style="font-size: 1.5rem;">FixFlow Engineering</span>
    </a>

    <!-- Tombol login di kanan -->
    <div class="ml-auto d-flex align-items-center">
      <a href="login.php" class="btn btn-sm btn-primary" style="font-size: 1rem; padding: 6px 16px;">Login</a>
    </div>
  </div>
</nav>

  <!-- Content Wrapper -->


  <div class="content">
    <div class="container pt-4">
      <!-- Judul -->

   </div> <!-- end container -->
  </div> <!-- end content -->

      <!-- Konten dashboard langsung di bawah judul -->
      <div class="row">
  <div class="col-12 col-xl-10 offset-xl-1">
    <h3 class="mb-4" style="font-weight: bold;">Dashboard</h3>
    <?php include 'isi_dashboard.php'; ?>
  </div>
</div>

      <div class="row">
  <div class="col-12 col-xl-10 offset-xl-1">
    <?php include 'isi_dashboard2.php'; ?>
  </div>
</div>

<div class="row">
  <div class="col-12 col-xl-10 offset-xl-1">
    <?php include 'isi_report.php'; ?>
    <?php include 'isi_evaluasi.php'; ?>
  </div>
</div>
 
</DIV>


  <!-- Main Footer -->
  <footer class="main-footer" style="
    margin-left: 0px;">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong><?= date('Y') ?> PT. BENGAWANTEX — Thank you for visiting!</strong>
    
    <div class="float-right d-none d-sm-inline-block">
      <b>© AdminLTE Version</b> 3.0.5
    </div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../assets/dist/js/adminlte.min.js"></script>
</body>
</html>
