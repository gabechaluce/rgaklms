<?php
include('db_connect.php'); // Ensure database connection

?>
<style>
   
  .main-sidebar {
      background-color: #3D2217 !important;
      border-right: none !important; /* Removes any border on the right */
      box-shadow: none !important; /* Removes any shadow effect */
  }
  .brand-link {
      border-bottom: none !important; /* Removes bottom border under logo */
      background-color: #3D2217 !important;
  }
  .sidebar {
      border-right: none !important;
  }
  .nav-sidebar > .nav-item {
      border-bottom: none !important;
  }
  .nav-pills .nav-link.active,
.nav-pills .show>.nav-link {
    background-color: #f5ecde !important;
    color: #3D2217 !important; /* Adjust text color for contrast */
}
.nav-sidebar .nav-icon {
    color: #f5ecde !important; /* Default color when not selected */
}
.nav-sidebar .nav-link.active .nav-icon {
    color: #3D2217 !important; /* Color when selected */
}
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
    <a href="./" class="brand-link">
    <div class="text-center p-0 m-0">
        <img src="logo.jpg"
             alt="User Avatar"
             class="img-fluid img-thumbnail rounded-circle"
             style="height: 40px; width: 50px; object-fit: cover; border-radius: 50%;">
        <h3 class="text-center p-0 m-0">
            <b><?php echo $_SESSION['login_type'] == 1 ? $_SESSION['login_name'] : $_SESSION['login_name']; ?></b>
        </h3>
    </div>
</a>
<style>
    img.img-thumbnail {
        height: 50px;
        width: 50px;
        object-fit: cover;
        border-radius: 50%;
    }
   
</style>


<div>
    <div class="sidebar pb-4 mb-4">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
<!-- Home -->
<li class="nav-item dropdown">
    <a href="./" class="nav-link nav-home">
        <i class="nav-icon fas fa-home"></i>
        <p>
            Home
        </p>
    </a>
</li>
<!-- Home -->
<li class="nav-item dropdown">
    <a href="./" class="nav-link nav-home">
        <i class="nav-icon fas fa-home"></i>
        <p>
            Home
        </p>
    </a>
</li>
<!-- Calendar -->
<li class="nav-item dropdown">
    <a href="./index.php?page=calendar" class="nav-link nav-calendar">
        <i class="nav-icon fas fa-calendar"></i>
        <p>Calendar</p>
    </a>
</li>
                <!-- Inquiry section - Hidden for Designers (type = 4) -->
<?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 7 || ($_SESSION['login_type'] == 2 && $_SESSION['login_type'] != 4) || !in_array($_SESSION['login_type'], [3, 4, 8])) :?>
    <li class="nav-item">
        <a href="#" class="nav-link nav-inquiry_list">
            <i class="fas fa-tasks nav-icon">
                
            </i>
            <p>
                Inquiry
                <i class="right fas fa-angle-left"></i>
              
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="./index.php?page=new_inquiry" class="nav-link nav-new_inquiry tree-item">
                    <i class="fas fa-angle-right nav-icon"></i>
                    <p>Add New</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?page=inquiry_list" class="nav-link nav-inquiry_list tree-item">
                    <i class="fas fa-angle-right nav-icon"></i>
                    <p>Inquiry List</p>
                  
                </a>
            </li>
        </ul>
    </li>
<?php endif; ?>

<?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 7 ||$_SESSION['login_type'] == 5 || !in_array($_SESSION['login_type'], [3,8])) : ?>
    <li class="nav-item">
        <a href="#" class="nav-link nav-edit_project nav-project_details">
            <i class="nav-icon fas fa-layer-group"></i>
            <p>Projects <i class="right fas fa-angle-left"></i></p>
        
        </a>
        <ul class="nav nav-treeview">
            <?php if ($_SESSION['login_type'] != 7): ?>
                <li class="nav-item">
                    <a href="./index.php?page=new_project" class="nav-link nav-new_project tree-item">
                        <i class="fas fa-angle-right nav-icon"></i>
                        <p>Add New</p>
                    </a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a href="./index.php?page=project_list" class="nav-link nav-project_list tree-item">
                    <i class="fas fa-angle-right nav-icon"></i>
                    <p>List</p>
                   
                </a>
            </li>
        </ul>
    </li>
<?php endif; ?>

<!-- Task section - Hidden for Designers (type = 4) -->
<?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 7 || !in_array($_SESSION['login_type'], [2,4, 8])) :?>
    <?// Show for Admins, Project Managers, and other applicable users but NOT for Designers ?>
    <li class="nav-item">
        <a href="./index.php?page=task_list" class="nav-link nav-task_list">
            <i class="fas fa-tasks nav-icon"></i>
            <p>Task</p>
        </a>
    </li>
