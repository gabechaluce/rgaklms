<?php 
ob_start();
include 'includes/session.php';

// Set timezone to Manila
date_default_timezone_set('Asia/Manila');

// Initialize variables
$rows = [];
$start_date = $end_date = date('Y-m-d');
$report_type = '';
$view_mode = false;
$selected_material = '';
$project_name = '';
$supplier_name = '';

// Process report generation
if (isset($_POST['generate']) || isset($_POST['view'])) {
    $period = $_POST['period'];
    $report_type = $_POST['report_type'] ?? '';
    $custom_date = $_POST['custom_date'] ?? null;
    $selected_material = $_POST['selected_material'] ?? '';
    $project_name = $_POST['project_name'] ?? '';
    $supplier_name = $_POST['supplier_name'] ?? '';
    $view_mode = isset($_POST['view']);

    // Validate and set date range
    if ($period === 'custom' && !empty($custom_date)) {
        if (DateTime::createFromFormat('Y-m-d', $custom_date) !== false) {
            $start_date = $end_date = $custom_date;
        } else {
            $_SESSION['error'] = 'Invalid custom date format';
            header('Location: materials_report.php');
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

    // Build the main query with filters - project data only with actual supplier and specification
    $sql = "SELECT 
                'Project' as source_type,
                bd.id as record_id,
                bd.product_name as material_name,
                COALESCE(pm.party_name, 'N/A') as supplier,
                COALESCE(pm.specification, 'N/A') as specification,
                bd.product_unit as unit,
                bd.price,
                bh.project_name,
                bh.date as record_date,
                bd.qty as quantity,
                bd.product_company as category
            FROM billing_details bd
            LEFT JOIN billing_header bh ON bd.bill_id = bh.id
            LEFT JOIN purchase_master pm ON bd.product_name = pm.product_name 
                AND bd.product_company = pm.company_name
            WHERE bh.date BETWEEN ? AND ?";
    
    $params = ['ss', $start_date, $end_date];
    
    // Add material filter
    if (!empty($selected_material)) {
        $sql = "SELECT * FROM ($sql) as combined_data WHERE material_name LIKE ?";
        $params[0] .= 's';
        $params[] = '%' . $selected_material . '%';
    }
    
    // Add project filter
    if (!empty($project_name)) {
        if (empty($selected_material)) {
            $sql = "SELECT * FROM ($sql) as combined_data WHERE (project_name = ? OR project_name = 'N/A')";
            $params[0] .= 's';
            $params[] = $project_name;
        } else {
            $sql .= " AND (project_name = ? OR project_name = 'N/A')";
            $params[0] .= 's';
            $params[] = $project_name;
        }
    }
    
    // Add supplier filter
    if (!empty($supplier_name)) {
        if (empty($selected_material) && empty($project_name)) {
            $sql = "SELECT * FROM ($sql) as combined_data WHERE supplier LIKE ?";
            $params[0] .= 's';
            $params[] = '%' . $supplier_name . '%';
        } else {
            $sql .= " AND supplier LIKE ?";
            $params[0] .= 's';
            $params[] = '%' . $supplier_name . '%';
        }
    }
    
    if (empty($selected_material) && empty($project_name) && empty($supplier_name)) {
        // No additional WHERE clause needed
    } else {
        // Already handled above
    }
    
    $sql .= " ORDER BY record_date DESC, material_name";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Database error: ' . $conn->error;
        header('Location: materials_report.php');
        exit();
    }

    // Handle exports (only if not in view mode)
    if (!$view_mode && in_array($report_type, ['excel', 'word'])) {
        ob_end_clean();
        
        if ($report_type == 'excel') {
            // CSV Export
            $filename = "materials_report_".date('Ymd').".csv";
            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            $output = fopen('php://output', 'w');
            
            // Materials data
            fputcsv($output, ['=== MATERIALS REPORT ===']);
            fputcsv($output, [
                'No.', 'Material Name', 'Supplier', 'Specification', 'Unit', 
                'Price', 'Project Name', 'Date', 'Quantity', 'Source', 'Category'
            ]);
            
            $counter = 1;
            foreach ($rows as $row) {
                fputcsv($output, [
                    $counter++,
                    $row['material_name'],
                    $row['supplier'],
                    $row['specification'],
                    $row['unit'],
                    number_format($row['price'], 2),
                    $row['project_name'],
                    date('M d, Y', strtotime($row['record_date'])),
                    $row['quantity'],
                    $row['source_type'],
                    $row['category']
                ]);
            }
            
            fclose($output);
            exit();
            
        } elseif ($report_type == 'word') {
            // HTML Table Export (for Word)
            $filename = "materials_report_".date('Ymd').".doc";
            header("Content-Type: application/vnd.ms-word");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            echo '<html><body>';
            echo '<h1>Materials Report</h1>';
            echo '<p>Period: '.date('M d, Y', strtotime($start_date)).' - '.date('M d, Y', strtotime($end_date)).'</p>';
            
            if (!empty($selected_material)) {
                echo '<p><strong>Material: </strong>' . htmlspecialchars($selected_material) . '</p>';
            }
            
            if (!empty($project_name)) {
                echo '<p><strong>Project: </strong>' . htmlspecialchars($project_name) . '</p>';
            }
            
            if (!empty($supplier_name)) {
                echo '<p><strong>Supplier: </strong>' . htmlspecialchars($supplier_name) . '</p>';
            }
            
            // Materials table
            echo '<h2>Materials Data</h2>';
            echo '<table border="1">';
            echo '<tr>
                    <th>No.</th>
                    <th>Material Name</th>
                    <th>Supplier</th>
                    <th>Specification</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Project Name</th>
                    <th>Date</th>
                    <th>Quantity</th>
                    <th>Source</th>
                    <th>Category</th>
                  </tr>';
            
            $counter = 1;
            foreach ($rows as $row) {
                echo '<tr>';
                echo '<td>'.$counter++.'</td>';
                echo '<td>'.$row['material_name'].'</td>';
                echo '<td>'.$row['supplier'].'</td>';
                echo '<td>'.$row['specification'].'</td>';
                echo '<td>'.$row['unit'].'</td>';
                echo '<td>'.number_format($row['price'], 2).'</td>';
                echo '<td>'.$row['project_name'].'</td>';
                echo '<td>'.date('M d, Y', strtotime($row['record_date'])).'</td>';
                echo '<td>'.$row['quantity'].'</td>';
                echo '<td>'.$row['source_type'].'</td>';
                echo '<td>'.$row['category'].'</td>';
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
    $total_amount += ($row['quantity'] * $row['price']);
    $total_quantity += $row['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Materials Report</title>
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
    .badge-purchase {
      background-color: #28a745;
      color: white;
    }
    .badge-project {
      background-color: #007bff;
      color: white;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Materials Reports</h1>

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
              <h3 class="box-title">Generate Materials Report</h3>
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
                      <label>Select Material:</label>
                      <select class="form-control" name="selected_material">
                        <option value="">All Materials</option>
                        <?php
                          // Get materials from both purchase_master and billing_details
                          $sql = "SELECT DISTINCT product_name as material_name FROM purchase_master 
                                  UNION 
                                  SELECT DISTINCT product_name as material_name FROM billing_details
                                  ORDER BY material_name";
                          $query = $conn->query($sql);
                          while($row = $query->fetch_assoc()){
                            $selected = (isset($_POST['selected_material']) && $_POST['selected_material'] == $row['material_name']) ? 'selected' : '';
                            echo "<option value='".$row['material_name']."' $selected>".$row['material_name']."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="row">
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
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Supplier:</label>
                      <select class="form-control" name="supplier_name">
                        <option value="">All Suppliers</option>
                        <?php
                          $sql = "SELECT DISTINCT party_name FROM purchase_master WHERE party_name IS NOT NULL AND party_name != '' ORDER BY party_name";
                          $query = $conn->query($sql);
                          while($row = $query->fetch_assoc()){
                            $selected = (isset($_POST['supplier_name']) && $_POST['supplier_name'] == $row['party_name']) ? 'selected' : '';
                            echo "<option value='".$row['party_name']."' $selected>".$row['party_name']."</option>";
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
                    <h3 class="box-title">Materials Report</h3>
                    <p><strong>Period:</strong> <?= date('F d, Y', strtotime($start_date)) ?> to <?= date('F d, Y', strtotime($end_date)) ?></p>
                    <?php if (!empty($selected_material)): ?>
                      <p><strong>Material:</strong> <?= htmlspecialchars($selected_material) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($project_name)): ?>
                      <p><strong>Project:</strong> <?= htmlspecialchars($project_name) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($supplier_name)): ?>
                      <p><strong>Supplier:</strong> <?= htmlspecialchars($supplier_name) ?></p>
                    <?php endif; ?>
                    <p><strong>Generated on:</strong> <?= date('F d, Y g:i A') ?> (Manila Time)</p>
                  </div>

                  <?php if (!empty($rows)): ?>
                    <!-- Summary Statistics -->
                    <div class="row" style="margin-bottom: 20px;">
                      <div class="col-md-3">
                        <div class="summary-box">
                          <span class="info-box-text">Total Records</span>
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
                          <span class="info-box-text">Total Value</span>
                          <span class="info-box-number">₱<?= number_format($total_amount, 2) ?></span>
                        </div>
                      </div>
                    </div>

                    <!-- Materials Table -->
                    <h3>Materials Data</h3>
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>No.</th>
                          <th>Material Name</th>
                          <th>Supplier</th>
                          <th>Specification</th>
                          <th>Unit</th>
                          <th>Price</th>
                          <th>Project Name</th>
                          <th>Date</th>
                          <th>Quantity</th>
                          <th>Total</th>
                          <th>Source</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $counter = 1;
                        foreach ($rows as $row): 
                          $row_total = $row['quantity'] * $row['price'];
                        ?>
                          <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($row['material_name']) ?></td>
                            <td><?= htmlspecialchars($row['supplier']) ?></td>
                            <td><?= htmlspecialchars($row['specification']) ?></td>
                            <td><?= htmlspecialchars($row['unit']) ?></td>
                            <td class="text-right">₱<?= number_format($row['price'], 2) ?></td>
                            <td><?= htmlspecialchars($row['project_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['record_date'])) ?></td>
                            <td class="text-center"><?= number_format($row['quantity'], 0) ?></td>
                            <td class="text-right">₱<?= number_format($row_total, 2) ?></td>
                            <td>
                              <span class="badge badge-<?= strtolower($row['source_type']) ?>">
                                <?= $row['source_type'] ?>
                              </span>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="8" class="text-right">Totals:</th>
                          <th class="text-center"><?= number_format($total_quantity) ?></th>
                          <th class="text-right">₱<?= number_format($total_amount, 2) ?></th>
                          <th></th>
                        </tr>
                      </tfoot>
                    </table>

                  <?php else: ?>
                    <p class="text-center text-muted">No materials records found for the selected criteria</p>
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
              <a href="index.php" class="btn btn-default">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
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
    'pageLength': 25,
    'dom': '<"top"Bf>rt<"bottom"lip><"clear">',
    'buttons': [
      {
        extend: 'excel',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        title: 'Materials Report',
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'word',
        text: '<i class="fa fa-file-word-o"></i> Word',
        title: 'Materials Report',
        exportOptions: {
          columns: ':visible'
        }
      },
      {
        extend: 'print',
        text: '<i class="fa fa-print"></i> Print',
        title: 'Materials Report',
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