<aside class="main-sidebar">
  <section class="sidebar">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Sidebar user panel without image -->
    <div class="user-panel" style="padding: 25px 20px; border-bottom: 1px solid #ff0000;">
      <div class="info" style="color: white; font-weight: bold;">
        <p style="margin: 0; text-align: center;"><?php echo $user['firstname'].' '.$user['lastname']; ?></p>
      </div>
    </div>

    <!-- Sidebar menu -->
    <ul class="sidebar-menu" data-widget="tree">
      
      <li class="header">INVENTORY</li>
      <li><a href="home.php"><i class="fas fa-warehouse nav-icon"></i> <span>&nbsp;Inventory Dashboard</span></a></li>
      <li><a href="inventory_selection.php"><i class="fas fa-warehouse nav-icon"></i> <span>&nbsp;Inventory Selection</span></a></li>
    <li><a href="projects.php"><i class="fas fa-project-diagram nav-icon"></i> <span>&nbsp;Projects</span></a></li>
      <li><a href="supplier_add.php"><i class="fas fa-store nav-icon"></i> <span>&nbsp;Supplier</span></a></li>
      <li><a href="product_unit.php"><i class="fas fa-balance-scale nav-icon"></i> <span>&nbsp;Add New Unit</span></a></li>
      <li><a href="product_category.php"><i class="fas fa-list nav-icon"></i> <span>&nbsp;Inventory Category</span></a></li>
      <li><a href="product_add.php"><i class="fas fa-briefcase nav-icon"></i> <span>&nbsp;Add New Material</span></a></li>
      <li><a href="specification.php"><i class="fas fa-list-alt nav-icon"></i> <span>&nbsp;Product Specifications</span></a></li>
      <li><a href="purchase_master.php"><i class="fas fa-cart-shopping nav-icon"></i> <span>Purchases </span></a></li>
      <li><a href="sales_master.php">&nbsp;<i class="fas fa-peso-sign nav-icon"></i> <span>&nbsp;Withdrawal </span></a></li>
      <li><a href="view_stock.php"><i class="fa-brands fa-stack-overflow nav-icon"></i> <span>&nbsp;Product Stock</span></a></li>
      <li><a href="shapeVisual.php"><i class="fas fa-chart-pie nav-icon"></i> <span>&nbsp;Shape Visualization</span></a></li>
      <li class="treeview" id="reports-treeview">
        <a href="javascript:void(0);">
          <i class="fa fa-area-chart nav-icon"></i>
          <span>Reports</span>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
          
        <ul class="treeview-menu">
          <li><a href="project_report.php"><i class="fa fa-circle-o nav-icon"></i> Project Report</a></li>
          <li><a href="purchase_report.php"><i class="fa fa-circle-o nav-icon"></i> Purchase Report</a></li>
          <li><a href="sales_report.php"><i class="fa fa-circle-o nav-icon"></i> Withdrawal Report</a></li>
          <li><a href="stock_report.php"><i class="fa fa-circle-o nav-icon"></i> Stock Report</a></li>
          <li><a href="material_report.php"><i class="fa fa-circle-o nav-icon"></i> Material Report</a></li>
       
        </ul>
      </li>
      <li class="header">CAR TRACK</li>
      <li class="treeview" id="cartrack-treeview">
        <a href="javascript:void(0);">
          <i class="fa fa-map-pin nav-icon"></i>
          <span>Cartrack</span>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
          <li><a href="https://fleetweb-ph.cartrack.com/map/fleet" class="nav-link nav-new_user tree-item" target="_blank" rel="noopener noreferrer"><i class="fa fa-car nav-icon"></i>&nbsp; Track Vehicles</a></li>
          <li><a href="track_list.php"><i class="fa fa-map-pin nav-icon"></i> Track Record List</a></li>
        </ul>
      </li>
      <li class="header">EQUIPMENT</li>
      <li><a href="equipmentDashboard.php"><i class="fas fa-tools nav-icon"></i> <span>Equipment Dashboard</span></a></li>

      <li class="treeview" id="transaction-treeview">
        <a href="javascript:void(0);">
          <i class="fa fa-refresh nav-icon"></i>
          <span>Transaction</span>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
          <li><a href="borrow.php"><i class="fa fa-arrow-right-long nav-icon"></i> Borrow</a></li>
          <li><a href="return.php"><i class="fa fa-arrow-left-long nav-icon"></i> Return</a></li>
        </ul>
      </li>

      <li class="treeview" id="equipment-treeview">
        <a href="javascript:void(0);">
          <i class="fa fa-wrench nav-icon"></i>
          <span>Equipments</span>
          <span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
          <li><a href="book.php"><i class="fa fa-rectangle-list nav-icon"></i> Equipment List</a></li>
          <li><a href="category.php"><i class="fa fa-layer-group nav-icon"></i> Category</a></li>
        </ul>
      </li>
    </ul>
  </section>
</aside>

