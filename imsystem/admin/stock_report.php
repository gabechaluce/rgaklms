<?php 
ob_start();
include 'includes/session.php';

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Initialize variables
$rows = [];
$inventory_type = 'all';
$report_type = '';
$stock_status = 'all';
$view_mode = false;

// Process report generation
if (isset($_POST['generate']) || isset($_POST['view'])) {
    $inventory_type = $_POST['inventory_type'];
    $report_type = $_POST['report_type'] ?? '';
    $stock_status = $_POST['stock_status'];
    $view_mode = isset($_POST['view']);
    
    // Start building the SQL query
    if ($inventory_type == 'all') {
        // For "All Inventory"
        if ($stock_status == 'all') {
            // All products
            $sql = "SELECT * FROM stock_master ORDER BY product_company, product_name";
        } 
        elseif ($stock_status == 'low') {
            // Low stock products
            $sql = "SELECT * FROM stock_master WHERE product_qty > 0 AND product_qty <= 5 ORDER BY product_company, product_name";
        } 
        elseif ($stock_status == 'out') {
            // Out of stock products
            $sql = "SELECT * FROM stock_master WHERE product_qty <= 0 ORDER BY product_company, product_name";
        }
        elseif ($stock_status == 'low_and_out') {
            // Low and Out of stock products
            $sql = "SELECT * FROM stock_master WHERE product_qty <= 5 ORDER BY product_company, product_name";
        }
    } 
    else {
        // For specific inventory type
        $escaped_type = $conn->real_escape_string($inventory_type);
        
        if ($stock_status == 'all') {
            // All products of this type
            $sql = "SELECT * FROM stock_master WHERE inventory_selection = '$escaped_type' ORDER BY product_company, product_name";
        } 
        elseif ($stock_status == 'low') {
            // Low stock products of this type
            $sql = "SELECT * FROM stock_master WHERE inventory_selection = '$escaped_type' AND product_qty > 0 AND product_qty <= 5 ORDER BY product_company, product_name";
        } 
        elseif ($stock_status == 'out') {
            // Out of stock products of this type
            $sql = "SELECT * FROM stock_master WHERE inventory_selection = '$escaped_type' AND product_qty <= 0 ORDER BY product_company, product_name";
        }
        elseif ($stock_status == 'low_and_out') {
            $sql = "SELECT * FROM stock_master WHERE inventory_selection = '$escaped_type' AND product_qty <= 5 ORDER BY product_company, product_name";
        }
    }
    
    // Execute query
    $query = $conn->query($sql);
    if (!$query) {
        $_SESSION['error'] = "Database error: " . $conn->error;
    } else {
        $rows = $query->fetch_all(MYSQLI_ASSOC);
    }
    
    // Handle exports (only if not in view mode)
    if (!$view_mode && in_array($report_type, ['excel', 'word'])) {
        ob_end_clean();
        
        if ($report_type == 'excel') {
            // CSV Export
            $filename = "stock_report_".date('Ymd').".csv";
            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Category', 'Inventory Type', 'Product', 'Unit', 
                'Price', 'Available Quantity', 'Stock Status'
            ]);
            
            foreach ($rows as $row) {
                $quantity = intval($row['product_qty']);
                $status = '';
                
                if ($quantity <= 0) {
                    $status = 'Out of Stock';
                } elseif ($quantity <= 5) {
                    $status = 'Low Stock';
                } else {
                    $status = 'In Stock';
                }
                
                fputcsv($output, [
                    $row['product_company'],
                    $row['inventory_selection'],
                    $row['product_name'],
                    $row['product_unit'],
                    number_format($row['product_selling_price'], 2),
                    $quantity,
                    $status
                ]);
            }
            fclose($output);
            exit();
            
        } elseif ($report_type == 'word') {
            // HTML Table Export (for Word)
            $filename = "stock_report_".date('Ymd').".doc";
            header("Content-Type: application/vnd.ms-word");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            echo '<html><body>';
            echo '<h1>Stock Report</h1>';
            echo '<p>Generated on: '.date('F d, Y').'</p>';
            
            // Add inventory type filter info if used
            if ($inventory_type != 'all') {
                echo '<p>Filtered by: '.$inventory_type.'</p>';
            }
            
            // Add stock status filter info if used
            if ($stock_status != 'all') {
                if ($stock_status == 'low_and_out') {
                    echo '<p>Status filter: Low Stock and Out of Stock</p>';
                } else {
                    echo '<p>Status filter: '.($stock_status == 'low' ? 'Low Stock' : 'Out of Stock').'</p>';
                }
            }
            
            echo '<table border="1">';
            echo '<tr>
                    <th>Category</th>
                    <th>Inventory Type</th>
                    <th>Product</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Available Quantity</th>
                    <th>Stock Status</th>
                  </tr>';
            
            foreach ($rows as $row) {
                $quantity = intval($row['product_qty']);
                $status = '';
                
                if ($quantity <= 0) {
                    $status = 'Out of Stock';
                } elseif ($quantity <= 5) {
                    $status = 'Low Stock';
                } else {
                    $status = 'In Stock';
                }
                
                echo '<tr>';
                echo '<td>'.$row['product_company'].'</td>';
                echo '<td>'.$row['inventory_selection'].'</td>';
                echo '<td>'.$row['product_name'].'</td>';
                echo '<td>'.$row['product_unit'].'</td>';
                echo '<td>'.number_format($row['product_selling_price'], 2).'</td>';
                echo '<td>'.$quantity.'</td>';
                echo '<td>'.$status.'</td>';
                echo '</tr>';
            }
            echo '</table></body></html>';
            exit();
        }
    }
}

