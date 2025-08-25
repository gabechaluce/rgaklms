<?php 
ob_start();
include 'includes/session.php';

// Initialize variables
$rows = [];
$report_type = 'print';
$time_period = 'all';
$status_filter = 'all';

// Process report generation
if (isset($_POST['generate'])) {
    $report_type = $_POST['report_type'];
    $time_period = $_POST['time_period'];
    $status_filter = $_POST['status_filter'];
    
    // Build SQL query with time period filter
    $sql = "SELECT borrow.id, borrow.date_borrow, 
                   CONCAT(users.firstname, ' ', users.lastname) AS fullname, 
                   books.title AS equipment, 
                   borrow.quantity, 
                   borrow.status AS return_status
            FROM borrow 
            LEFT JOIN users ON users.id = borrow.user_id
            LEFT JOIN books ON books.id = borrow.book_id";

    $conditions = [];
    // Time period filter
    if ($time_period != 'all') {
        $current_date = date('Y-m-d');
        switch ($time_period) {
            case 'week':
                $conditions[] = "date_borrow >= DATE_SUB('$current_date', INTERVAL 1 WEEK)";
                break;
            case 'month':
                $conditions[] = "MONTH(date_borrow) = MONTH('$current_date') 
                                AND YEAR(date_borrow) = YEAR('$current_date')";
                break;
            case 'year':
                $conditions[] = "YEAR(date_borrow) = YEAR('$current_date')";
                break;
        }
    }

    // Status filter
    if ($status_filter != 'all') {
        $status_value = ($status_filter == 'returned') ? 1 : 0;
        $conditions[] = "borrow.status = $status_value";
    }

    // Combine conditions
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY borrow.date_borrow DESC";

    $query = $conn->query($sql);
    if (!$query) {
        $_SESSION['error'] = "Database error: " . $conn->error;
    } else {
        $rows = $query->fetch_all(MYSQLI_ASSOC);
    }
    
    // Handle exports (existing code with status filter added)
    if (in_array($report_type, ['excel', 'word'])) {
        ob_end_clean();
        
        $filename = "borrow_return_report_".date('Ymd').".";
        $period_text = ($time_period == 'all') ? 'All Time' : ucfirst($time_period);
        $status_text = ($status_filter == 'all') ? 'All Status' : 
                      (($status_filter == 'returned') ? 'Returned Only' : 'Not Returned Only');
        
        if ($report_type == 'excel') {
            $filename .= "csv";
            header("Content-Type: text/csv");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                'Borrow Date', 'Name', 'Equipment', 
                'Quantity', 'Status'
            ]);
            
            foreach ($rows as $row) {
                $status = $row['return_status'] ? 'Returned' : 'Not Returned';
                fputcsv($output, [
                    date('M d, Y', strtotime($row['date_borrow'])),
                    $row['fullname'],
                    $row['equipment'],
                    $row['quantity'],
                    $status
                ]);
            }
            fclose($output);
            exit();
            
        } elseif ($report_type == 'word') {
            $filename .= "doc";
            header("Content-Type: application/vnd.ms-word");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            
            echo '<html><body>';
            echo '<h1>Borrow/Return Report</h1>';
            echo '<p>Generated on: '.date('F d, Y').'</p>';
            echo '<p>Period: '.$period_text.'</p>';
            echo '<p>Status: '.$status_text.'</p>';
            
            echo '<table border="1">';
            echo '<tr>
                    <th>Borrow Date</th>
                    <th>Name</th>
                    <th>Equipment</th>
                    <th>Quantity</th>
                    <th>Status</th>
                  </tr>';
            
            foreach ($rows as $row) {
                $status = $row['return_status'] ? 'Returned' : 'Not Returned';
                echo '<tr>';
                echo '<td>'.date('M d, Y', strtotime($row['date_borrow'])).'</td>';
                echo '<td>'.$row['fullname'].'</td>';
                echo '<td>'.$row['equipment'].'</td>';
                echo '<td>'.$row['quantity'].'</td>';
                echo '<td>'.$status.'</td>';
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
  <title>Borrow/Return Report</title>
  <link rel="icon" type="image/x-icon" href="rga.png">
  <?php include 'includes/header.php'; ?>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Borrow/Return Reports</h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Borrow/Return Report</li>
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
              <h3 class="box-title">Generate Report</h3>
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
                      <label>Time Period:</label>
                      <select class="form-control" name="time_period" required>
                        <option value="all" <?= ($time_period === 'all') ? 'selected' : '' ?>>All Time</option>
                        <option value="week" <?= ($time_period === 'week') ? 'selected' : '' ?>>Last Week</option>
                        <option value="month" <?= ($time_period === 'month') ? 'selected' : '' ?>>This Month</option>
                        <option value="year" <?= ($time_period === 'year') ? 'selected' : '' ?>>This Year</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Status:</label>
                      <select class="form-control" name="status_filter" required>
                        <option value="all" <?= ($status_filter === 'all') ? 'selected' : '' ?>>All Status</option>
                        <option value="returned" <?= ($status_filter === 'returned') ? 'selected' : '' ?>>Returned</option>
                        <option value="not_returned" <?= ($status_filter === 'not_returned') ? 'selected' : '' ?>>Not Returned</option>
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
                  <h2 class="text-center">Borrow/Return Report</h2>
                  <p class="text-center">Generated on: <?= date('F d, Y') ?></p>
                  <p class="text-center">Period: <?= ($time_period === 'all') ? 'All Time' : ucfirst($time_period) ?></p>
                  <p class="text-center">Status: <?= ($status_filter === 'all') ? 'All Status' : 
                                                  (($status_filter === 'returned') ? 'Returned Only' : 'Not Returned Only') ?></p>
                  
                  <?php if (!empty($rows)): ?>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>Borrow Date</th>
                          <th>Name</th>
                          <th>Equipment</th>
                          <th>Quantity</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($rows as $row): 
                          $status = $row['return_status'] ? 'Returned' : 'Not Returned';
                          $statusClass = $row['return_status'] ? 'text-success' : 'text-danger';
                        ?>
                          <tr>
                            <td><?= date('M d, Y', strtotime($row['date_borrow'])) ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['equipment']) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td class="<?= $statusClass ?>"><?= $status ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php else: ?>
                    <p class="text-center text-muted">No records found for selected criteria</p>
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
                    .text-success { color: #5cb85c !important; }
                  }
                </style>
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
</body>
</html>
<?php ob_end_flush(); ?>