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

            <div class="form-group">
              <label for="tanggal">Tanggal</label>
              <input type="date" name="tanggal" class="form-control" style="max-width: 200px;" required>
            </div>

            <div class="form-group">
              <label for="kode_mesin">Kode Mesin</label>
              <input type="text" name="kode_mesin" class="form-control" placeholder="Masukkan kode mesin" required>
            </div>

            <div class="form-group">
              <label for="kerusakan">Kerusakan</label>
              <textarea name="kerusakan" class="form-control" rows="2" placeholder="Deskripsi kerusakan"></textarea>
            </div>

            <div class="form-group">
              <label for="man">Man</label>
              <input type="text" name="man" class="form-control" placeholder="Masukkan faktor Man (Operator)">
            </div>

            <div class="form-group">
              <label for="methode">Methode</label>
              <input type="text" name="methode" class="form-control" placeholder="Masukkan metode perbaikan">
            </div>

            <div class="form-group">
              <label for="material">Material</label>
              <input type="text" name="material" class="form-control" placeholder="Masukkan material yang terlibat">
            </div>

            <div class="form-group">
              <label for="mesin">Mesin</label>
              <input type="text" name="mesin" class="form-control" placeholder="Masukkan mesin terkait">
            </div>

            <div class="form-group">
              <label for="environment">Environment</label>
              <input type="text" name="environment" class="form-control" placeholder="Masukkan kondisi lingkungan">
            </div>

            <div class="form-group">
              <label for="countermeasure">Countermeasure</label>
              <textarea name="countermeasure" class="form-control" rows="2" placeholder="Tindakan perbaikan"></textarea>
            </div>

            <div class="form-group">
              <label for="status">Status</label>
              <select name="status" class="form-control" required>
                <option value="">-- Pilih Status --</option>
                <option value="OK">OK</option>
                <option value="NOT OK">NOT OK</option>
              </select>
            </div>

          </div>

          <div class="card-footer">
            <button type="submit" class="btn btn-primary" name="aksi" value="evaluasi">Simpan</button>
            <a href="evaluasi.php" class="btn btn-secondary">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<?php
include('includes/footer.php');
?>
