<?php
$currentPage = basename($_SERVER['PHP_SELF']); 

// Tentukan apakah menu Data Master harus terbuka
$isDataMaster = in_array($currentPage, [
    'data_mesin.php',
    'data_downtime.php',
    'data_part.php'
]);
?>

<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="assets/dist/img/one.jpg" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">FixFlow Engineering</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="assets/dist/img/btxlogo.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">PT. BENGAWANTEX</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
            </li>
          
          <li class="nav-item has-treeview <?= $isDataMaster ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link <?= $isDataMaster ? 'active' : '' ?>">
                <i class="nav-icon fas fa-box"></i>
                <p>
                    Data Master
                    <i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                <a href="data_mesin.php" class="nav-link <?= $currentPage == 'data_mesin.php' ? 'active' : '' ?> pl-5">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Data Mesin</p>
                </a>
                </li>
                <li class="nav-item">
                <a href="data_downtime.php" class="nav-link <?= $currentPage == 'data_downtime.php' ? 'active' : '' ?> pl-5">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Data Downtime</p>
                </a>
                </li>
                <li class="nav-item">
                <a href="data_part.php" class="nav-link <?= $currentPage == 'data_part.php' ? 'active' : '' ?> pl-5">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Data Part Mesin</p>
                </a>
                </li>
            

                

              <!-- <li class="nav-item">
                <a href="pages/forms/editors.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Editors</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages/forms/validation.html" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Validation</p>
                </a>
              </li> -->
            </ul>
          </li>
          <li class="nav-item">
            <a href="downtime.php" class="nav-link">
              <i class="nav-icon far fa-file"></i>
              <p>
                Log Downtime
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pemakaian_part.php" class="nav-link">
              <i class="fas fa-cogs nav-icon"></i>
              <p>
                Pemakaian Sparepart 
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="evaluasi.php" class="nav-link">
              <i class="nav-icon fas fa-sitemap"></i>
              <p>
                Fishbone
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="kanban.php" class="nav-link">
              <i class="nav-icon fas fa-clipboard-list"></i>
              <p>
                Kanban Extruder
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="report.php" class="nav-link">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>
                Report
              </p>
            </a>
          </li>
          
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
