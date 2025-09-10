<?php
include 'includes/session.php';
include 'includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid project ID.");
}

$id = intval($_GET['id']);
$stat = array("Pending", "To be reviewed", "On-Progress", "On-Hold", "Over Due", "Done");

// Fetch project details
$qry = $conn->query("SELECT * FROM project_list WHERE id = $id");

if (!$qry || $qry->num_rows == 0) {
    die("Project not found.");
}

$project = $qry->fetch_assoc();
foreach ($project as $k => $v) {
    $$k = $v;
}

// Handle Add Overhead Cost
if (isset($_POST['add_overhead_cost'])) {
    $overhead_description = $conn->real_escape_string($_POST['overhead_description']);
    $overhead_price = floatval($_POST['overhead_price']);
    
    // Get the actual user's full name from the users table
    $user_id = $_SESSION['login_id'] ?? $_SESSION['user_id'] ?? $_SESSION['id'] ?? null; // Adjust based on your session variable name
    $added_by = 'Admin'; // Default fallback
    
    if ($user_id) {
        $user_query = $conn->query("SELECT firstname, lastname FROM users WHERE id = $user_id");
        if ($user_query && $user_query->num_rows > 0) {
            $user_data = $user_query->fetch_assoc();
            $added_by = trim($user_data['firstname'] . ' ' . $user_data['lastname']);
        }
    }
    
    if (!empty($overhead_description) && $overhead_price > 0) {
        $insert_sql = "INSERT INTO overhead_costs (project_name, description, price, added_by) 
                       VALUES ('$name', '$overhead_description', '$overhead_price', '$added_by')";
        
        if ($conn->query($insert_sql)) {
            $_SESSION['success'] = 'Overhead cost added successfully!';
        } else {
            $_SESSION['error'] = ['Failed to add overhead cost: ' . $conn->error];
        }
    } else {
        $_SESSION['error'] = ['Please fill in all required fields with valid values.'];
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle Edit Overhead Cost
if (isset($_POST['edit_overhead_cost'])) {
    $overhead_id = intval($_POST['overhead_id']);
    $overhead_description = $conn->real_escape_string($_POST['overhead_description']);
    $overhead_price = floatval($_POST['overhead_price']);
    
    if (!empty($overhead_description) && $overhead_price > 0) {
        $update_sql = "UPDATE overhead_costs SET 
                       description = '$overhead_description', 
                       price = '$overhead_price' 
                       WHERE id = $overhead_id AND project_name = '$name'";
        
        if ($conn->query($update_sql)) {
            $_SESSION['success'] = 'Overhead cost updated successfully!';
        } else {
            $_SESSION['error'] = ['Failed to update overhead cost: ' . $conn->error];
        }
    } else {
        $_SESSION['error'] = ['Please fill in all required fields with valid values.'];
    }
    
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Handle Delete Overhead Cost
if (isset($_POST['delete_overhead_cost'])) {
    $overhead_id = intval($_POST['overhead_id']);
    $delete_sql = "DELETE FROM overhead_costs WHERE id = $overhead_id AND project_name = '$name'";
    
    if ($conn->query($delete_sql)) {
        $_SESSION['success'] = 'Overhead cost deleted successfully!';
    } else {
        $_SESSION['error'] = ['Failed to delete overhead cost: ' . $conn->error];
    }
    
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Initialize variables for expenses section
$rows = [];
$start_date = isset($start_date) ? $start_date : date('Y-m-d');
$end_date = isset($end_date) ? $end_date : date('Y-m-d');
$period = 'today';
$inventory_selection = '';
$total_expenses = 0;

// Process expenses filtering
if (isset($_POST['filter_expenses'])) {
    $period = $_POST['period'];
    $inventory_selection = $_POST['inventory_selection'] ?? '';
    
    // Calculate date ranges
    switch ($period) {
        case 'week':
            $start_date = date('Y-m-d', strtotime('monday this week'));
            $end_date = date('Y-m-d', strtotime('sunday this week'));
            break;
        case 'month':
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
            break;
        case 'year':
            $start_date = date('Y-01-01');
            $end_date = date('Y-12-31');
            break;
        case 'custom':
            if (!empty($_POST['custom_date'])) {
                $custom_date = $_POST['custom_date'];
                if (DateTime::createFromFormat('Y-m-d', $custom_date) !== false) {
                    $start_date = $end_date = $custom_date;
                }
            }
            break;
        default:
            $start_date = $end_date = date('Y-m-d');
            break;
    }
}

// Always fetch expenses for this project (with optional filters)
$sql = "SELECT 
            bh.project_name,
            bd.inventory_selection,
            bd.product_name,
            bd.product_company,
            bd.qty as quantity,
            bd.product_unit as unit,
            bd.price,
            bd.total as total_amount,
            bh.date as expense_date
        FROM billing_details bd
        LEFT JOIN billing_header bh ON bd.bill_id = bh.id
        WHERE bh.project_name = '$name' 
        AND bh.date BETWEEN '$start_date' AND '$end_date'";
    
// Add inventory selection filter if specified
if (!empty($inventory_selection)) {
    $sql .= " AND bd.inventory_selection = '$inventory_selection'";
}

$sql .= " ORDER BY bh.date DESC";

$result = $conn->query($sql);
if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}

// Calculate total expenses for this project
$total_sql = "SELECT SUM(bd.total) as total_expenses
              FROM billing_details bd
              LEFT JOIN billing_header bh ON bd.bill_id = bh.id
              WHERE bh.project_name = '$name'";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_expenses = $total_row['total_expenses'] ?? 0;

// Fetch overhead costs for this project
$overhead_sql = "SELECT * FROM overhead_costs WHERE project_name = '$name' ORDER BY date_added DESC";
$overhead_result = $conn->query($overhead_sql);
$overhead_costs = [];
$total_overhead_costs = 0;

if ($overhead_result && $overhead_result->num_rows > 0) {
    while($row = $overhead_result->fetch_assoc()) {
        $overhead_costs[] = $row;
        $total_overhead_costs += $row['price'];
    }
}

// Calculate grand total (expenses + overhead costs)
$grand_total = $total_expenses + $total_overhead_costs;

// FIXED: Fetch project cost from project_list table instead of billing data
$project_cost = isset($project_cost) ? $project_cost : 0;

// Fetch materials used in the project
$materials_sql = "SELECT bd.product_name, bd.product_unit, bd.qty, bd.price, bd.total,
                 bd.inventory_selection, bd.product_company, bh.date as purchase_date
                 FROM billing_header bh
                 JOIN billing_details bd ON bh.id = bd.bill_id
                 WHERE bh.project_name = '$name'
                 ORDER BY bh.date DESC";
$materials_result = $conn->query($materials_sql);
$materials = [];
$total_materials_cost = 0;

if ($materials_result && $materials_result->num_rows > 0) {
    while($row = $materials_result->fetch_assoc()) {
        $materials[] = $row;
        $total_materials_cost += $row['total'];
    }
}
?>
<head>
    <link rel="icon" type="image/x-icon" href="rga.png">
    <title>Project Details - <?php echo ucwords($name); ?></title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Project Details</h1>
     
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
              <h3 class="box-title">Project Information</h3>
              <div class="pull-right">
                <a href="projects.php" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i> Back to Projects</a>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-4">
                  <table class="table table-bordered table-fit">
                    <tr>
                      <th width="40%">Project Name</th>
                      <td><?php echo ucwords($name); ?></td>
                    </tr>
                    <tr>

                    <tr>
                      <th>Start Date</th>
                      <td><?php echo date("F d, Y", strtotime($start_date)); ?></td>
                    </tr>
                    <tr>
                      <th>End Date</th>
                      <td><?php echo date("F d, Y", strtotime($end_date)); ?></td>
                    </tr>
                  </table>
                </div>
                <div class="col-md-4">
                  <table class="table table-bordered table-fit">
                    <tr>
                      <th width="40%">Location</th>
                      <td><?php echo !empty($location) ? ucwords($location) : '<small><i>Not specified</i></small>'; ?></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        <?php
                        if($stat[$status] =='Pending'){
                            echo "<span class='badge badge-secondary'>{$stat[$status]}</span>";
                        }elseif($stat[$status] =='To be reviewed'){
                            echo "<span class='badge badge-primary'>{$stat[$status]}</span>";
                        }elseif($stat[$status] =='On-Progress'){
                            echo "<span class='badge badge-info'>{$stat[$status]}</span>";
                        }elseif($stat[$status] =='On-Hold'){
                            echo "<span class='badge badge-warning'>{$stat[$status]}</span>";
                        }elseif($stat[$status] =='Over Due'){
                            echo "<span class='badge badge-danger'>{$stat[$status]}</span>";
                        }elseif($stat[$status] =='Done'){
                            echo "<span class='badge badge-success'>{$stat[$status]}</span>";
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Estimated Project Cost</th>
                      <td class="purchase-summary-total">₱<?php echo number_format($project_cost, 2); ?></td>
                    </tr>
                    <tr>
                      <?php if($user['type'] != 3): ?>
                      <th>Material Expenses</th>
                      <td class="purchase-summary-total">₱<?php echo number_format($total_expenses, 2); ?></td>
                      <?php endif; ?>
                    </tr>
                  </table>
                </div>
                <div class="col-md-4">
                  <table class="table table-bordered table-fit">
                    <tr>
                      <?php if($user['type'] != 3): ?>
                      <th width="40%">Overhead Costs</th>
                      <td class="purchase-summary-total">₱<?php echo number_format($total_overhead_costs, 2); ?></td>
                      <?php endif; ?>
                    </tr>
                    <tr>
                      <?php if($user['type'] != 3): ?>
                      <th>Total Project Cost</th>
                      <td class="purchase-summary-total text-primary" style="font-size: 18px; font-weight: bold;">₱<?php echo number_format($grand_total, 2); ?></td>
                      <?php endif; ?>
                    </tr>
                    <tr>
                      <th>Description</th>
                      <td>
                        <div id="desc-short" class="desc-short"><?php echo substr(strip_tags(html_entity_decode($description)), 0, 100); ?>...</div>
                        <div id="desc-full" class="desc-full" style="display: none;"><?php echo html_entity_decode($description); ?></div>
                        <button id="toggleDesc" class="btn btn-link p-0">See More</button>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Project Expenses Section -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Material Expenses</h3>
              <div class="pull-right">
                <button class="btn btn-sm btn-info" data-toggle="collapse" data-target="#expenseFilter">
                  <i class="fa fa-filter"></i> Filter Expenses
                </button>
              </div>
            </div>
            <div class="box-body">
              <!-- Expense Filter Form -->
              <div id="expenseFilter" class="collapse <?php echo isset($_POST['filter_expenses']) ? 'in' : ''; ?>">
                <form method="post" action="">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Period:</label>
                        <select class="form-control" name="period" id="period">
                          <option value="today" <?= $period === 'today' ? 'selected' : '' ?>>Today</option>
                          <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>This Week</option>
                          <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>This Month</option>
                          <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>This Year</option>
                          <option value="custom" <?= $period === 'custom' ? 'selected' : '' ?>>Custom Date</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3" id="customDate" style="display:<?php echo $period === 'custom' ? 'block' : 'none'; ?>;">
                      <div class="form-group">
                        <label>Custom Date:</label>
                        <input type="date" class="form-control" name="custom_date" 
                               value="<?= $_POST['custom_date'] ?? date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Inventory Selection:</label>
                        <select class="form-control" name="inventory_selection">
                          <option value="">All Inventories</option>
                          <?php
                            $sql = "SELECT DISTINCT inventory_selection FROM inventory_selection ORDER BY inventory_selection";
                            $query = $conn->query($sql);
                            while($row = $query->fetch_assoc()){
                              $selected = ($inventory_selection == $row['inventory_selection']) ? 'selected' : '';
                              echo "<option value='".$row['inventory_selection']."' $selected>".$row['inventory_selection']."</option>";
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block" name="filter_expenses">
                          <i class="fa fa-filter"></i> Apply Filters
                        </button>
                      </div>
                    </div>
                  </div>
                </form>
              </div>

              <div class="table-responsive">
                <table id="expensesTable" class="table table-bordered table-striped table-fit">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Inventory</th>
                      <th>Product</th>
                      <th>Category</th>
                      <th>Quantity</th>
                      <th>Unit</th>
                      <?php if($user['type'] != 3): ?>
                      <th>Price</th>
                      
                      <th>Total</th>
                           <?php endif; ?>
                      <th>Project Name</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if (!empty($rows)): ?>
    <?php 
    $filtered_total = 0;
    foreach($rows as $row): 
        $filtered_total += $row['total_amount'];
    ?>
        <tr>
            <td><?php echo date('M d, Y', strtotime($row['expense_date'])); ?></td>
            <td><?php echo htmlspecialchars($row['inventory_selection']); ?></td>
            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
            <td><?php echo htmlspecialchars($row['product_company']); ?></td>
            <td class="text-center"><?php echo number_format($row['quantity'], 0); ?></td>
            <td><?php echo htmlspecialchars($row['unit']); ?></td>
            <?php if($user['type'] != 3): ?>
            <td class="text-right">₱<?php echo number_format($row['price'], 2); ?></td>
       
            <td class="text-right">₱<?php echo number_format($row['total_amount'], 2); ?></td>
                 <?php endif; ?>
            <td><?php echo htmlspecialchars($row['project_name']); ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr><td colspan="<?php echo ($user['type'] != 3) ? '9' : '8'; ?>" class="text-center">No material expenses recorded for this project.</td></tr>
<?php endif; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <?php if($user['type'] != 3): ?>
                      <th colspan="7" class="text-right">Material Expenses Total:</th>
                      <th class="text-right purchase-summary-total">₱<?php echo number_format($total_expenses, 2); ?></th>
                    <?php endif; ?>
                      <th></th>
                      
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Overhead Costs Section -->
      <div class="row">
        <?php if($user['type'] != 3): ?>
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Overhead Costs</h3>
              <div class="pull-right">
                <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addOverheadModal">
                  <i class="fa fa-plus"></i> Add Cost
                </button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="overheadTable" class="table table-bordered table-striped table-fit">
                  <thead>
                    <tr>
                      <th>Date Added</th>
                      <th>Description</th>
                      <th>Price</th>
                      <th>Added By</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($overhead_costs)): ?>
                      <?php foreach($overhead_costs as $overhead): ?>
                        <tr>
                          <td><?php echo date('M d, Y', strtotime($overhead['date_added'])); ?></td>
                          <td><?php echo htmlspecialchars($overhead['description']); ?></td>
                          <td class="text-right">₱<?php echo number_format($overhead['price'], 2); ?></td>
                          <td><?php echo htmlspecialchars($overhead['added_by'] ?? 'N/A'); ?></td>
                          <td>
                            <button type="button" class="btn btn-info btn-xs" data-toggle="modal" 
                                    data-target="#editOverheadModal" 
                                    data-id="<?php echo $overhead['id']; ?>"
                                    data-description="<?php echo htmlspecialchars($overhead['description']); ?>"
                                    data-price="<?php echo $overhead['price']; ?>">
                              <i class="fa fa-edit"></i> Edit
                            </button>
                            <form method="post" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this overhead cost?');">
                              <input type="hidden" name="overhead_id" value="<?php echo $overhead['id']; ?>">
                              <button type="submit" name="delete_overhead_cost" class="btn btn-danger btn-xs">
                                <i class="fa fa-trash"></i> Delete
                              </button>
                            </form>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr><td colspan="5" class="text-center">No overhead costs recorded for this project.</td></tr>
                    <?php endif; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="2" class="text-right">Overhead Costs Total:</th>
                      <th class="text-right purchase-summary-total">₱<?php echo number_format($total_overhead_costs, 2); ?></th>
                      <th colspan="2"></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Project Summary -->
<!-- In Project Cost Summary section -->
<?php if($user['type'] != 3): ?>
<div class="row">
    <div class="col-xs-12">
        <div class="box floating-box">
            <div class="box-header with-border">
                <h3 class="box-title">Project Cost Summary</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3">
                        <table class="table table-bordered">
                            <tr>
                                <th>Estimated Project Cost</th>
                                <td class="text-right">₱<?php echo number_format($project_cost, 2); ?></td>
                            </tr>
                            <tr>
                                <th>Material Expenses</th>
                                <td class="text-right">₱<?php echo number_format($total_expenses, 2); ?></td>
                            </tr>
                            <tr>
                                <th>Overhead Costs</th>
                                <td class="text-right">₱<?php echo number_format($total_overhead_costs, 2); ?></td>
                            </tr>
                            <tr class="<?php echo ($grand_total > $project_cost) ? 'danger' : 'success'; ?>">
                                <th style="font-size: 16px;">Total Project Cost</th>
                                <td class="text-right purchase-summary-total" style="font-size: 18px; font-weight: bold;">₱<?php echo number_format($grand_total, 2); ?></td>
                            </tr>
                            <tr class="<?php echo ($grand_total > $project_cost) ? 'danger' : 'success'; ?>">
                                <th>Cost Variance</th>
                                <td class="text-right" style="font-weight: bold;">
                                    <?php 
                                    $variance = $project_cost - $grand_total;
                                    echo ($variance >= 0 ? '+' : '') . '₱' . number_format($variance, 2);
                                    echo ($variance >= 0) ? ' (Under Budget)' : ' (Over Budget)';
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
    </section>   
  </div>

  <!-- Add Overhead Cost Modal -->
  <div class="modal fade" id="addOverheadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="post" action="">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Add Overhead Cost</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label for="overhead_description">Description <span class="text-danger">*</span></label>
              <textarea class="form-control" id="overhead_description" name="overhead_description" rows="3" required 
                        placeholder="Enter description of the overhead cost..."></textarea>
            </div>
            <div class="form-group">
              <label for="overhead_price">Price <span class="text-danger">*</span></label>
              <input type="text" class="form-control price-input" id="overhead_price" name="overhead_price" required 
                     placeholder="0.00" pattern="[0-9]+(\.[0-9]{1,2})?">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" name="add_overhead_cost" class="btn btn-success">Add Cost</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Overhead Cost Modal -->
  <div class="modal fade" id="editOverheadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form method="post" action="">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Edit Overhead Cost</h4>
          </div>
          <div class="modal-body">
            <input type="hidden" id="edit_overhead_id" name="overhead_id">
            <div class="form-group">
              <label for="edit_overhead_description">Description <span class="text-danger">*</span></label>
              <textarea class="form-control" id="edit_overhead_description" name="overhead_description" rows="3" required 
                        placeholder="Enter description of the overhead cost..."></textarea>
            </div>
            <div class="form-group">
              <label for="edit_overhead_price">Price <span class="text-danger">*</span></label>
              <input type="text" class="form-control price-input" id="edit_overhead_price" name="overhead_price" required 
                     placeholder="0.00" pattern="[0-9]+(\.[0-9]{1,2})?">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" name="edit_overhead_cost" class="btn btn-primary">Update Cost</button>
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

.table-fit {
  width: 100%;
  border-collapse: collapse;
}

.table-fit th {
  background-color: #f8f9fa;
  text-align: left;
  padding: 15px 10px;
  font-weight: bold;
}

.table-fit td {
  padding: 12px 10px;
  text-align: left;
  border-top: 1px solid #ddd;
}

.table-fit tbody tr:hover {
  background-color: #f5f5f5;
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

.box-header.with-border {
  border-bottom: 1px solid #f4f4f4;
  padding: 15px 20px;
}

.box-title {
  font-weight: 600;
  font-size: 18px;
}

.purchase-summary-total {
  font-weight: 700;
  color: #00a65a;
  font-size: 16px;
}

.desc-short {
  display: inline;
}

.desc-full {
  display: none;
}

.modal-content {
  border-radius: 15px;
}

.text-primary {
  color: #337ab7 !important;
}

.danger {
  background-color: #f2dede !important;
}

.success {
  background-color: #dff0d8 !important;
}

/* Custom styles for price input - hide number input spinners */
.price-input {
  -moz-appearance: textfield;
}

.price-input::-webkit-outer-spin-button,
.price-input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.price-input[type=number] {
  -moz-appearance: textfield;
}
</style>

<script>
$(document).ready(function() {
  // Initialize DataTable for expenses table
  $('#expensesTable').DataTable({
    "responsive": true,
    "autoWidth": false,
    "pageLength": 10,
    "order": [[0, "desc"]],
    "dom": '<"top"Bf>rt<"bottom"lip><"clear">',
    "buttons": [
      {
        extend: 'excel',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        title: 'Material Expenses - <?php echo $name; ?>',
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'print',
        text: '<i class="fa fa-print"></i> Print',
        title: 'Material Expenses - <?php echo $name; ?>',
        exportOptions: {
          columns: ':visible'
        }
      }
    ],
    "language": {
      "emptyTable": "No material expenses recorded for this project",
      "info": "Showing _START_ to _END_ of _TOTAL_ expenses",
      "infoEmpty": "Showing 0 to 0 of 0 expenses",
      "infoFiltered": "(filtered from _MAX_ total expenses)",
      "search": "Search expenses:"
    }
  });

  // Initialize DataTable for overhead costs table
  $('#overheadTable').DataTable({
    "responsive": true,
    "autoWidth": false,
    "pageLength": 10,
    "order": [[0, "desc"]],
    "dom": '<"top"Bf>rt<"bottom"lip><"clear">',
    "buttons": [
      {
        extend: 'excel',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        title: 'Overhead Costs - <?php echo $name; ?>',
        exportOptions: {
          columns: [0, 1, 2, 3] // Exclude Actions column
        }
      },
      {
        extend: 'print',
        text: '<i class="fa fa-print"></i> Print',
        title: 'Overhead Costs - <?php echo $name; ?>',
        exportOptions: {
          columns: [0, 1, 2, 3] // Exclude Actions column
        }
      }
    ],
    "language": {
      "emptyTable": "No overhead costs recorded for this project",
      "info": "Showing _START_ to _END_ of _TOTAL_ costs",
      "infoEmpty": "Showing 0 to 0 of 0 costs",
      "infoFiltered": "(filtered from _MAX_ total costs)",
      "search": "Search overhead costs:"
    }
  });

  // Description toggle functionality
  $("#toggleDesc").click(function() {
    var shortDesc = $("#desc-short");
    var fullDesc = $("#desc-full");
    if (shortDesc.css("display") === "none") {
      shortDesc.css("display", "inline");
      fullDesc.css("display", "none");
      $(this).text("See More");
    } else {
      shortDesc.css("display", "none");
      fullDesc.css("display", "inline");
      $(this).text("See Less");
    }
  });

  // Show/hide custom date input
  function toggleCustomDate() {
    $('#customDate').toggle($('#period').val() === 'custom');
  }
  $('#period').change(toggleCustomDate);
  toggleCustomDate(); // Initial check

  // Price input validation and formatting
  $('.price-input').on('input', function() {
    let value = $(this).val();
    
    // Remove any non-numeric characters except decimal point
    value = value.replace(/[^0-9.]/g, '');
    
    // Ensure only one decimal point
    const parts = value.split('.');
    if (parts.length > 2) {
      value = parts[0] + '.' + parts.slice(1).join('');
    }
    
    // Limit to 2 decimal places
    if (parts[1] && parts[1].length > 2) {
      value = parts[0] + '.' + parts[1].substring(0, 2);
    }
    
    $(this).val(value);
  });

  // Prevent non-numeric input except decimal point
  $('.price-input').on('keypress', function(e) {
    const charCode = e.which ? e.which : e.keyCode;
    
    // Allow: backspace, delete, tab, escape, enter
    if ([8, 9, 27, 13, 46].indexOf(charCode) !== -1 ||
        // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
        (charCode === 65 && e.ctrlKey === true) ||
        (charCode === 67 && e.ctrlKey === true) ||
        (charCode === 86 && e.ctrlKey === true) ||
        (charCode === 88 && e.ctrlKey === true)) {
      return;
    }
    
    // Allow decimal point only if there isn't one already
    if (charCode === 46 && $(this).val().indexOf('.') === -1) {
      return;
    }
    
    // Ensure that it is a number and stop the keypress
    if ((charCode < 48 || charCode > 57)) {
      e.preventDefault();
    }
  });

  // Clear modal forms when modals are closed
  $('#addOverheadModal, #editOverheadModal').on('hidden.bs.modal', function() {
    $(this).find('form')[0].reset();
  });

  // Auto-focus on description field when modals open
  $('#addOverheadModal').on('shown.bs.modal', function() {
    $('#overhead_description').focus();
  });

  $('#editOverheadModal').on('shown.bs.modal', function() {
    $('#edit_overhead_description').focus();
  });

  // Handle edit modal data population
  $('#editOverheadModal').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var description = button.data('description');
    var price = button.data('price');
    
    var modal = $(this);
    modal.find('#edit_overhead_id').val(id);
    modal.find('#edit_overhead_description').val(description);
    modal.find('#edit_overhead_price').val(parseFloat(price).toFixed(2));
  });

  // Format price display on blur
  $('.price-input').on('blur', function() {
    let value = parseFloat($(this).val());
    if (!isNaN(value)) {
      $(this).val(value.toFixed(2));
    }
  });

  // Disable mouse wheel on price inputs
  $('.price-input').on('wheel', function(e) {
    e.preventDefault();
  });
});
</script>
</body>
</html>