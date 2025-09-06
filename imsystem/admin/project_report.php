<?php
ob_start();
include 'includes/session.php';

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Initialize variables
$rows = [];
$start_date = $end_date = date('Y-m-d');
$project_name = '';
$inventory_selection = '';
$view_mode = false;
$total_expenses = 0;

// Process report generation
if (isset($_POST['generate']) || isset($_POST['view'])) {
    $period = $_POST['period'];
    $report_type = $_POST['report_type'] ?? '';
    $custom_date = $_POST['custom_date'] ?? null;
    $inventory_selection = $_POST['inventory_selection'] ?? '';
    $project_name = $_POST['project_name'] ?? '';
    $view_mode = isset($_POST['view']);

    // Validate and set date range
    if ($period === 'custom' && !empty($custom_date)) {
        if (DateTime::createFromFormat('Y-m-d', $custom_date) !== false) {
            $start_date = $end_date = $custom_date;
        } else {
            $_SESSION['error'] = 'Invalid custom date format';
            header('Location: project_report.php');
            exit();
        }
    } else {
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
            default:
                $start_date = $end_date = date('Y-m-d');
                break;
        }
    }

    // Build the main query with filters
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
            WHERE bh.date BETWEEN ? AND ?";
    
    $params = ['ss', $start_date, $end_date];
    
    // Add project name filter
    if (!empty($project_name)) {
        $sql .= " AND bh.project_name = ?";
        $params[0] .= 's';
        $params[] = $project_name;
    }
    
    // Add inventory selection filter
    if (!empty($inventory_selection)) {
        $sql .= " AND bd.inventory_selection = ?";
        $params[0] .= 's';
        $params[] = $inventory_selection;
    }
    
    $sql .= " ORDER BY bh.project_name, bh.date DESC";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Database error: ' . $conn->error;
        header('Location: project_report.php');
        exit();
    }

    // Calculate total expenses across all projects
    $total_sql = "SELECT SUM(bd.total) as total_expenses
                  FROM billing_details bd
                  LEFT JOIN billing_header bh ON bd.bill_id = bh.id
                  WHERE bh.date BETWEEN ? AND ?";
    
    $total_params = ['ss', $start_date, $end_date];
    
    // Add project name filter to total if specified
    if (!empty($project_name)) {
        $total_sql .= " AND bh.project_name = ?";
        $total_params[0] .= 's';
        $total_params[] = $project_name;
    }
    
    // Add inventory selection filter to total if specified
    if (!empty($inventory_selection)) {
        $total_sql .= " AND bd.inventory_selection = ?";
        $total_params[0] .= 's';
        $total_params[] = $inventory_selection;
    }
    
    $total_stmt = $conn->prepare($total_sql);
    if ($total_stmt) {
        $total_stmt->bind_param(...$total_params);
        $total_stmt->execute();
        $total_result = $total_stmt->get_result();
        $total_row = $total_result->fetch_assoc();
        $total_expenses = $total_row['total_expenses'] ?? 0;
        $total_stmt->close();
    }

    // Handle exports (only if not in view mode)
    if (!$view_mode && in_array($report_type, ['excel', 'word'])) {
        ob_end_clean();
        
        if ($report_type == 'excel') {
            // CSV Export
            $filename = "project_expense_report_".date('Ymd').".csv";
            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            $output = fopen('php://output', 'w');
            
            // Total expenses
            fputcsv($output, ['TOTAL EXPENSES', number_format($total_expenses, 2)]);
            fputcsv($output, []);
            
            // Project expense data
            fputcsv($output, ['=== PROJECT EXPENSE REPORT ===']);
            fputcsv($output, [
                'Project Name', 'Inventory', 'Product', 'Category', 
                'Quantity', 'Unit', 'Price', 'Total Amount', 'Date', 'Customer'
            ]);
            
            foreach ($rows as $row) {
                fputcsv($output, [
                    $row['project_name'],
                    $row['inventory_selection'],
                    $row['product_name'],
                    $row['product_company'],
                    $row['quantity'],
                    $row['unit'],
                    number_format($row['price'], 2),
                    number_format($row['total_amount'], 2),
                    date('M d, Y', strtotime($row['expense_date'])),
                    $row['customer_name']
                ]);
            }
            
            fclose($output);
            exit();
            
        } elseif ($report_type == 'word') {
            // HTML Table Export (for Word)
            $filename = "project_expense_report_".date('Ymd').".doc";
            header("Content-Type: application/vnd.ms-word");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            echo '<html><body>';
            echo '<h1>Project Expense Report</h1>';
            echo '<p><strong>Total Expenses: </strong> ₱' . number_format($total_expenses, 2) . '</p>';
            echo '<p>Period: '.date('M d, Y', strtotime($start_date)).' - '.date('M d, Y', strtotime($end_date)).'</p>';
            
            if (!empty($project_name)) {
                echo '<p><strong>Project: </strong>' . htmlspecialchars($project_name) . '</p>';
            }
            
            if (!empty($inventory_selection)) {
                echo '<p><strong>Inventory: </strong>' . htmlspecialchars($inventory_selection) . '</p>';
            }
            
            // Project expense table
            echo '<h2>Project Expenses</h2>';
            echo '<table border="1">';
            echo '<tr>
                    <th>Project Name</th>
                    <th>Inventory</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Total Amount</th>
                    <th>Date</th>
                    <th>Customer</th>
                  </tr>';
            
            foreach ($rows as $row) {
                echo '<tr>';
                echo '<td>'.$row['project_name'].'</td>';
                echo '<td>'.$row['inventory_selection'].'</td>';
                echo '<td>'.$row['product_name'].'</td>';
                echo '<td>'.$row['product_company'].'</td>';
                echo '<td>'.$row['quantity'].'</td>';
                echo '<td>'.$row['unit'].'</td>';
                echo '<td>'.number_format($row['price'], 2).'</td>';
                echo '<td>'.number_format($row['total_amount'], 2).'</td>';
                echo '<td>'.date('M d, Y', strtotime($row['expense_date'])).'</td>';
                echo '<td>'.$row['customer_name'].'</td>';
                echo '</tr>';
            }
            echo '</table></body></html>';
            exit();
        }
    }
}

