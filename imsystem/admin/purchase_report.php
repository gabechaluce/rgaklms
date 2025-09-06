<?php 
ob_start();
include 'includes/session.php';

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Initialize variables
$rows = [];
$start_date = $end_date = date('Y-m-d');
$report_type = '';
$history_rows = [];
$view_mode = false;

// Process report generation
if (isset($_POST['generate']) || isset($_POST['view'])) {
    $period = $_POST['period'];
    $report_type = $_POST['report_type'] ?? '';
    $custom_date = $_POST['custom_date'] ?? null;
    $inventory_selection = $_POST['inventory_selection'] ?? '';
    $company_name = $_POST['company_name'] ?? '';
    $party_name = $_POST['party_name'] ?? '';
    $purchase_type = $_POST['purchase_type'] ?? '';
    $view_mode = isset($_POST['view']);

    // Validate and set date range
    if ($period === 'custom' && !empty($custom_date)) {
        if (DateTime::createFromFormat('Y-m-d', $custom_date) !== false) {
            $start_date = $end_date = $custom_date;
        } else {
            $_SESSION['error'] = 'Invalid custom date format';
            header('Location: purchase_report.php');
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
                pm.inventory_selection,
                pm.company_name,
                pm.product_name,
                pm.specification,
                pm.quantity as purchased_quantity,
                pm.unit,
                pm.price,
                pm.party_name,
                pm.purchase_date,
                pm.purchase_type,
                pi.businessname as distributor_full_name
            FROM purchase_master pm
            LEFT JOIN party_info pi ON pm.party_name = pi.businessname
            WHERE pm.purchase_date BETWEEN ? AND ?";
    
    $params = ['ss', $start_date, $end_date];
    
    // Add inventory selection filter
    if (!empty($inventory_selection)) {
        $sql .= " AND pm.inventory_selection = ?";
        $params[0] .= 's';
        $params[] = $inventory_selection;
    }
    
  
    
    $sql .= " ORDER BY pm.purchase_date DESC, pm.id DESC";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Database error: ' . $conn->error;
        header('Location: purchase_report.php');
        exit();
    }

    // Build the history query with the same filters
    $history_sql = "SELECT 
                        ph.product_name,
                        ph.company_name,
                        ph.inventory_selection,
                        ph.specification,
                        ph.quantity,
                        ph.unit,
                        ph.price,
                        ph.party_name,
                        ph.purchase_type,
                        ph.action_type,
                        ph.action_time,
                        CASE 
                            WHEN LAG(ph.price) OVER (PARTITION BY ph.product_name, ph.company_name ORDER BY ph.action_time) IS NULL THEN 'Initial'
                            WHEN ph.price > LAG(ph.price) OVER (PARTITION BY ph.product_name, ph.company_name ORDER BY ph.action_time) THEN 'Increased'
                            WHEN ph.price < LAG(ph.price) OVER (PARTITION BY ph.product_name, ph.company_name ORDER BY ph.action_time) THEN 'Decreased'
                            ELSE 'No Change'
                        END as price_change_status
                    FROM purchase_history ph
                    WHERE DATE(ph.action_time) BETWEEN ? AND ?";
    
    $history_params = ['ss', $start_date, $end_date];
    
    // Add the same filters to history query
    if (!empty($inventory_selection)) {
        $history_sql .= " AND ph.inventory_selection = ?";
        $history_params[0] .= 's';
        $history_params[] = $inventory_selection;
    }
    
    if (!empty($company_name)) {
        $history_sql .= " AND ph.company_name LIKE ?";
        $history_params[0] .= 's';
        $history_params[] = '%' . $company_name . '%';
    }
    
    if (!empty($party_name)) {
        $history_sql .= " AND ph.party_name LIKE ?";
        $history_params[0] .= 's';
        $history_params[] = '%' . $party_name . '%';
    }
    
    if (!empty($purchase_type)) {
        $history_sql .= " AND ph.purchase_type = ?";
        $history_params[0] .= 's';
        $history_params[] = $purchase_type;
    }
    
    $history_sql .= " ORDER BY ph.action_time DESC";
    
    $history_stmt = $conn->prepare($history_sql);
    if ($history_stmt) {
        $history_stmt->bind_param(...$history_params);
        $history_stmt->execute();
        $history_result = $history_stmt->get_result();
        $history_rows = $history_result->fetch_all(MYSQLI_ASSOC);
        $history_stmt->close();
    }

    // Handle exports (only if not in view mode)
    if (!$view_mode && in_array($report_type, ['excel', 'word'])) {
        ob_end_clean();
        
        if ($report_type == 'excel') {
            // CSV Export
            $filename = "purchase_report_".date('Ymd').".csv";
            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            $output = fopen('php://output', 'w');
            
            // Purchase history data
            fputcsv($output, []);
            fputcsv($output, ['=== PURCHASE HISTORY ===']);
            fputcsv($output, [
                'Date/Time', 'Inventory', 'Category', 'Product', 'Specification', 
                'Quantity', 'Unit', 'Price', 'Distributor', 'Type', 'Action', 'Price Change'
            ]);
            
            foreach ($history_rows as $history) {
                fputcsv($output, [
                    date('M d, Y H:i', strtotime($history['action_time'])),
                    $history['inventory_selection'],
                    $history['company_name'],
                    $history['product_name'],
                    $history['specification'] ?? 'N/A',
                    $history['quantity'],
                    $history['unit'],
                    number_format($history['price'], 2),
                    $history['party_name'],
                    $history['purchase_type'],
                    $history['action_type'],
                    $history['price_change_status']
                ]);
            }
            
            fclose($output);
            exit();
            
        } elseif ($report_type == 'word') {
            // HTML Table Export (for Word)
            $filename = "purchase_report_".date('Ymd').".doc";
            header("Content-Type: application/vnd.ms-word");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            echo '<html><body>';
            echo '<h1>Purchase Report</h1>';
            echo '<p>Period: '.date('M d, Y', strtotime($start_date)).' - '.date('M d, Y', strtotime($end_date)).'</p>';
            
            // Purchase history table
            echo '<h2>Purchase History</h2>';
            echo '<table border="1">';
            echo '<tr>
                    <th>Date/Time</th>
                    <th>Inventory</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Specification</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Distributor</th>
                    <th>Type</th>
                    <th>Action</th>
                    <th>Price Change</th>
                  </tr>';
            
            foreach ($history_rows as $history) {
                echo '<tr>';
                echo '<td>'.date('M d, Y H:i', strtotime($history['action_time'])).'</td>';
                echo '<td>'.$history['inventory_selection'].'</td>';
                echo '<td>'.$history['company_name'].'</td>';
                echo '<td>'.$history['product_name'].'</td>';
                echo '<td>'.($history['specification'] ?? 'N/A').'</td>';
                echo '<td>'.$history['quantity'].'</td>';
                echo '<td>'.$history['unit'].'</td>';
                echo '<td>'.number_format($history['price'], 2).'</td>';
                echo '<td>'.$history['party_name'].'</td>';
                echo '<td>'.$history['purchase_type'].'</td>';
                echo '<td>'.$history['action_type'].'</td>';
                echo '<td>'.$history['price_change_status'].'</td>';
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
    $total_amount += ($row['purchased_quantity'] * $row['price']);
    $total_quantity += $row['purchased_quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Purchase Report</title>
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
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Purchase Reports</h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>Purchases</li>
        <li class="active">Purchase Report</li>
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
              <h3 class="box-title">Generate Purchase Report</h3>
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
                </div>
                
                <div class="row">
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
                    <h3 class="box-title">Purchase Report</h3>
                    <p><strong>Period:</strong> <?= date('F d, Y', strtotime($start_date)) ?> to <?= date('F d, Y', strtotime($end_date)) ?></p>
                    <?php if (!empty($_POST['inventory_selection'])): ?>
                      <p><strong>Inventory:</strong> <?= htmlspecialchars($_POST['inventory_selection']) ?></p>
                    <?php endif; ?>
                    <p><strong>Generated on:</strong> <?= date('F d, Y g:i A') ?> (Manila Time)</p>
                  </div>

                  <?php if (!empty($rows)): ?>
                    <!-- Summary Statistics -->
                    <div class="row" style="margin-bottom: 20px;">
                      <div class="col-md-3">
                        <div class="summary-box">
                          <span class="info-box-text">Total Amount</span>
                          <span class="info-box-number">₱<?= number_format($total_amount, 2) ?></span>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="summary-box">
                          <span class="info-box-text">History Records</span>
                          <span class="info-box-number"><?= count($history_rows) ?></span>
                        </div>
                      </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
                      <li role="presentation" class="active">
                        <a href="#purchase-history" aria-controls="purchase-history" role="tab" data-toggle="tab">
                          <i class="fa fa-history"></i> Purchase History
                        </a>
                      </li>
                      <li role="presentation">
                        <a href="#category-summary" aria-controls="category-summary" role="tab" data-toggle="tab">
                          <i class="fa fa-bar-chart"></i> Category Summary
                        </a>
                      </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                      <!-- Purchase History Tab -->
                      <div role="tabpanel" class="tab-pane active" id="purchase-history">
                        <h3>Purchase History</h3>
                        <?php if (!empty($history_rows)): ?>
                          <table class="table table-bordered table-striped">
                            <thead>
                              <tr>
                                <th>Date/Time</th>
                                <th>Inventory</th>
                                <th>Category</th>
                                <th>Product</th>
                                <th>Specification</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Distributor</th>
                                <th>Type</th>
                                <th>Action</th>
                                <th>Price Change</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($history_rows as $history): ?>
                                <tr>
                                  <td><?= date('M d, Y H:i', strtotime($history['action_time'])) ?></td>
                                  <td><?= htmlspecialchars($history['inventory_selection']) ?></td>
                                  <td><?= htmlspecialchars($history['company_name']) ?></td>
                                  <td><?= htmlspecialchars($history['product_name']) ?></td>
                                  <td><?= htmlspecialchars($history['specification'] ?? 'N/A') ?></td>
                                  <td class="text-center"><?= number_format($history['quantity'], 0) ?></td>
                                  <td><?= htmlspecialchars($history['unit']) ?></td>
                                  <td class="text-right">₱<?= number_format($history['price'], 2) ?></td>
                                  <td><?= htmlspecialchars($history['party_name']) ?></td>
                                  <td><?= htmlspecialchars($history['purchase_type']) ?></td>
                                  <td>
                                    <span class="label label-<?= $history['action_type'] == 'create' ? 'success' : ($history['action_type'] == 'update' ? 'warning' : 'danger') ?>">
                                      <?= ucfirst($history['action_type']) ?>
                                    </span>
                                  </td>
                                  <td>
                                    <span class="label label-<?= $history['price_change_status'] == 'Increased' ? 'danger' : ($history['price_change_status'] == 'Decreased' ? 'success' : 'default') ?>">
                                      <?= $history['price_change_status'] ?>
                                    </span>
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        <?php else: ?>
                          <p class="text-center text-muted">No purchase history records found for the selected period</p>
                        <?php endif; ?>
                      </div>

                      <!-- Category Summary Tab -->
                      <div role="tabpanel" class="tab-pane" id="category-summary">
                        <h3>Purchase Summary by Category</h3>
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>Category</th>
                              <th>Total Items</th>
                              <th>Total Purchased Qty</th>
                              <th>Total Amount</th>
                             
                            </tr>
                          </thead>
                          <tbody>
                            <?php 
                            $category_summary = [];
                            foreach ($rows as $row) {
                              $cat = $row['company_name'];
                              if (!isset($category_summary[$cat])) {
                                $category_summary[$cat] = [
                                  'count' => 0,
                                  'quantity' => 0,
                                  'amount' => 0
                                ];
                              }
                              $category_summary[$cat]['count']++;
                              $category_summary[$cat]['quantity'] += $row['purchased_quantity'];
                              $category_summary[$cat]['amount'] += ($row['purchased_quantity'] * $row['price']);
                            }
                            
                            foreach ($category_summary as $category => $data): 
                              $avg_price = $data['quantity'] > 0 ? $data['amount'] / $data['quantity'] : 0;
                            ?>
                              <tr>
                                <td><?= htmlspecialchars($category) ?></td>
                                <td class="text-center"><?= $data['count'] ?></td>
                                <td class="text-center"><?= number_format($data['quantity']) ?></td>
                                <td class="text-right">₱<?= number_format($data['amount'], 2) ?></td>
                              
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>

                  <?php else: ?>
                    <p class="text-center text-muted">No purchase records found for the selected criteria</p>
                  <?php endif; ?>

                  <div class="text-center" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                    <small>
                      Report generated on <?= date('F d, Y g:i A') ?> 
                    </small>
                  </div>
                </div>
              <?php endif; ?>
            </div>
            <div class="box-footer">
              <a href="purchase_master.php" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Back to Purchases
              </a>
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
    'pageLength': 25
  });
});
</script>
</body>
</html>
<?php ob_end_flush(); ?>