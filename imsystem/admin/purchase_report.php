<?php 
ob_start();
include 'includes/session.php';

// Initialize variables
$rows = [];
$start_date = $end_date = date('Y-m-d');
$report_type = '';

// Process report generation
if (isset($_POST['generate'])) {
    $period = $_POST['period'];
    $report_type = $_POST['report_type'];
    $custom_date = $_POST['custom_date'] ?? null;

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

    // Fetch data with prepared statement
    $sql = "SELECT * FROM purchase_master 
            WHERE purchase_date BETWEEN ? AND ? 
            ORDER BY purchase_date DESC";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ss', $start_date, $end_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Database error: ' . $conn->error;
        header('Location: purchase_report.php');
        exit();
    }

    // Handle exports
    if (in_array($report_type, ['excel', 'word'])) {
        ob_end_clean();
        
        if ($report_type == 'excel') {
            // CSV Export
            $filename = "purchase_report_".date('Ymd').".csv";
            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Inventory Type', 'Category', 'Product', 'Quantity',
                'Unit', 'Price', 'Vendor', 'Purchase Date'
            ]);
            
            foreach ($rows as $row) {
                fputcsv($output, [
                    $row['inventory_selection'],
                    $row['company_name'],
                    $row['product_name'],
                    $row['quantity'],
                    $row['unit'],
                    number_format($row['price'], 2),
                    $row['party_name'],
                    date('M d, Y', strtotime($row['purchase_date']))
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
            echo '<table border="1">';
            echo '<tr>
                    <th>Inventory Type</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Vendor</th>
                    <th>Purchase Date</th>
                  </tr>';
            
            foreach ($rows as $row) {
                echo '<tr>';
                echo '<td>'.$row['inventory_selection'].'</td>';
                echo '<td>'.$row['company_name'].'</td>';
                echo '<td>'.$row['product_name'].'</td>';
                echo '<td>'.$row['quantity'].'</td>';
                echo '<td>'.$row['unit'].'</td>';
                echo '<td>'.number_format($row['price'], 2).'</td>';
                echo '<td>'.$row['party_name'].'</td>';
                echo '<td>'.date('M d, Y', strtotime($row['purchase_date'])).'</td>';
                echo '</tr>';
            }
            echo '</table></body></html>';
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Purchase Report</title>
  <link rel="icon" type="image/x-icon" href="rga.png">
  <?php include 'includes/header.php'; ?>
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
                      <select class="form-control" name="report_type" required>
                        <option value="excel" <?= ($_POST['report_type'] ?? '') === 'excel' ? 'selected' : '' ?>>Excel</option>
                        <option value="word" <?= ($_POST['report_type'] ?? '') === 'word' ? 'selected' : '' ?>>Word</option>
                        <option value="print" <?= ($_POST['report_type'] ?? '') === 'print' ? 'selected' : '' ?>>Print</option>
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
                  <h2 class="text-center">Purchase Report</h2>
                  <p class="text-center">
                    <?= date('F d, Y', strtotime($start_date)) ?> to <?= date('F d, Y', strtotime($end_date)) ?>
                  </p>
                  <?php if (!empty($rows)): ?>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Inventory Type</th>
                          <th>Category</th>
                          <th>Product</th>
                          <th>Quantity</th>
                          <th>Unit</th>
                          <th>Price</th>
                          <th>Vendor</th>
                          <th>Purchase Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($rows as $row): ?>
                          <tr>
                            <td><?= htmlspecialchars($row['inventory_selection']) ?></td>
                            <td><?= htmlspecialchars($row['company_name']) ?></td>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= htmlspecialchars($row['unit']) ?></td>
                            <td><?= number_format($row['price'], 2) ?></td>
                            <td><?= htmlspecialchars($row['party_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['purchase_date'])) ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php else: ?>
                    <p class="text-center text-muted">No purchase records found for the selected period</p>
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
                  }
                </style>
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
});
</script>
</body>
</html>
<?php ob_end_flush(); ?>