// Get distinct inventory types for dropdown
$distinct_inventory_types = [];
$query = $conn->query("SELECT DISTINCT inventory_selection FROM stock_master WHERE inventory_selection != '' ORDER BY inventory_selection");
if ($query) {
    while ($row = $query->fetch_assoc()) {
        $distinct_inventory_types[] = $row['inventory_selection'];
    }
}

// Calculate totals for the report
$total_items = count($rows);
$total_value = 0;
$out_of_stock_count = 0;
$low_stock_count = 0;

foreach ($rows as $row) {
    $total_value += ($row['product_qty'] * $row['product_selling_price']);
    $quantity = intval($row['product_qty']);
    
    if ($quantity <= 0) {
        $out_of_stock_count++;
    } elseif ($quantity <= 5) {
        $low_stock_count++;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Stock Report</title>
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
      <h1>Stock Reports</h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>Inventory</li>
        <li class="active">Stock Report</li>
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
              <h3 class="box-title">Generate Stock Report</h3>
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
                      <label>Inventory Type:</label>
                      <select class="form-control" name="inventory_type" required>
                        <option value="all" <?= ($inventory_type === 'all') ? 'selected' : '' ?>>All Inventory</option>
                        <?php foreach ($distinct_inventory_types as $type): ?>
                          <option value="<?= htmlspecialchars($type) ?>" <?= ($inventory_type === $type) ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Stock Status:</label>
                      <select class="form-control" name="stock_status" required>
                        <option value="all" <?= ($stock_status === 'all') ? 'selected' : '' ?>>All Products</option>
                        <option value="low_and_out" <?= ($stock_status === 'low_and_out') ? 'selected' : '' ?>>Low and Out of Stock</option>
                        <option value="low" <?= ($stock_status === 'low') ? 'selected' : '' ?>>Low Stock Only</option>
                        <option value="out" <?= ($stock_status === 'out') ? 'selected' : '' ?>>Out of Stock Only</option>
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
                    <h3 class="box-title">Stock Report</h3>
                    <p><strong>Inventory Type:</strong> <?= $inventory_type == 'all' ? 'All Inventory' : htmlspecialchars($inventory_type) ?></p>
                    <p><strong>Stock Status:</strong> 
                      <?php 
                      switch($stock_status) {
                        case 'all': echo 'All Products'; break;
                        case 'low_and_out': echo 'Low and Out of Stock'; break;
                        case 'low': echo 'Low Stock Only'; break;
                        case 'out': echo 'Out of Stock Only'; break;
                      }
                      ?>
                    </p>
                    <p><strong>Generated on:</strong> <?= date('F d, Y g:i A') ?> (Manila Time)</p>
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
                          <span class="info-box-text">Total Stock Value</span>
                          <span class="info-box-number">₱<?= number_format($total_value, 2) ?></span>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="summary-box">
                          <span class="info-box-text">Low Stock Items</span>
                          <span class="info-box-number text-warning"><?= $low_stock_count ?></span>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="summary-box">
                          <span class="info-box-text">Out of Stock Items</span>
                          <span class="info-box-number text-danger"><?= $out_of_stock_count ?></span>
                        </div>
                      </div>
                    </div>

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 20px;">
                      <li role="presentation" class="active">
                        <a href="#stock-details" aria-controls="stock-details" role="tab" data-toggle="tab">
                          <i class="fa fa-list"></i> Stock Details
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
                      <!-- Stock Details Tab -->
                      <div role="tabpanel" class="tab-pane active" id="stock-details">
                        <h3>Stock Details</h3>
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>Category</th>
                              <th>Inventory Type</th>
                              <th>Product</th>
                              <th>Unit</th>
                              <th>Price</th>
                              <th>Available Quantity</th>
                              <th>Stock Status</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php foreach ($rows as $row): 
                              $quantity = number_format($row['product_qty'] ?? 0, 0);
                              $status = '';
                              $statusClass = '';
                              
                              if ($row['product_qty'] <= 0) {
                                  $status = 'Out of Stock';
                                  $statusClass = 'text-danger';
                              } elseif ($row['product_qty'] <= 5) {
                                  $status = 'Low Stock';
                                  $statusClass = 'text-warning';
                              } else {
                                  $status = 'In Stock';
                                  $statusClass = 'text-success';
                              }
                            ?>
                              <tr>
                                <td><?= htmlspecialchars($row['product_company'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['inventory_selection'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['product_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($row['product_unit'] ?? 'N/A') ?></td>
                                <td class="text-right">₱<?= number_format($row['product_selling_price'] ?? 0, 2) ?></td>
                                <td class="text-center"><?= $quantity ?></td>
                                <td>
                                  <span class="label label-<?= $status == 'Out of Stock' ? 'danger' : ($status == 'Low Stock' ? 'warning' : 'success') ?>">
                                    <?= $status ?>
                                  </span>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>

                      <!-- Category Summary Tab -->
                      <div role="tabpanel" class="tab-pane" id="category-summary">
                        <h3>Stock Summary by Category</h3>
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>Category</th>
                              <th>Total Items</th>
                              <th>Total Quantity</th>
                              <th>Total Value</th>
                              <th>Average Price</th>
                              <th>Low Stock Items</th>
                              <th>Out of Stock Items</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php 
                            $category_summary = [];
                            foreach ($rows as $row) {
                              $cat = $row['product_company'];
                              if (!isset($category_summary[$cat])) {
                                $category_summary[$cat] = [
                                  'count' => 0,
                                  'quantity' => 0,
                                  'value' => 0,
                                  'low_stock' => 0,
                                  'out_of_stock' => 0
                                ];
                              }
                              $category_summary[$cat]['count']++;
                              $category_summary[$cat]['quantity'] += $row['product_qty'];
                              $category_summary[$cat]['value'] += ($row['product_qty'] * $row['product_selling_price']);
                              
                              if ($row['product_qty'] <= 0) {
                                $category_summary[$cat]['out_of_stock']++;
                              } elseif ($row['product_qty'] <= 5) {
                                $category_summary[$cat]['low_stock']++;
                              }
                            }
                            
                            foreach ($category_summary as $category => $data): 
                              $avg_price = $data['quantity'] > 0 ? $data['value'] / $data['quantity'] : 0;
                            ?>
                              <tr>
                                <td><?= htmlspecialchars($category) ?></td>
                                <td class="text-center"><?= $data['count'] ?></td>
                                <td class="text-center"><?= number_format($data['quantity'], 0) ?></td>
                                <td class="text-right">₱<?= number_format($data['value'], 2) ?></td>
                                <td class="text-right">₱<?= number_format($avg_price, 2) ?></td>
                                <td class="text-center text-warning"><?= $data['low_stock'] ?></td>
                                <td class="text-center text-danger"><?= $data['out_of_stock'] ?></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>

                  <?php else: ?>
                    <p class="text-center text-muted">No stock records found for the selected criteria</p>
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
              <a href="view_stock.php" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Back to Stock
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