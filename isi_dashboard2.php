<?php
include('includes/header.php');
include('config/dbcon.php'); // atau sesuaikan dengan file koneksimu

$today = date("Y-m-d"); // Hari ini
$last7 = date("Y-m-d", strtotime("-6 days")); // 7 hari ke belakang
?>
<STYle>
  .chart-container {
  height: 1550px !important;
  min-height: 550px;
}

.chart-container canvas {
  width: 100% !important;
  height: 100% !important;
}
.card .chart-container {
  height: 550px !important;
}

.card {
  min-height: 630px; /* biar total tinggi card sama */
}

</STYle>
<div class="page-break"></div>
<div id="export-content">
  <div class="row mt-4">
    
    <!-- Chart Pertama -->
    <div class="col-6">
      <div class="card card-warning card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title m-0">
            <i class="fas fa-exclamation-triangle"></i>
            Kerusakan Mesin
          </h3>
          <div class="form-inline">
            <select id="filter-jenis-mesin" class="form-control form-control-sm d-inline mr-1">
              <option value="">Semua Mesin</option>
              <?php
                $mesin_q = mysqli_query($con, "SELECT DISTINCT jenis_mesin FROM data_mesin");
                while ($m = mysqli_fetch_assoc($mesin_q)) {
                  echo '<option value="' . htmlspecialchars($m['jenis_mesin']) . '">' . htmlspecialchars($m['jenis_mesin']) . '</option>';
                }
              ?>
            </select>
            <input type="date" id="filter-tanggal-mulai" class="form-control form-control-sm d-inline mr-1" value="<?= $start ?>" style="width: 130px;" >
            <input type="date" id="filter-tanggal-akhir" class="form-control form-control-sm d-inline mr-1" value="<?= $end ?>" style="width: 130px;:">
            <button id="filter-btn" class="btn btn-primary">Filter</button>
          </div>
        </div>
        <div class="chart-container" >
          <canvas id="chart-kerusakan-mesin" ></canvas>
        </div>
      </div>
    </div>

    <!-- Chart Kedua -->
    <div class="col-md-6">
                <div class="card card-primary card-outline">
                <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                    <i class="far fa-chart-bar"></i>
                    Pekerjaan Teknisi
                        </h3>
                        <div>
                        <input type="date" id="start_date" name="start_date" value="<?= $start ?>" class="form-control form-control-sm d-inline" style="width: 130px;" >
                        <input type="date" id="end_date" name="end_date" value="<?= $end ?>" class="form-control form-control-sm d-inline" style="width: 130px;">
                        <button id="filterBtn" class="btn btn-sm btn-primary">Filter</button>
                        </div>
                </div>
                <div class="chart-container" >
                    <canvas id="bar-chart-mini2" ></canvas>
                </div>
                </div>
            </div>
        </div>
      </div>
  </div>


                    



<script>




$(document).ready(function () {
  // load default 7 hari terakhir dari latest_date di DB
  loadChartKerusakan();

  // kalau user klik tombol filter
  $('#filter-btn').on('click', () => {
    loadChartKerusakan(
      $('#filter-jenis-mesin').val(),
      $('#filter-tanggal-mulai').val(),
      $('#filter-tanggal-akhir').val()
    );
  });
});

// Fungsi format tanggal YYYY-MM-DD
function formatDate(date) {
  let d = new Date(date),
      month = '' + (d.getMonth() + 1),
      day = '' + d.getDate(),
      year = d.getFullYear();
  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;
  return [year, month, day].join('-');
}

$(document).ready(function () {
  // hitung hari ini dan 7 hari ke belakang
  let today = new Date();
  let last7 = new Date();
  last7.setDate(today.getDate() - 6);

  // set default ke input chart teknisi
  $('#start_date').val(formatDate(last7));
  $('#end_date').val(formatDate(today));

  // set default ke input chart kerusakan mesin juga (kalau mau)
  $('#filter-tanggal-mulai').val(formatDate(last7));
  $('#filter-tanggal-akhir').val(formatDate(today));

  // load awal chart dengan default tanggal
  updateChartData();
  loadChartTeknisi($('#start_date').val(), $('#end_date').val());
  loadChartKerusakan($('#filter-jenis-mesin').val(), $('#filter-tanggal-mulai').val(), $('#filter-tanggal-akhir').val());

  // event listener filter
  $('#chartFilter, #tanggalMulai, #tanggalAkhir').on('change', updateChartData);
  $('#filterBtn').on('click', () => loadChartTeknisi($('#start_date').val(), $('#end_date').val()));
  $('#filter-btn').on('click', () => loadChartKerusakan($('#filter-jenis-mesin').val(), $('#filter-tanggal-mulai').val(), $('#filter-tanggal-akhir').val()));
});
</script>