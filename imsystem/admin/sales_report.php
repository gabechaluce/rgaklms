<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Sales Reports</h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Sales Report</li>
      </ol>
    </section>

    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Filter Report</h3>
            </div>
            <div class="box-body">
              <form method="GET" action="sales_report.php">
                <div class="row">
                  <div class="col-md-3">
                      <div class="form-group">
                          <label>Project Name:</label>
                          <select class="form-control" name="project_name">
                              <option value="">All Projects</option>
                              <?php
                              // Query to get distinct project names
                              $project_query = $conn->query("SELECT DISTINCT project_name FROM billing_header WHERE project_name IS NOT NULL AND project_name != '' ORDER BY project_name");
                              while($project = $project_query->fetch_assoc()){
                                  $selected = (isset($_GET['project_name']) && $_GET['project_name'] == $project['project_name']) ? 'selected' : '';
                                  echo "<option value='".$project['project_name']."' $selected>".$project['project_name']."</option>";
                              }
                              ?>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Period:</label>
                      <select class="form-control" name="period" id="period">
                        <option value="custom" <?= (isset($_GET['period']) && $_GET['period'] == 'custom') ? 'selected' : '' ?>>Custom</option>
                        <option value="week" <?= (isset($_GET['period']) && $_GET['period'] == 'week') ? 'selected' : '' ?>>This Week</option>
                        <option value="month" <?= (isset($_GET['period']) && $_GET['period'] == 'month') ? 'selected' : '' ?>>This Month</option>
                        <option value="year" <?= (isset($_GET['period']) && $_GET['period'] == 'year') ? 'selected' : '' ?>>This Year</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Date From:</label>
                      <input type="date" class="form-control" name="date_from" 
                             value="<?= isset($_GET['date_from']) ? $_GET['date_from'] : '' ?>">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Date To:</label>
                      <input type="date" class="form-control" name="date_to" 
                             value="<?= isset($_GET['date_to']) ? $_GET['date_to'] : '' ?>">
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Customer Name:</label>
                      <input type="text" class="form-control" name="customer" 
                             value="<?= isset($_GET['customer']) ? $_GET['customer'] : '' ?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Bill Type:</label>
                      <select class="form-control" name="bill_type">
                        <option value="">All</option>
                        <option value="Cash" <?= (isset($_GET['bill_type']) && $_GET['bill_type'] == 'Cash') ? 'selected' : '' ?>>Cash</option>
                        <option value="Debit" <?= (isset($_GET['bill_type']) && $_GET['bill_type'] == 'Debit') ? 'selected' : '' ?>>Debit</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Inventory Selection:</label>
                      <select class="form-control" name="inventory">
                        <option value="">All</option>
                        <?php
                          $sql = "SELECT DISTINCT inventory_selection FROM inventory_selection";
                          $query = $conn->query($sql);
                          while($row = $query->fetch_assoc()){
                            echo "<option value='".$row['inventory_selection']."' ".((isset($_GET['inventory']) && $_GET['inventory'] == $row['inventory_selection']) ? 'selected' : '').">".$row['inventory_selection']."</option>";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3" style="margin-top:25px;">
                    <button type="submit" class="btn btn-primary" name="filter"><i class="fa fa-filter"></i> Filter</button>
                    <a href="sales_report.php" class="btn btn-default"><i class="fa fa-refresh"></i> Reset</a>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Sales Report</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-success btn-sm" onclick="exportToCSV()"><i class="fa fa-file-excel-o"></i> Excel</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="exportToWord()"><i class="fa fa-file-word-o"></i> Word</button>
                <button type="button" class="btn btn-info btn-sm" onclick="window.print()"><i class="fa fa-print"></i> Print</button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Bill No</th>
                      <th>Date</th>
                      <th>Project Name</th>
                      <th>Customer</th>
                      <th>Bill Type</th>
                      <th>Inventory</th>
                      <th>Total Amount</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                     $sql = "SELECT bh.id, bh.full_name, bh.bill_type, bh.date AS sale_date, bh.bill_no, bh.project_name,
               bd.product_company AS category,
               bd.product_unit AS unit,
               bd.qty AS quantity,
               SUM(bd.total) AS total_amount,
               GROUP_CONCAT(DISTINCT bd.inventory_selection SEPARATOR ', ') AS inventory_selection 
        FROM billing_header bh
        LEFT JOIN billing_details bd ON bh.id = bd.bill_id
        WHERE 1";

if(isset($_GET['filter'])){
  if(!empty($_GET['date_from'])){
    $sql .= " AND bh.date >= '".$_GET['date_from']."'";
  }
  if(!empty($_GET['date_to'])){
    $sql .= " AND bh.date <= '".$_GET['date_to']."'";
  }
  if(!empty($_GET['project_name'])){
  $sql .= " AND bh.project_name = '".$_GET['project_name']."'";
  }
  if(!empty($_GET['customer'])){
    $sql .= " AND bh.full_name LIKE '%".$_GET['customer']."%'";
  }
  if(!empty($_GET['bill_type'])){
    $sql .= " AND bh.bill_type = '".$_GET['bill_type']."'";
  }
  if(!empty($_GET['inventory'])){
    $sql .= " AND bd.inventory_selection = '".$_GET['inventory']."'";
  }
}

$sql .= " GROUP BY bh.id ORDER BY bh.date DESC";
                      $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){echo "
                    <tr>
                    <td>".$row['bill_no']."</td>
                    <td>".date('M d, Y', strtotime($row['sale_date']))."</td>
                    <td>".$row['project_name']."</td>
                    <td>".$row['full_name']."</td>
                    <td>".$row['bill_type']."</td>
                    <td>".$row['inventory_selection']."</td>
                    <td>".number_format($row['total_amount'], 2)."</td>
                    <td>
        <button class='btn btn-info btn-sm view-details' data-id='".$row['id']."'>
          <i class='fa fa-eye'></i> View
        </button>
      </td>
    </tr>
  ";
}
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Details Modal -->
  <div class="modal fade" id="detailsModal">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Bill Details</h4>
        </div>
        <div class="modal-body" id="detailsContent">
          <!-- Details will be loaded here via AJAX -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  // Handle period change
  $('#period').change(function() {
    var period = $(this).val();
    var dateFrom = '';
    var dateTo = '';

    if (period === 'week') {
      var today = new Date();
      var dayOfWeek = today.getDay();
      var diff = today.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
      var monday = new Date(today.setDate(diff));
      var sunday = new Date(monday);
      sunday.setDate(monday.getDate() + 6);

      dateFrom = monday.toISOString().split('T')[0];
      dateTo = sunday.toISOString().split('T')[0];
    } else if (period === 'month') {
      var date = new Date();
      dateFrom = new Date(date.getFullYear(), date.getMonth(), 1).toISOString().split('T')[0];
      dateTo = new Date(date.getFullYear(), date.getMonth() + 1, 0).toISOString().split('T')[0];
    } else if (period === 'year') {
      var date = new Date();
      dateFrom = date.getFullYear() + '-01-01';
      dateTo = date.getFullYear() + '-12-31';
    }

    if (period !== 'custom') {
      $('input[name="date_from"]').val(dateFrom);
      $('input[name="date_to"]').val(dateTo);
      $('input[name="date_from"], input[name="date_to"]').prop('disabled', true);
    } else {
      $('input[name="date_from"], input[name="date_to"]').prop('disabled', false);
    }
  });

  // Trigger change on page load if period is set
  <?php if (isset($_GET['period']) && $_GET['period'] != 'custom'): ?>
    $('#period').trigger('change');
  <?php endif; ?>

  // View Bill Details
  $('.view-details').click(function(){
    var billId = $(this).data('id');
    $.ajax({
      type: 'POST',
      url: 'sales_details.php',
      data: {id: billId},
      success: function(response){
        $('#detailsContent').html(response);
        $('#detailsModal').modal('show');
      }
    });
  });
});

function exportToCSV() {
  var params = $('form').serialize();
  window.location.href = 'sales_export.php?' + params;
}

function exportToWord() {
  var params = $('form').serialize();
  window.location.href = 'sales_word_export.php?' + params;
}
</script>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  .box, .box * {
    visibility: visible;
  }
  .box {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    border: none;
    box-shadow: none;
  }
  .table {
    border-collapse: collapse;
    width: 100%;
  }
  .table th, .table td {
    border: 1px solid #000;
    padding: 5px;
  }
  .box-header, .box-tools, .filter-form, .navbar, .sidebar, .content-header, .footer {
    display: none !important;
  }
}
</style>
</body>
</html>