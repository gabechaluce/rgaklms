<?php 
ob_start();
include 'includes/session.php';

// Initialize variables
$rows = [];
$inventory_type = 'all';
$report_type = 'print';
$stock_status = 'all';

// Process report generation
if (isset($_POST['generate'])) {
    $inventory_type = $_POST['inventory_type'];
    $report_type = $_POST['report_type'];
    $stock_status = $_POST['stock_status'];
    
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
    // Add this new condition for low_and_out
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
            // Fixed: Low stock products of this type
            $sql = "SELECT * FROM stock_master WHERE inventory_selection = '$escaped_type' AND product_qty > 0 AND product_qty <= 5 ORDER BY product_company, product_name";
        } 
        elseif ($stock_status == 'out') {
            // Fixed: Out of stock products of this type
            $sql = "SELECT * FROM stock_master WHERE inventory_selection = '$escaped_type' AND product_qty <= 0 ORDER BY product_company, product_name";
        }
        // NEW ADDITION - Add the option to show both low and out of stock for a specific inventory type
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
    
    // Handle exports
    if (in_array($report_type, ['excel', 'word'])) {
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
?>

<!DOCTYPE html>
<html>
<head>
  <title>Stock Report</title>
  <link rel="icon" type="image/x-icon" href="rga.png">
  <?php include 'includes/header.php'; ?>
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
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Generate Stock Report</h3>
            </div>
            <div class="box-body">
              <form method="post" action="">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Report Type:</label>
                      <select class="form-control" name="report_type" required>
                        <option value="excel" <?= ($report_type === 'excel') ? 'selected' : '' ?>>Excel</option>
                        <option value="word" <?= ($report_type === 'word') ? 'selected' : '' ?>>Word</option>
                        <option value="print" <?= ($report_type === 'print') ? 'selected' : '' ?>>Print</option>
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
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>&nbsp;</label>
                      <button type="submit" class="btn btn-primary btn-block" name="generate">
                        <i class="fa fa-file"></i> Generate Report
                      </button>
                    </div>
                  </div>
                </div>
              </form>

              <?php if (isset($_POST['generate']) && $report_type == 'print'): ?>
                <div id="printSection">
                  <h2 class="text-center">Stock Report</h2>
                  <p class="text-center">Generated on: <?= date('F d, Y') ?></p>
                  
                  <?php if ($inventory_type != 'all'): ?>
                    <p class="text-center">Inventory Type: <?= $inventory_type ?></p>
                  <?php endif; ?>
                  
                  <?php if ($stock_status != 'all'): ?>
                    <p class="text-center">Status Filter: 
                      <?php 
                      if ($stock_status == 'low_and_out') {
                          echo 'Low Stock and Out of Stock';
                      } else {
                          echo ($stock_status == 'low' ? 'Low Stock' : 'Out of Stock');
                      }
                      ?>
                    </p>
                  <?php endif; ?>
                  
                  <?php if (!empty($rows)): ?>
                    <table class="table table-bordered">
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
                          $quantity = intval($row['product_qty']?? 0);
                          $status = '';
                          $statusClass = '';
                          
                          if ($quantity <= 0) {
                              $status = 'Out of Stock';
                              $statusClass = 'text-danger';
                          } elseif ($quantity <= 5) {
                              $status = 'Low Stock';
                              $statusClass = 'text-warning';
                          } else {
                              $status = 'In Stock';
                              $statusClass = 'text-success';
                          }
                        ?>
                          <tr>
                        <td><?= htmlspecialchars($row['product_company']) ?? 'N/A' ?></td>
                        <td><?= htmlspecialchars($row['inventory_selection'])?? 'N/A' ?></td>
                        <td><?= htmlspecialchars($row['product_name']) ?? 'N/A' ?></td>
                        <td><?= htmlspecialchars($row['product_unit']) ?? 'N/A' ?></td>
                        <td><?= number_format($row['product_selling_price'], 2) ?? 'N/A' ?></td>   
                        <td><?= ($row['product_qty'])?></td>
                            <td class="<?= $statusClass ?>"><?= $status ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php else: ?>
                    <p class="text-center text-muted">No stock records found matching the selected criteria</p>
                  <?php endif; ?>
                </div>
                <script>
                  window.onload = function() {
                    window.print();
                  }
                </script>
                <style>
                  @media print {
                    body * { visibility: hidden; }
                    #printSection, #printSection * { visibility: visible; }
                    #printSection { position: absolute; left: 0; top: 0; }
                    .table { border-collapse: collapse; }
                    .table th, .table td { border: 1px solid #000; padding: 5px; }
                    .text-danger { color: #d9534f !important; }
                    .text-warning { color: #f0ad4e !important; }
                    .text-success { color: #5cb85c !important; }
                  }
                </style>
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
</body>
</html>
<?php ob_end_flush(); ?>