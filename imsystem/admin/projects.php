<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Project List</h1>
    </section>

    <section class="content">
      <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> Error!</h4>
                    <ul>';
            foreach ($_SESSION['error'] as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul></div>';
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
            unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">All Projects</h3>
            </div>
            <div class="box-body">
              <div class="col-lg-12">
                <div class="card card-outline card-success">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-condensed m-0" id="list">
                                <colgroup>
                                    <col width="5%">
                                    <col width="30%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="15%">
                                    <col width="20%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Project</th>
                                        <th>Date Started</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
                                    $where = "";
                                    
                                    // Corrected project visibility logic
                                    if ($_SESSION['login_type'] == 1) {
                                        // Admin sees all projects
                                        $where = "";
                                    } elseif ($_SESSION['login_type'] == 2) {
                                        // Coordinator sees projects where they're in user_ids
                                        $user_id = $conn->real_escape_string($_SESSION['login_id']);
                                        $where = " WHERE (
                                            FIND_IN_SET('$user_id', user_ids) > 0 OR 
                                            user_ids LIKE '$user_id,%' OR 
                                            user_ids LIKE '%,$user_id' OR 
                                            user_ids = '$user_id'
                                        )";
                                    } elseif ($_SESSION['login_type'] == 14) {
                                        // Manager sees projects where they're in manager_id or user_ids
                                        $user_id = $conn->real_escape_string($_SESSION['login_id']);
                                        $where = " WHERE (
                                            FIND_IN_SET('$user_id', manager_id) > 0 OR 
                                            manager_id LIKE '$user_id,%' OR 
                                            manager_id LIKE '%,$user_id' OR 
                                            manager_id = '$user_id' OR 
                                            FIND_IN_SET('$user_id', user_ids) > 0 OR 
                                            user_ids LIKE '$user_id,%' OR 
                                            user_ids LIKE '%,$user_id' OR 
                                            user_ids = '$user_id'
                                        )";
                                    } elseif ($_SESSION['login_type'] == 3) {
                                        // Employee sees projects where they're in user_ids
                                        $user_id = $conn->real_escape_string($_SESSION['login_id']);
                                        $where = " WHERE (
                                            FIND_IN_SET('$user_id', user_ids) > 0 OR 
                                            user_ids LIKE '$user_id,%' OR 
                                            user_ids LIKE '%,$user_id' OR 
                                            user_ids = '$user_id'
                                        )";
                                    }
                                    
                                    $qry = $conn->query("SELECT * FROM project_list $where ORDER BY date_created DESC, name ASC");

                                    while ($row = $qry->fetch_assoc()):
                                        // Improved description cleaning
                                        $desc = html_entity_decode($row['description']);
                                        $desc = strip_tags($desc);
                                        $desc = str_replace(["&nbsp;", "\r", "\n"], " ", $desc);
                                        $desc = preg_replace('/\s+/', ' ', $desc);
                                        $desc = trim($desc);
                                        
                                        // Progress calculation
                                        $tprog = $conn->query("SELECT * FROM task_list WHERE project_id = {$row['id']}")->num_rows;
                                        $cprog = $conn->query("SELECT * FROM task_list WHERE project_id = {$row['id']} AND status = 3")->num_rows;
                                        $prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
                                        $prog = $prog > 0 ? number_format($prog, 2) : $prog;
                                        $prod = $conn->query("SELECT * FROM user_productivity WHERE project_id = {$row['id']}")->num_rows;

                                        // Auto-update project status based on dates and progress
                                        if ($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])):
                                            $row['status'] = ($prod > 0 || $cprog > 0) ? 2 : 1;
                                        elseif ($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])):
                                            $row['status'] = 4;
                                        endif;
                                    ?>
                                        <tr>
                                            <th class="text-center"><?php echo $i++ ?></th>
                                            <td>
                                                <p><b><?php echo ucwords($row['name']) ?></b></p>
                                                <p class="truncate" title="<?php echo htmlspecialchars($desc) ?>"><?php echo $desc ?></p>
                                            </td>
                                            <td><b><?php echo date("M d, Y", strtotime($row['start_date'])) ?></b></td>
                                            <td><b><?php echo date("M d, Y", strtotime($row['end_date'])) ?></b></td>
                                            <td class="text-center">
                                                <?php
                                                // Status badge with improved conditional
                                                $status_class = [
                                                    0 => 'secondary', // Pending
                                                    1 => 'primary',   // Started
                                                    2 => 'info',      // On-Progress
                                                    3 => 'warning',   // On-Hold
                                                    4 => 'danger',    // Over Due
                                                    5 => 'success'    // Done
                                                ];
                                                echo "<span class='badge badge-{$status_class[$row['status']]}'>{$stat[$row['status']]}</span>";
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <!-- Added dedicated View button -->
                                                <a href="project_details.php?id=<?php echo $row['id'] ?>" class="btn btn-sm btn-info view-btn" title="View Project">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                
                                               
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
  
  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<style>
