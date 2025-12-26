<?php
session_start();
include('includes/header.php');
include('includes/topbar.php');
include('includes/sidebar.php');
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Form Input Fishbone</h1>
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
          <h3 class="card-title">Tambah Data Fishbone</h3>
        </div>

        <!-- Form -->
        <form action="code1.php" method="POST">
            <div class="card-body">

                <!-- Tanggal -->
                <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" style="max-width: 200px;" required>
                </div>

                <!-- Departement -->
                <div class="form-group">
                    <label for="departement">Departement</label>
                    <input type="text" name="departement" class="form-control" placeholder="Masukkan departement" required>
                </div>

                <!-- Nama Mesin -->
                <div class="form-group">
                    <label for="nama_mesin">Nama Mesin</label>
                    <select name="nama_mesin" class="form-control form-control-sm mr-2" style="width: 300px;" required>
                          <option value="">-- Pilih Mesin --</option>
                          <option value="EXT YM-1600">EXT YM-1600</option>
                          <option value="EXT YM-2100">EXT YM-2100</option>
                          <option value="EXT LOHIA">EXT LOHIA</option>
                      </select>
                </div>

                <!-- Waktu Stop Mesin -->
                <div class="form-group">
                    <label for="waktu_stop_mesin">Waktu Stop Mesin</label>
                    <input type="text" name="waktu_stop_mesin" id="waktu_stop_mesin" class="form-control" placeholder="HH:MM" maxlength="5" required>
                </div>

                <!-- Waktu Mulai PM/Perbaikan -->
                <div class="form-group">
                    <label for="waktu_mulai_pm">Waktu Mulai PM / Perbaikan</label>
                    <input type="text" name="waktu_mulai_pm" id="waktu_mulai_pm" class="form-control" placeholder="HH:MM" maxlength="5" required>
                </div>

                <!-- Waktu Selesai PM -->
                <div class="form-group">
                    <label for="waktu_selesai_pm">Waktu Selesai PM / Perbaikan</label>
                    <input type="text" name="waktu_selesai_pm" id="waktu_selesai_pm" class="form-control" placeholder="HH:MM" maxlength="5" required>
                </div>

                <!-- Waktu Start Up -->
                <div class="form-group">
                    <label for="waktu_start_up">Waktu Start Awal Mesin</label>
                    <input type="text" name="waktu_start_up" id="waktu_start_up" class="form-control" placeholder="HH:MM" maxlength="5" required>
                </div>

                <!-- Waktu Normal Run -->
                <div class="form-group">
                    <label for="waktu_normal_run">Waktu Mesin Normal</label>
                    <input type="text" name="waktu_normal_run" id="waktu_normal_run" class="form-control" placeholder="HH:MM" maxlength="5" required>
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Tambahkan catatan jika ada"></textarea>
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" name="aksi" value="kanban">Simpan</button>
                <a href="kanban.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>

      </div>
    </div>
  </section>
</div>


<script>
    function formatTimeInput(input) {
    let value = input.value.replace(/\D/g, ''); // Hapus semua karakter non-digit
    if (value.length >= 3) {
        input.value = value.substring(0,2) + ':' + value.substring(2,4);
    } else {
        input.value = value;
    }
}
document.getElementById('waktu_normal_run').addEventListener('input', function() {
    formatTimeInput(this);
});
document.getElementById('waktu_start_up').addEventListener('input', function() {
    formatTimeInput(this);
});
document.getElementById('waktu_selesai_pm').addEventListener('input', function() {
    formatTimeInput(this);
});
document.getElementById('waktu_mulai_pm').addEventListener('input', function() {
    formatTimeInput(this);
});
document.getElementById('waktu_stop_mesin').addEventListener('input', function() {
    formatTimeInput(this);
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

</script>
<?php
include('includes/footer.php');
?>
