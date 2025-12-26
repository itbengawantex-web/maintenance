<?php
session_start();
include 'config/dbcon.php';

// Daftar user dan password (hardcoded)
$users = [
  'admin' => '123456',
  'user1' => 'abc123',
  'user2' => 'password2',
  'user3' => 'qwerty',
  'user4' => 'user456'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  // Cek apakah username dan password cocok
  if (isset($users[$username]) && $users[$username] === $password) {
    $_SESSION['user'] = $username; // simpan user aktif ke session
    header('Location: dashboard.php');
    exit();
  } else {
    $error = "Username atau Password salah!";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 3 | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <style>
  .login-page {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start; /* posisi mulai dari atas */
    padding-top: 60px; /* sesuaikan untuk naik/turun */
    background-color: #f4f6f9; /* warna latar belakang */
  }

  .login-box {
    width: 360px;
    margin: 0;
  }
</style>
</head>
<body class="hold-transition" style="background-color: #f4f6f9;">
  <div style="
    display: flex;
    justify-content: center;
    align-items: flex-start;
    height: 100vh;
    padding-top: 60px;
  ">
    <div class="login-box">
      <div class="login-logo">
        <img src="assets/dist/img/mtclogo.png" alt="Logo" style="height:50px;">
        <a href="#"><b style="color:#333;">System</b><span style="color:#666;">Downtime</span></a>
      </div>
      <!-- /.login-logo -->
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">Sign in to start your session</p>
<?php if (isset($error)): ?>
  <div class="alert alert-danger" role="alert">
    <?= $error ?>
  </div>
<?php endif; ?>

          <form action="login.php" method="post" autocomplete="off">
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Username" name="username" required>
              <div class="input-group-append">
                <div class="input-group-text">
                 <span class="fas fa-user"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" placeholder="Password" name="password" required>
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-8">
                <div class="icheck-primary">
                  <input type="checkbox" id="remember">
                  <label for="remember">Remember Me</label>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
              </div>
              <div class="col-12 text-center mt-2">
  <a href="index.php">Kembali</a>
</div>
            </div>
          </form>

        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/dist/js/adminlte.min.js"></script>
</body>
</html>