.floating-box {
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.floating-box:hover {
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);  
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: 15px;
  margin-top: 20px;
}

.table {
  border-collapse: collapse;
  margin: 0;
  padding: 0;
  border-radius: 15px;
  overflow: hidden;
}

.table th {
  background-color: #f8f9fa;
  text-align: center;
  padding: 15px 10px;
  font-weight: bold;
}

.table td {
  padding: 12px 10px;
  text-align: center;
  border-top: 1px solid #ddd;
}

.table tbody tr:hover {
  background-color: #f5f5f5;
  cursor: pointer;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn {
  border-radius: 8px;
  padding: 8px 16px;
  margin-right: 5px;
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.view-btn {
  margin-right: 5px;
}

table p {
    margin: unset !important;
}

table td {
    vertical-align: middle !important;
}

table p.truncate {
    max-width: 300px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    display: block;
}

/* Mobile responsiveness improvements */
@media (max-width: 768px) {
    table p.truncate {
        max-width: 200px; /* Shorter on mobile */
    }
    
    .table td, .table th {
        padding: 0.5rem 0.25rem; /* Reduced padding on mobile */
        font-size: 0.875rem; /* Slightly smaller text */
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .badge {
        font-size: 0.65em;
    }
    
    /* Stack buttons vertically on mobile */
    .view-btn {
        display: block;
        margin-bottom: 5px;
        width: 100%;
    }
    
    .dropdown-toggle {
        width: 100%;
    }
}

/* Improved badge colors */
.badge-secondary { background-color: #6c757d; }
.badge-primary { background-color: #007bff; }
.badge-info { background-color: #17a2b8; }
.badge-warning { background-color: #ffc107; color: #212529; }
.badge-danger { background-color: #dc3545; }
.badge-success { background-color: #28a745; }

body, .wrapper, .content-wrapper {
    background-color: #f4f1ed !important;
}
</style>

<script>
$(document).ready(function () {
    $('#list').dataTable({
        "responsive": true,
        "autoWidth": false,
        "scrollX": true, // Enable horizontal scroll in DataTable
        "language": {
            "emptyTable": "No projects found",
            "info": "Showing _START_ to _END_ of _TOTAL_ projects",
            "infoEmpty": "Showing 0 to 0 of 0 projects",
            "infoFiltered": "(filtered from _MAX_ total projects)",
            "search": "Search projects:"
        }
    });

    $('.delete_project').click(function () {
        _conf("Are you sure to delete this project?", "delete_project", [$(this).attr('data-id')]);
    });
    
    // Add tooltip for truncated text
    $('.truncate').tooltip();
    
    // Make entire row clickable to view project
    $('#list tbody tr').click(function(e) {
        // Don't trigger if clicking on a button or link
        if ($(e.target).is('button, a, input') || $(e.target).closest('button, a, input').length) {
            return;
        }
        
        var viewUrl = $(this).find('.view-btn').attr('href');
        if (viewUrl) {
            window.location.href = viewUrl;
        }
    });
});

function delete_project($id) {
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_project',
        method: 'POST',
        data: { id: $id },
        success: function (resp) {
            if (resp == 1) {
                alert_toast("Data successfully deleted", 'success');
                setTimeout(function () {
                    location.reload();
                }, 1500);
            } else {
                alert_toast("Error: " + resp, 'error');
                end_load();
            }
        },
        error: function(xhr) {
            alert_toast("Request failed: " + xhr.statusText, 'error');
            end_load();
        }
    });
}
</script>
</body>
</html>