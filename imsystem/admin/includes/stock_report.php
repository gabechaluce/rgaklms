<?php 
include 'includes/session.php';

// PDF Generation Handling
if (isset($_POST['generate_report'])) {
    require_once($_SERVER['DOCUMENT_ROOT'].'/taskmanagementsystem/tcpdf/tcpdf.php');
    
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $report_type = $_POST['report_type'];

    // Fetch data
    $where = "";
    if(!empty($category)) {
        $where = "WHERE product_company = '$category'";
    }
    
    $sql = "SELECT * FROM stock_master $where ORDER BY product_company, product_name";
    $query = $conn->query($sql);
    $rows = $query->fetch_all(MYSQLI_ASSOC);

    if ($report_type == 'pdf') {
        // Create PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Stock Report');
        $pdf->AddPage();

        // Add content
        $html = '<h1 style="text-align:center">Stock Report</h1>';
        if(!empty($category)) {
            $html .= '<p style="text-align:center">Category: '.$category.'</p>';
        }
        $html .= '<table border="1" cellpadding="5">
                    <tr>
                        <th>Category</th>
                        <th>Product</th>
                        <th>Unit</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Status</th>
                    </tr>';
        
        foreach ($rows as $row) {
            $status = getStatusLabel($row['product_qty']);
            $html .= '<tr>
                        <td>'.$row['product_company'].'</td>
                        <td>'.$row['product_name'].'</td>
                        <td>'.$row['product_unit'].'</td>
                        <td>'.number_format($row['product_selling_price'], 2).'</td>
                        <td>'.$row['product_qty'].'</td>
                        <td>'.$status.'</td>
                    </tr>';
        }
        
        $html .= '</table>';
        
        $pdf->writeHTML($html);
        $pdf->Output('stock_report_'.date('Ymd').'.pdf', 'D');
        exit();
    }
}

function getStatusLabel($quantity) {
    $quantity = intval($quantity);
    if($quantity <= 0) return 'Out of Stock';
    if($quantity <= 5) return 'Low Stock';
    return 'In Stock';
}
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Existing navigation and header code... -->

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Current Stock</h1>
      <!-- ... existing breadcrumb ... -->
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Export Options</h3>
            </div>
            <div class="box-body">
              <form method="post" action="">
                <div class="row">
                  <div class="col-md-3">
                    <select class="form-control" name="report_type" required>
                      <option value="pdf">PDF Export</option>
                      <option value="print">Print View</option>
                    </select>
                  </div>
                  <div class="col-md-2">
                    <button type="submit" name="generate_report" class="btn btn-success">
                      <i class="fa fa-file-pdf-o"></i> Generate Report
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Print Section -->
      <?php if(isset($_POST['generate_report']) && $_POST['report_type'] == 'print'): ?>
      <div id="printSection" class="row print-view">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header text-center">
              <h2>Stock Report</h2>
              <?php if(!empty($category)): ?>
                <p>Category: <?php echo $category; ?></p>
              <?php endif; ?>
              <p>Generated on: <?php echo date('M d, Y H:i'); ?></p>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sql = "SELECT * FROM stock_master $where ORDER BY product_company, product_name";
                  $query = $conn->query($sql);
                  while($row = $query->fetch_assoc()):
                  ?>
                  <tr>
                    <td><?php echo $row['product_company']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['product_unit']; ?></td>
                    <td><?php echo number_format($row['product_selling_price'], 2); ?></td>
                    <td><?php echo $row['product_qty']; ?></td>
                    <td><?php echo getStatusLabel($row['product_qty']); ?></td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 text-center">
          <button onclick="window.print()" class="btn btn-primary">
            <i class="fa fa-print"></i> Print Now
          </button>
        </div>
      </div>
      <?php endif; ?>

      <!-- Original stock table (hidden during printing) -->
      <div class="row" id="mainStockTable">
        <!-- Your existing stock table code here -->
      </div>

    </section>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(document).ready(function() {
  window.onafterprint = function() {
    $('.print-view').hide();
    $('#mainStockTable').show();
  }
});

function printReport() {
  $('#mainStockTable').hide();
  $('.print-view').show();
  window.print();
}
</script>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .print-view, .print-view * {
    visibility: visible;
  }
  .print-view {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  .table {
    font-size: 12px;
  }
  .box-header {
    margin-bottom: 20px;
  }
}
.print-view {
  display: none;
}
</style>
</body>
</html>