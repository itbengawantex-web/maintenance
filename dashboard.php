<?php
session_start();

include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

// Koneksi ke database
include('config/dbcon.php'); // atau sesuaikan dengan file koneksimu
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
  .content-wrapper {
    background-image: url(''); /* ganti dengan URL gambar kamu */
    background-size: cover; /* agar gambar memenuhi seluruh area */
    background-repeat: no-repeat;
    background-position: center center;
  }
</style>


<div class="content-wrapper" style="
    background-image: url('');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Dashboard</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
      <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-danger" onclick="exportIsiDashboard()">Export ke PDF</button>
      </div>
    </div>
  </div>
  
   <section class="content">
    <div id="export-content">   
      <div id="isi-dashboard-export">
        <?php include 'isi_dashboard.php'; ?>
        <?php include 'isi_dashboard2.php'; ?>
      </div>
    </div>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>




// Fungsi export ke PDF
function exportIsiDashboard() {
  const element = document.getElementById('export-content');
  setTimeout(() => {
    const opt = {
      margin: 0.3,
      filename: 'isi_dashboard.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2, useCORS: true },
      jsPDF: { unit: 'in', format: 'a3', orientation: 'landscape' }
    };
    html2pdf().from(element).set(opt).save();
  }, 2000);
}

// Muat data chart saat dokumen siap
$(document).ready(function () {
  if (typeof updateChartData === 'function') updateChartData();
  if (typeof loadChartTeknisi === 'function') loadChartTeknisi();
});
</script>

<?php
include('includes/footer.php');
?>
