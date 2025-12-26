<?php
session_start();
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');

?>
  <style>
    .big-spinner::-webkit-inner-spin-button {
      -webkit-appearance: inner-spin-button !important;
      transform: scale(1.5);   /* besarkan tombol panah */
    }

    .big-spinner {
      width: 100px;       /* kecilkan kotak input */
      font-size: 16px;    /* perbesar angka */
    }
    input[type=number]::-webkit-outer-spin-button,
input[type=number]::-webkit-inner-spin-button {
  -webkit-appearance: inner-spin-button !important;
  opacity: 1 !important;     /* pastikan kelihatan */
  display: block !important;
}
  </style>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Form Log Downtime</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Form Log Downtime</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  
  <section class="content">
    <div class="container-fluid">
      <?php if (isset($_SESSION['status'])): ?>
        <div class="alert alert-<?= ($_SESSION['status_code'] == "success") ? "success" : "danger"; ?> alert-dismissible fade show" role="alert">
          <?= $_SESSION['status']; ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <?php unset($_SESSION['status']); unset($_SESSION['status_code']); ?>
      <?php endif; ?>
      <div class="card card-primary">
        <div class="card-header">
          <h3 class="card-title">Input Log Downtime</h3>
        </div>
        <form action="code.php" method="POST" autocomplete="off">
          <div class="card-body">
            <div class="form-group">
              <label for="tanggal">Tanggal</label>
              <input type="date" name="tanggal" class="form-control" style="max-width: 200px;" required>
            </div>
            <div class="form-group">
              <label for="kode_mesin">Nomor Mesin</label>
              <input type="text" name="kode_mesin" id="kode_mesin" class="form-control" placeholder="Ketik nomor atau nama mesin" autocomplete="off" required>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nomor_wo">Nomor WO</label>
                    <input type="text" name="nomor_wo" class="form-control" placeholder="Masukkan Nomor WO" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="nik_prod">Amano Prod</label>
                    <input type="text" name="nik_prod" class="form-control" placeholder="Masukkan NIK Prod" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nik">Amano MTC</label>
                    <input type="text" name="nik_mekanik" id="nik_mekanik" class="form-control" placeholder="Masukkan NIK" required onkeyup="tampilkanNama()">
                </div>
                <div class="form-group col-md-6">
                    <label>Nama Mekanik</label>
                    <input type="text" id="nama" class="form-control" placeholder="Nama akan muncul otomatis" readonly>
                </div>
                </div>
            <div class="form-group">
              <label for="kriteria">Kriteria Kerusakan</label>
              <input type="text" name="kriteria" id="kriteria" class="form-control" placeholder="Ketik kriteria kerusakan" required />
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="kode_part1">Kode Part 1</label>
                <input type="text" name="kode_part1" class="form-control kode_part" placeholder="Masukkan Kode Part">
              </div>
              <div class="form-group col-md-3">
                <label>Jumlah Part 1</label>
                <input type="number" id="jumlah1" name="jumlah1" class="form-control big-spinner" placeholder="Jumlah Part" min="0" step="1" value="0">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="kode_part2">Kode Part 2</label>
                <input type="text" name="kode_part2" class="form-control kode_part" placeholder="Masukkan Kode Part">
              </div>
              <div class="form-group col-md-3">
                <label>Jumlah Part 2</label>
                <input type="number" id="jumlah2" name="jumlah2" class="form-control big-spinner" placeholder="Jumlah Part" min="0" step="1" value="0">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="kode_part3">Kode Part 3</label>
                <input type="text" name="kode_part3" class="form-control kode_part" placeholder="Masukkan Kode Part">
              </div>
              <div class="form-group col-md-3">
                <label>Jumlah Part 3</label>
                <input type="number" id="jumlah3" name="jumlah3" class="form-control big-spinner" placeholder="Jumlah Part" min="0" step="1" value="0">
              </div>
            </div>

            <div class="form-group">
              <label for="Action MTC/UTY">Action MTC/UTY</label>
              <input type="text" name="tindakan" id="tindakan" class="form-control" placeholder="Ketik Action Mekanik" required />
            </div>
            <input type="hidden" name="kode_downtime" id="kode_kodedowntime" />
            <div class="form-group">
                <label for="jam_mulai">Jam Mulai</label>
                <input type="text" name="jam_mulai" id="jam_mulai" class="form-control" placeholder="HH:MM" maxlength="5" required>
            </div>

            <div class="form-group">
                <label for="jam_selesai">Jam Selesai</label>
                <input type="text" name="jam_selesai" id="jam_selesai" class="form-control" placeholder="HH:MM" maxlength="5" required>
            </div>
            <div class="form-group">
              <label for="status">Status</label>
              <select name="status" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <option value="Major">Major</option>
                <option value="Minor">Minor</option>
              </select>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="downtime.php" class="btn btn-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </section>

  <script>
  function formatTimeInput(input) {
    let value = input.value.replace(/\D/g, ''); // Hapus semua karakter non-digit
    if (value.length >= 3) {
        input.value = value.substring(0,2) + ':' + value.substring(2,4);
    } else {
        input.value = value;
    }
}

