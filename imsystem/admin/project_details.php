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
            bh.date as expense_date,
            bh.full_name as customer_name
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
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="projects.php">Projects</a></li>
        <li class="active">Project Details</li>
      </ol>
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
                      <th>Customer Name</th>
                      <td><?php echo !empty($full_name) ? ucwords($full_name) : '<small><i>Not specified</i></small>'; ?></td>
                    </tr>
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
                      <th>Total Expenses</th>
                      <td class="purchase-summary-total">₱<?php echo number_format($total_expenses, 2); ?></td>
                    </tr>
                  </table>
                </div>
                <div class="col-md-4">
                  <table class="table table-bordered table-fit">
                    <tr>
                      <th width="40%">Description</th>
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
              <h3 class="box-title">Project Expenses</h3>
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
                      <th>Price</th>
                      <th>Total</th>
                      <th>Customer</th>
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
                          <td class="text-right">₱<?php echo number_format($row['price'], 2); ?></td>
                          <td class="text-right">₱<?php echo number_format($row['total_amount'], 2); ?></td>
                          <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr><td colspan="9" class="text-center">No expenses recorded for this project.</td></tr>
                    <?php endif; ?>
                  </tbody>
                  <tfoot>

                    <tr>
                      <th colspan="7" class="text-right">Overall Project Total:</th>
                      <th class="text-right purchase-summary-total">₱<?php echo number_format($total_expenses, 2); ?></th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
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
        title: 'Project Expenses - <?php echo $name; ?>',
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'print',
        text: '<i class="fa fa-print"></i> Print',
        title: 'Project Expenses - <?php echo $name; ?>',
        exportOptions: {
          columns: ':visible'
        }
      }
    ],
    "language": {
      "emptyTable": "No expenses recorded for this project",
      "info": "Showing _START_ to _END_ of _TOTAL_ expenses",
      "infoEmpty": "Showing 0 to 0 of 0 expenses",
      "infoFiltered": "(filtered from _MAX_ total expenses)",
      "search": "Search expenses:"
    }
  });

  // Initialize DataTable for materials table
  $('#materialsTable').DataTable({
    "responsive": true,
    "autoWidth": false,
    "pageLength": 10,
    "order": [[0, "desc"]],
    "language": {
      "emptyTable": "No materials recorded for this project",
      "info": "Showing _START_ to _END_ of _TOTAL_ materials",
      "infoEmpty": "Showing 0 to 0 of 0 materials",
      "infoFiltered": "(filtered from _MAX_ total materials)",
      "search": "Search materials:"
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
});
</script>
</body>
</html>