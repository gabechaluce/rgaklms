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
        // Handle payment status update
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_payment_status'])) {
            if (isset($_POST['project_id']) && isset($_POST['payment_status'])) {
                $project_id = $conn->real_escape_string($_POST['project_id']);
                $payment_status = $conn->real_escape_string($_POST['payment_status']);
                
                // Check if user has permission to update payment status
                if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2 || $_SESSION['login_type'] == 7) {
                    // Ensure payment_status column exists
                    $check_column = $conn->query("SHOW COLUMNS FROM project_list LIKE 'payment_status'");
                    if ($check_column->num_rows == 0) {
                        $conn->query("ALTER TABLE project_list ADD COLUMN payment_status ENUM('incomplete', 'down_payment', 'complete') DEFAULT 'incomplete'");
                    }
                    
                    $sql = "UPDATE project_list SET payment_status = '$payment_status' WHERE id = '$project_id'";
                    
                    if ($conn->query($sql)) {
                        $_SESSION['success'] = "Payment status updated successfully";
                    } else {
                        $_SESSION['error'][] = "Database error: " . $conn->error;
                    }
                } else {
                    $_SESSION['error'][] = "You do not have permission to update payment status";
                }
            } else {
                $_SESSION['error'][] = "Missing project ID or payment status";
            }
        }


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
                                    <col width="25%">
                                    <col width="10%">
                                    <col width="10%">
                                    <col width="10%">
                                    <col width="15%">
                                    <col width="25%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Project</th>
                                        <th>Date Started</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
                                    $payment_stat = array("incomplete" => "Incomplete", "down_payment" => "Down Payment", "complete" => "Complete");
                                    $payment_class = array("incomplete" => "danger", "down_payment" => "warning", "complete" => "success");
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
                                    
                                    // Check if payment_status column exists, if not add it
                                    $check_column = $conn->query("SHOW COLUMNS FROM project_list LIKE 'payment_status'");
                                    if ($check_column->num_rows == 0) {
                                        $conn->query("ALTER TABLE project_list ADD COLUMN payment_status ENUM('incomplete', 'down_payment', 'complete') DEFAULT 'incomplete'");
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
                                        
                                        // Check if payment_status exists, if not set default
                                        if (!isset($row['payment_status']) || empty($row['payment_status'])) {
                                            $row['payment_status'] = 'incomplete';
                                        }
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
                                                <span class="badge badge-<?php echo $payment_class[$row['payment_status']] ?>">
                                                    <?php echo $payment_stat[$row['payment_status']] ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <!-- Added dedicated View button -->
                                                <a href="project_details.php?id=<?php echo $row['id'] ?>" class="btn btn-sm btn-info view-btn" title="View Project">
                                                    <i class="fa fa-eye"></i> View
                                                </a>
                                                
                                                <!-- Payment Status Button - Only show for certain user types -->
                                                <?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2 || $_SESSION['login_type'] == 14): ?>
                                                <button type="button" class="btn btn-sm btn-warning edit-payment-btn" 
                                                        data-id="<?php echo $row['id'] ?>" 
                                                        data-status="<?php echo $row['payment_status'] ?>"
                                                        data-project-name="<?php echo htmlspecialchars($row['name']) ?>"
                                                        title="Edit Payment Status">
                                                    <i class="fa fa-credit-card"></i> Payment
                                                </button>
                                                <?php endif; ?>
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
  
  <!-- Payment Status Modal -->
  <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="paymentModalLabel">Update Payment Status</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="paymentForm" method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="project_id" id="project_id">
                    <input type="hidden" name="update_payment_status" value="1">
                    
                    <div class="form-group">
                        <label>Project:</label>
                        <p id="project_name_display" class="form-control-static"></p>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_status">Payment Status: <span class="text-danger">*</span></label>
                        <select class="form-control" id="payment_status" name="payment_status" required>
                            <option value="">-- Select Status --</option>
                            <option value="incomplete">Incomplete</option>
                            <option value="down_payment">Down Payment</option>
                            <option value="complete">Complete</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Close
                    </button>
                    <button type="submit" class="btn btn-primary" id="savePaymentBtn">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
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

.edit-payment-btn {
  margin-left: 5px;
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

/* Modal improvements */
.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.form-control-static {
    font-weight: bold;
    color: #333;
    background-color: #f8f9fa;
    padding: 6px 12px;
    border-radius: 4px;
}

.text-danger {
    color: #dc3545 !important;
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
    .view-btn, .edit-payment-btn {
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
        if ($(e.target).is('button, a, input, select') || $(e.target).closest('button, a, input, select').length) {
            return;
        }
        
        var viewUrl = $(this).find('.view-btn').attr('href');
        if (viewUrl) {
            window.location.href = viewUrl;
        }
    });
    
    // Payment status modal handling
    $('.edit-payment-btn').click(function(e) {
        e.stopPropagation(); // Prevent row click event
        var projectId = $(this).data('id');
        var currentStatus = $(this).data('status');
        var projectName = $(this).data('project-name');
        
        console.log('Opening modal for project:', projectId, currentStatus, projectName);
        
        $('#project_id').val(projectId);
        $('#payment_status').val(currentStatus);
        $('#project_name_display').text(projectName);
        
        $('#paymentModal').modal('show');
    });
    
    // Form validation and submission
    $('#paymentForm').submit(function(e) {
        var projectId = $('#project_id').val();
        var paymentStatus = $('#payment_status').val();
        
        if (!projectId || !paymentStatus) {
            e.preventDefault();
            alert('Please select a payment status.');
            return false;
        }
        
        // Disable submit button to prevent double submission
        $('#savePaymentBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        // Allow normal form submission
        return true;
    });
    
    // Reset form when modal is closed
    $('#paymentModal').on('hidden.bs.modal', function () {
        $('#paymentForm')[0].reset();
        $('#savePaymentBtn').prop('disabled', false).html('<i class="fa fa-save"></i> Save Changes');
    });
    
    // Debug: Log when modal is shown
    $('#paymentModal').on('shown.bs.modal', function () {
        console.log('Modal shown, project ID:', $('#project_id').val());
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