<?php endif; ?>

<!-- Report section - Hidden for Designers (type = 4) -->
<?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 7 || !in_array($_SESSION['login_type'], [2, 4, 8])) :?>
    <li class="nav-item">
        <a href="./index.php?page=reports" class="nav-link nav-reports">
            <i class="fas fa-th-list nav-icon"></i>
            <p>Report</p>
        </a>
    </li>
<?php endif; ?>

<!-- Users (Admin Only) -->
<?php if ($_SESSION['login_type'] == 1 ||$_SESSION['login_type'] == 10) :?>
    <li class="nav-item">
        <a href="#" class="nav-link nav-edit_user">
            <i class="nav-icon fas fa-users"></i>
            <p>
                Users
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="./index.php?page=new_user" class="nav-link nav-new_user tree-item">
                    <i class="fas fa-angle-right nav-icon"></i>
                    <p>Add New</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="./index.php?page=user_list" class="nav-link nav-user_list tree-item">
                    <i class="fas fa-angle-right nav-icon"></i>
                    <p>List</p>
                </a>
            </li>
        </ul>
    </li>
    <?php endif; ?>
    <?php if ($_SESSION['login_type'] == 1 ||$_SESSION['login_type'] == 10) :?>
        <li class="nav-item">
        <a href="#" class="nav-link nav-edit_user">
                <i class="nav-icon fa fa-map-pin"></i>
                <p>
                    Car Track
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="https://fleetweb-ph.cartrack.com/map/fleet" class="nav-link nav-new_user tree-item" target="_blank" rel="noopener noreferrer">
                        <i class="fas fa-angle-right nav-icon"></i>
                        <p>Track Vehicle</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./index.php?page=new_track" class="nav-link nav-new_user tree-item">
                        <i class="fas fa-angle-right nav-icon"></i>
                        <p>Add New Track</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="./index.php?page=track_list" class="nav-link nav-user_list tree-item">
                        <i class="fas fa-angle-right nav-icon"></i>
                        <p>Track Record List</p>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>
  <!-- Files should be accessible to Project Managers and Designers -->
<?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 7 || $_SESSION['login_type'] == 3 || $_SESSION['login_type'] == 2 || !in_array($_SESSION['login_type'], [5])) :?>
    <li class="nav-item dropdown">
        <a href="./index.php?page=files" class="nav-link nav-files">
            <i class="nav-icon fas fa-folder"></i>
            <p>Files</p>
        </a>
    </li>
<?php endif; ?>
 
            </ul>
        </nav>
    </div>
</aside>
<script>
    $(document).ready(function () {
        var page = '<?php echo isset($_GET["page"]) ? $_GET["page"] : "home" ?>';
        var s = '<?php echo isset($_GET["s"]) ? $_GET["s"] : "" ?>';
        if (s != "") page = page + "_" + s;

        if ($('.nav-link.nav-' + page).length > 0) {
            $('.nav-link.nav-' + page).addClass('active');
            if ($('.nav-link.nav-' + page).hasClass('tree-item')) {
                $('.nav-link.nav-' + page).closest('.nav-treeview').siblings('a').addClass('active');
                $('.nav-link.nav-' + page).closest('.nav-treeview').parent().addClass('menu-open');
            }
            if ($('.nav-link.nav-' + page).hasClass('nav-is-tree')) {
                $('.nav-link.nav-' + page).parent().addClass('menu-open');
            }
        }

        function updateInquiryCountPosition() {
            if ($('body').hasClass('sidebar-collapse')) {
                $('.count-text').hide();
                $('.count-icon').show();
            } else {
                $('.count-icon').hide();
                $('.count-text').show();
            }
        }

        // Handle sidebar toggle with pushmenu click
        $(document).on('click', '[data-widget="pushmenu"]', function () {
            setTimeout(updateInquiryCountPosition, 300); // Sync with sidebar animation
        });

        // Handle sidebar hover effect
        $('.main-sidebar').hover(
            function () { // Mouse enter
                if ($('body').hasClass('sidebar-collapse')) {
                    $('.count-icon').hide();
                    $(this).find('.count-text').fadeIn(200).css({
                    'display': 'inline-block',
                    'position': 'absolute',
                    'right': '100px',  // Position specific to #inqL
                    'top': '10px'
                }); // Move to the end of "Inquiry List"
                }
            },
            function () { // Mouse leave
                if ($('body').hasClass('sidebar-collapse')) {
                    $('.count-text').fadeOut(200, function () {
                        $('.count-icon').fadeIn(200);
                    });
                }
            }
        );

        // Initial state check
        updateInquiryCountPosition();
    });
</script>