// Calculate totals for the report
$total_items = count($rows);
$total_amount = 0;
$total_quantity = 0;

foreach ($rows as $row) {
    $total_amount += $row['total_amount'];
    $total_quantity += $row['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Project Expense Report</title>
  <link rel="icon" type="image/x-icon" href="rga.png">
  <?php include 'includes/header.php'; ?>
  <style>
    .report-section {
      margin-top: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 15px;
      background-color: #f9f9f9;
    }
    .report-header {
      background-color: #3c8dbc;
      color: white;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 3px;
    }
    .summary-box {
      background-color: #ecf0f5;
      border: 1px solid #bdc3c7;
      border-radius: 3px;
      padding: 10px;
      margin-bottom: 15px;
    }
    .total-expenses {
      background-color: #f39c12;
      color: white;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      text-align: center;
      font-size: 24px;
      font-weight: bold;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Project Expense Reports</h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>Reports</li>
        <li class="active">Project Expense Report</li>
      </ol>
    </section>

    <section class="content">
      <?php
        if(isset($_SESSION['error'])) {
          echo '<div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h4><i class="icon fa fa-warning"></i> Error!</h4>
                  '.$_SESSION['error'].'
                </div>';
          unset($_SESSION['error']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Generate Project Expense Report</h3>
            </div>
            <div class="box-body">
              <form method="post" action="">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Report Type:</label>
                      <select class="form-control" name="report_type">
                        <option value="excel" <?= ($_POST['report_type'] ?? '') === 'excel' ? 'selected' : '' ?>>Excel</option>
                        <option value="word" <?= ($_POST['report_type'] ?? '') === 'word' ? 'selected' : '' ?>>Word</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Period:</label>
                      <select class="form-control" name="period" id="period" required>
                        <option value="week" <?= ($_POST['period'] ?? '') === 'week' ? 'selected' : '' ?>>This Week</option>
                        <option value="month" <?= ($_POST['period'] ?? '') === 'month' ? 'selected' : '' ?>>This Month</option>
                        <option value="year" <?= ($_POST['period'] ?? '') === 'year' ? 'selected' : '' ?>>This Year</option>
                        <option value="custom" <?= ($_POST['period'] ?? '') === 'custom' ? 'selected' : '' ?>>Custom Date</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3" id="customDate" style="display:none;">
                    <div class="form-group">
                      <label>Custom Date:</label>
                      <input type="date" class="form-control" name="custom_date" 
                             value="<?= $_POST['custom_date'] ?? date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Project Name:</label>
                      <select class="form-control" name="project_name">
                        <option value="">All Projects</option>
                        <?php
                          $sql = "SELECT DISTINCT project_name FROM billing_header WHERE project_name IS NOT NULL AND project_name != '' ORDER BY project_name";
                          $query = $conn->query($sql);
                          while($row = $query->fetch_assoc()){
                            $selected = (isset($_POST['project_name']) && $_POST['project_name'] == $row['project_name']) ? 'selected' : '';
                            echo "<option value='".$row['project_name']."' $selected>".$row['project_name']."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Inventory Selection:</label>
                      <select class="form-control" name="inventory_selection">
                        <option value="">All Inventories</option>
                        <?php
                          $sql = "SELECT DISTINCT inventory_selection FROM inventory_selection ORDER BY inventory_selection";
                          $query = $conn->query($sql);
                          while($row = $query->fetch_assoc()){
                            $selected = (isset($_POST['inventory_selection']) && $_POST['inventory_selection'] == $row['inventory_selection']) ? 'selected' : '';
                            echo "<option value='".$row['inventory_selection']."' $selected>".$row['inventory_selection']."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>&nbsp;</label>
                      <div class="btn-group" style="width: 100%; display: flex;">
                        <button type="submit" class="btn btn-primary" name="generate" style="flex: 1; margin-right: 5px;">
                          <i class="fa fa-file"></i> Generate Report
                        </button>
                        <button type="submit" class="btn btn-success" name="view" style="flex: 1; margin-left: 5px;">
                          <i class="fa fa-eye"></i> View Report
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>

              <?php if ($view_mode): ?>
                <div class="report-section">
                  <div class="report-header">
                    <h3 class="box-title">Project Expense Report</h3>
                    <p><strong>Period:</strong> <?= date('F d, Y', strtotime($start_date)) ?> to <?= date('F d, Y', strtotime($end_date)) ?></p>
                    <?php if (!empty($project_name)): ?>
                      <p><strong>Project:</strong> <?= htmlspecialchars($project_name) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($inventory_selection)): ?>
                      <p><strong>Inventory:</strong> <?= htmlspecialchars($inventory_selection) ?></p>
                    <?php endif; ?>
                    <p><strong>Generated on:</strong> <?= date('F d, Y g:i A') ?> (Manila Time)</p>
                  </div>

                  <!-- Total Expenses Display -->
                  <div class="total-expenses">
                    Total Expenses: ₱<?= number_format($total_expenses, 2) ?>
                  </div>

                  <?php if (!empty($rows)): ?>
                    <!-- Summary Statistics -->
                    <div class="row" style="margin-bottom: 20px;">
                      <div class="col-md-3">
                        <div class="summary-box">
                          <span class="info-box-text">Total Items</span>
                          <span class="info-box-number"><?= $total_items ?></span>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="summary-box">
                          <span class="info-box-text">Total Quantity</span>
                          <span class="info-box-number"><?= number_format($total_quantity) ?></span>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="summary-box">
                          <span class="info-box-text">Report Total</span>
                          <span class="info-box-number">₱<?= number_format($total_amount, 2) ?></span>
                        </div>
                      </div>
                    </div>

                    <!-- Project Expense Table -->
                    <h3>Project Expenses</h3>
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Project Name</th>
                          <th>Inventory</th>
                          <th>Product</th>
                          <th>Category</th>
                          <th>Quantity</th>
                          <th>Unit</th>
                          <th>Price</th>
                          <th>Total Amount</th>
                          <th>Date</th>
                          <th>Customer</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($rows as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['project_name']) ?></td>
                            <td><?= htmlspecialchars($row['inventory_selection']) ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= htmlspecialchars($row['product_company']) ?></td>
                            <td class="text-center"><?= number_format($row['quantity'], 0) ?></td>
                            <td><?= htmlspecialchars($row['unit']) ?></td>
                            <td class="text-right">₱<?= number_format($row['price'], 2) ?></td>
                            <td class="text-right">₱<?= number_format($row['total_amount'], 2) ?></td>
                            <td><?= date('M d, Y', strtotime($row['expense_date'])) ?></td>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="7" class="text-right">Total:</th>
                          <th class="text-right">₱<?= number_format($total_amount, 2) ?></th>
                          <th colspan="2"></th>
                        </tr>
                      </tfoot>
                    </table>

                  <?php else: ?>
                    <p class="text-center text-muted">No project expense records found for the selected criteria</p>
                  <?php endif; ?>

                  <div class="text-center" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                    <small>
                      Report generated on <?= date('F d, Y g:i A') ?> 
                    </small>
                  </div>
                </div>
              <?php endif; ?>
            </div>

          </div>
        </div>
      </div>
    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(document).ready(function() {
  // Show/hide custom date input
  function toggleCustomDate() {
    $('#customDate').toggle($('#period').val() === 'custom');
  }
  $('#period').change(toggleCustomDate);
  toggleCustomDate(); // Initial check
  
  // Initialize DataTables for tables
  $('.table').DataTable({
    'paging': true,
    'lengthChange': true,
    'searching': true,
    'ordering': true,
    'info': true,
    'autoWidth': false,
    'pageLength': 25,
    'dom': '<"top"Bf>rt<"bottom"lip><"clear">',
    'buttons': [
      {
        extend: 'excel',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        title: 'Project Expense Report',
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'word',
        text: '<i class="fa fa-file-word-o"></i> Word',
        title: 'Project Expense Report',
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'print',
        text: '<i class="fa fa-print"></i> Print',
        title: 'Project Expense Report',
        exportOptions: {
          columns: ':visible'
        }
      }
    ]
  });
});
</script>
</body>
</html>
<?php ob_end_flush(); ?>