// Tangani dua input sekaligus
document.getElementById('jam_mulai').addEventListener('input', function() {
    formatTimeInput(this);
});

document.getElementById('jam_selesai').addEventListener('input', function() {
    formatTimeInput(this);
});
    function tampilkanNama() {
    var nik = document.getElementById('nik_mekanik').value;

    if (nik.length >= 3) { // hanya kirim kalau panjangnya masuk akal
      var xhr = new XMLHttpRequest();
      xhr.open("GET", "get_nama.php?nik=" + encodeURIComponent(nik), true);
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          document.getElementById('nama').value = xhr.responseText;
        }
      };
      xhr.send();
    } else {
      document.getElementById('nama').value = '';
    }
  }
  </script>

  <link href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

  <script>
    $(function () {
      $('#kode_mesin').autocomplete({
        source: function (request, response) {
          $.ajax({
            url: 'search_mesin.php',
            type: 'GET',
            dataType: 'json',
            data: {
              term: request.term
            },
            success: function (data) {
              response(data);
            },
            error: function () {
              response([]);
            }
          });
        },
        minLength: 2,
        select: function (event, ui) {
          $('#kode_mesin').val(ui.item.value);
          return false;
        }
      });

      $(function () {
        
  $(document).ready(function () {
  $('input.kode_part').each(function () {
    $(this).autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "search_part.php",
          type: "GET",
          dataType: "json",
          data: {
            term: request.term
          },
          success: function (data) {
            response(data);
          },
          error: function () {
            response([]);
          }
        });
      },
      minLength: 2,
      select: function (event, ui) {
        $(this).val(ui.item.value); // hanya field aktif yang terisi
        return false;
      }
    });
  });
});


    
    

  // Autocomplete untuk Kriteria Kerusakan
  $('#kriteria').autocomplete({
    source: function (request, response) {
      $.ajax({
        url: 'search_kriteria.php',
        type: 'GET',
        dataType: 'json',
        data: {
          term: request.term
        },
        success: function (data) {
          response(data);
        },
        error: function () {
          response([]);
        }
      });
    },
    minLength: 1,
    select: function (event, ui) {
      $('#kriteria').val(ui.item.value);
      
      // Ambil kode_downtime berdasarkan kriteria yang dipilih
      $.ajax({
        url: 'get_kode_downtime.php',
        type: 'GET',
        dataType: 'json',
        data: {
          kriteria: ui.item.value
        },
        success: function (data) {
          if (data.kode_downtime) {
            $('#kode_kodedowntime').val(data.kode_downtime); // Simpan kode_downtime di input tersembunyi
          } else {
            $('#kode_kodedowntime').val(''); // Kosongkan jika tidak ditemukan
          }
        }
      });
      
      return false;
    }
  });
});

    });

     function validateTimeInput(inputId) {
    var timeInput = document.getElementById(inputId).value;
    var timePattern = /^([01]\d|2[0-3]):([0-5]\d)$/; // Format HH:MM
    if (!timePattern.test(timeInput)) {
      alert("Format waktu tidak valid. Harap masukkan waktu dalam format HH:MM.");
      document.getElementById(inputId).focus();
      return false;
    }
    return true;
  }
  // Tambahkan event listener pada form submit
  document.getElementById('logDowntimeForm').onsubmit = function() {
    return validateTimeInput('jam_mulai') && validateTimeInput('jam_selesai');
  };
  
  </script>
</div>

<?php
include('includes/footer.php');
?>
