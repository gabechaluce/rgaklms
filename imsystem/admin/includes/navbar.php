<header class="main-header">
  <!-- Logo -->
  <a href="#" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><b>RGA</b></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg">RGA Inventory </span>
  </a>
  <!-- Header Navbar -->
  <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button -->
<!-- This should be in your navbar -->
<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
  <span class="sr-only">Toggle navigation</span>
 
</a>
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">

        <!-- Project Workflow Button -->
        <?php
        // Determine current page to hide/show appropriate buttons
        $current_page = basename($_SERVER['PHP_SELF']);
        $current_dir = basename(dirname($_SERVER['PHP_SELF']));
        
        // Only show Project Workflow button when NOT in the project workflow section
        if($current_page != 'index.php' || $current_dir == 'executive'): 
        ?>
        <li class="nav-item">
          <a href="../../projectworkflow/index.php" class="btn btn-navbar">
          <i class="fas fa-tasks"></i>&nbsp;<b>Project Workflow</b>
          </a>
        </li>
        <?php endif; ?>
        
        <!-- KPI Button - Only for Executive (Role 1) -->
        <?php
        // Check if user is logged in and get their role
        // Assuming you have a session variable that stores the user's role/type
        // You may need to adjust this based on how you store the user's role in your system
        if(isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1): 
          // Only show KPI button when NOT already in KPI section AND user is Executive
          if($current_page != 'index.php' || $current_dir == 'executive'): 
        ?>
        <li class="nav-item">
         <a href="../../kpisystem/admin/home.php" class="btn btn-navbar">
            <i class="fas fa-chart-bar"></i>&nbsp;&nbsp;<b>KPI</b>
          </a>
        </li>
        <?php 
          endif;
        endif; 
        ?>
        
        <?php
        // Only show Inventory button when NOT already in the inventory section
        if($current_page != 'home.php'): 
        ?>
   
        <?php endif; ?>
        
        <li class="nav-item">
          <a href="../../projectworkflow/ajax.php?action=logout" class="btn btn-navbar">
          <i class="fas fa-sign-out-alt"></i> <b>Sign Out</b>
          </a>
        </li>

      </ul>
    </div>
  </nav>
</header>
<?php include 'includes/profile_modal.php'; ?>

<style>
  .main-header {
    background-color: #ea2020 !important;
  }
  
  .main-header .logo {
    background-color: #ea2020 !important;
    color: white !important;
    border-bottom: 1px solid #cc0000;
  }
  
  .main-header .navbar {
    background-color:#ea2020 !important;
  }
  
  .main-header .navbar .sidebar-toggle {
    color: white !important;
  }
  
  .main-header .navbar .sidebar-toggle:hover {
    background-color: #cc0000 !important;
  }
  
  .btn-navbar {
    background-color: transparent !important;
    color: white !important;
    border: 1px #ff0000 !important;
    margin-right: 10px;
    padding: 5px 10px;
    border-radius: 3px;
  }
  
  .btn-navbar:hover {
    background-color: white !important;
    color: #ff0000 !important;
  }
</style>