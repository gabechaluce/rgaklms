<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-primary navbar-dark ">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <?php if(isset($_SESSION['login_id'])): ?>
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="" role="button"><i class="fas fa-bars"></i></a>
      </li>
    <?php endif; ?>
    </ul>

    <ul class="navbar-nav ml-auto">
       <!-- Buttons -->
       
    <?php
    // Determine current page to hide/show appropriate buttons
    $current_page = basename($_SERVER['PHP_SELF']);
    $current_dir = basename(dirname($_SERVER['PHP_SELF']));
    
    // Only show Project Workflow button when NOT in the main index.php
    // We check if we're not in the main index.php or if we're in the inventory section
    if($current_page != 'index.php' || $current_dir == 'admin' || $current_dir == 'libsystem'): 
    ?>
    <li class="nav-item">
      <a href="../index.php" class="btn btn-navbar">
        <b style="color: #3D2217">Project Workflow</b>
      </a>
    </li>&ensp;
    <?php endif; ?>

<?php 
// Check if user has permission to access Inventory (Admin = 1, Project Supervisor = 6, Inventory Coordinator = 4, Project Manager = 2)
// AND we're not currently in the inventory section
if(isset($_SESSION['login_type']) && 
   ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2 || $_SESSION['login_type'] == 3 || $_SESSION['login_type'] == 4|| $_SESSION['login_type'] == 5|| $_SESSION['login_type'] == 6 || $_SESSION['login_type'] == 7|| $_SESSION['login_type'] == 8|| $_SESSION['login_type'] == 9|| $_SESSION['login_type'] == 10) && 
   !($current_dir == 'admin' || $current_dir == 'libsystem')): 
?>
<li class="nav-item">
  <a href="../imsystem/admin/home.php" class="btn btn-navbar">
    <b style="color: #3D2217"><i class="fas fa-box-open"></i>&nbsp;Inventory</b>
  </a>
</li>
<?php endif; ?>

<?php 
// Check if user has permission to access KPI (Admin = 1)
// AND we're not currently in the KPI section
if(isset($_SESSION['login_type']) && 
   ($_SESSION['login_type'] == 1) && 
   !($current_dir == 'admin' || $current_dir == 'kpisystem')): 
?>
<li class="nav-item">
  <a href="../kpisystem/admin/home.php" class="btn btn-navbar">
    <b style="color: #3D2217"><i class="fas fa-chart-bar"></i>&nbsp;&nbsp;KPI</b>
  </a>
</li>
<?php endif; ?>

      <li class="nav-item">
        </a>
      </li>
     <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" aria-expanded="true" href="javascript:void(0)">
              <span>
                <div class="d-felx badge-pill">
                  <span class="fa fa-user mr-2"></span>
                  <span><b><?php echo ucwords($_SESSION['login_firstname']) ?></b></span>
                  <span class="fa fa-angle-down ml-2"></span>
                </div>
              </span>
            </a>
            <div class="dropdown-menu" aria-labelledby="account_settings" style="left: -2.5em;">
              <a class="dropdown-item" href="javascript:void(0)" id="manage_account"><i class="fa fa-cog"></i> Manage Account</a>
              <a class="dropdown-item" href="ajax.php?action=logout"><i class="fa fa-power-off"></i> Logout</a>
            </div>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  <script>
     $('#manage_account').click(function(){
        uni_modal('Manage Account','manage_user.php?id=<?php echo $_SESSION['login_id'] ?>')
      })
  </script>
    

    <!-- ALL THE CSS STYLE HERE -->
        <style>
      /* Sidebar menu background */
.sidebar {
    background-color: #3D2217 !important;
}

/* Default text and icon color */
.nav-sidebar .nav-link {
    color: #f5ecde !important;
}

/* Default icon color */
.nav-sidebar .nav-link i {
    color: #f5ecde !important;
}

/* Default arrow icon color */
.nav-sidebar .nav-link .fa-angle-left {
    color: #f5ecde !important;
}

/* Hover and active state */
.nav-sidebar .nav-link:hover, 
.nav-sidebar .nav-link.active {
    background-color: #f5ecde !important;
    color: #3D2217 !important;
}

/* Change icon and arrow color when selected */
.nav-sidebar .nav-link:hover i,
.nav-sidebar .nav-link.active i,
.nav-sidebar .nav-link:hover .fa-angle-left,
.nav-sidebar .nav-link.active .fa-angle-left {
    color: #3D2217 !important;
}

  .main-header {
    background-color: #f5ecde !important;
  }

  /* Change color of fas fa-bars */
  .nav-link i.fas.fa-bars  {
    color: #3D2217 !important;
  }
  .nav-link .fa-user,
.nav-link b, .dropdown-menu a{
    color: #3D2217 !important;
    /* Dropdown menu background */
.dropdown-menu {
    background-color: #f5ecde !important;
    border: none;
}

/* Dropdown menu items */
.dropdown-menu .dropdown-item {
    color: #3D2217 !important;
}

/* Dropdown menu icons */
.dropdown-menu .dropdown-item i {
    color: #3D2217 !important;
}

/* Change hover effect */
.dropdown-menu .dropdown-item:hover {
    background-color: #e4d7c5 !important;
}

}
/* Change dropdown arrow color */
.nav-link .fa-angle-down {
    color: #3D2217 !important;
}

/* Dropdown menu background */
.dropdown-menu {
    background-color: #3D2217 !important;
    border: none;
}

/* Dropdown text and icon color */
.dropdown-menu a {
    color: #f5ecde !important;
    display: flex;
    align-items: center;
}

/* Dropdown icons */
.dropdown-menu i {
    color: #f5ecde !important;
    margin-right: 8px;
}

/* Hover/Selected effect */
.dropdown-menu a:hover {
    background-color: #f5ecde !important;
    color: #3D2217 !important;
}

/* Change icon color on hover */
.dropdown-menu a:hover i {
    color: #3D2217 !important;
}


</style>