<style>
  .main-sidebar, .sidebar-menu {
    background-color: #ea2020 !important;
  }
  
  .main-sidebar {
    z-index: 1000 !important;
  }

  .canvas-wrapper {
    z-index: 1 !important;
  }
  
  .sidebar-menu .treeview-menu {
    display: none;
  }

  .sidebar-menu .treeview.active > .treeview-menu,
  .sidebar-menu .treeview.menu-open > .treeview-menu {
    display: block !important;
  }

  .sidebar-menu .treeview > a > .pull-right-container {
    height: auto !important;
  }
  
  /* Make the caret icon rotate when menu is open */
  .sidebar-menu .treeview.menu-open > a > .pull-right-container > .fa-angle-left {
    transform: rotate(-90deg);
    transition: transform 0.2s;
  }

  .sidebar-menu .treeview > a > .pull-right-container > .fa-angle-left {
    transition: transform 0.2s;
  }
  
  .sidebar-menu li.header {
    background-color: #ea2020 !important;
    color: white !important;
    font-weight: bold;
    padding: 10px 15px;
    font-size: 12px;
  }
  
  .treeview-menu {
    background-color: #ea2020 !important;
    position: relative;
    z-index: 1001;
  }

  .nav-icon, .sidebar-menu > li > a {
    color: white !important;
    cursor: pointer;
  }

  .sidebar-menu .treeview-menu > li > a {
    color: white !important;
    padding-left: 25px;
    background-color: #ea2020 !important;
  }
  
  .sidebar-menu .treeview-menu {
    padding-left: 0;
    background-color: #ea2020 !important;
  }

  .user-panel {
    background-color: #ea2020 !important;
  }
  
  .user-panel .info p {
    color: white;
    font-weight: bold;
    margin: 0;
  }
  
  /* Make active items stand out */
  .sidebar-menu li.active > a {
    background-color: #cc0000 !important;
    border-left: 3px solid white !important;
  }
  
  /* Hover effect */
  .sidebar-menu li > a:hover {
    background-color: #cc0000 !important;
  }
</style>

<!-- jQuery and AdminLTE JavaScript dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Use the appropriate version based on your AdminLTE version -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.4.18/js/adminlte.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Wait for jQuery to be fully loaded
  if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded');
    return;
  }
  
  // Wait a short moment to ensure all other scripts have initialized
  setTimeout(function() {
    // First, remove any existing click handlers from treeview elements to prevent conflicts
    $('.sidebar-menu .treeview > a').off('click');
    
    // Add handlers for each treeview menu by ID
    $('#reports-treeview > a, #cartrack-treeview > a, #transaction-treeview > a, #equipment-treeview > a').on('click', function(e) {
      // Prevent the default anchor behavior (stops the # from being added to URL)
      e.preventDefault();
      e.stopPropagation();
      
      var parentLi = $(this).parent();
      
      // Toggle the menu-open class
      parentLi.toggleClass('menu-open');
      
      // Toggle the submenu visibility
      var submenu = parentLi.find('> .treeview-menu');
      if (parentLi.hasClass('menu-open')) {
        submenu.show();
      } else {
        submenu.hide();
      }
      
      // Return false to prevent other handlers and default behavior
      return false;
    });
    
    // Generic handler for any other treeview items (as a backup)
    $('.sidebar-menu .treeview > a').on('click', function(e) {
      // Only process if this isn't already handled by a specific ID handler
      if (!$(this).parent().attr('id')) {
        e.preventDefault();
        e.stopPropagation();
        
        var parentLi = $(this).parent();
        parentLi.toggleClass('menu-open');
        
        var submenu = parentLi.find('> .treeview-menu');
        if (parentLi.hasClass('menu-open')) {
          submenu.show();
        } else {
          submenu.hide();
        }
        
        return false;
      }
    });
    
    // Make sure any already active menus are visible
    $('.sidebar-menu .treeview.active, .sidebar-menu .treeview.menu-open').each(function() {
      $(this).addClass('menu-open').find('> .treeview-menu').show();
    });
    
    // Disable AdminLTE's default tree behavior if it's causing conflicts
    if ($.fn.tree) {
      $('.sidebar-menu').tree({
        animationSpeed: 500,
        accordion: true,
        followLink: false,
        trigger: '.treeview a'
      });
    }
  }, 500); // Wait 500ms to ensure everything else has loaded
});
$(document).ready(function() {
  // Toggle sidebar when the navbar toggle button is clicked
  $('.sidebar-toggle, [data-toggle="offcanvas"]').on('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // Toggle sidebar collapsed class
    $('body').toggleClass('sidebar-collapse');
    $('body').toggleClass('sidebar-open');
    
    // Save state in localStorage if needed
    localStorage.setItem('sidebar-collapsed', $('body').hasClass('sidebar-collapse'));
  });
  
  // Check if sidebar was collapsed previously
  if (localStorage.getItem('sidebar-collapsed') === 'true') {
    $('body').addClass('sidebar-collapse');
  }
});
</script>