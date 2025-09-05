<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Current Stock</h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="sales_master.php">Sales</a></li>
        <li class="active">View Stock</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Inventory Status</h3>
              <?php
                $category = isset($_GET['category']) ? $_GET['category'] : '';
                if(!empty($category)) {
                  echo '<span class="pull-right">Filtered by: <strong>'.$category.'</strong> <a href="view_stock.php" class="btn btn-xs btn-default"><i class="fa fa-times"></i> Clear</a></span>';
                }
              ?>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="stockTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Category</th>
                      <th>Inventory Type</th>
                      <th>Product</th>
                      <th>Unit</th>
                      <th>Specification</th>
                      <th>Price</th>
                      <th>Available Quantity</th>
                      <th>Stock Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $where = "";
                      if(!empty($category)) {
                        $where = "WHERE product_company = '$category'";
                      }
                      
                      $sql = "SELECT * FROM stock_master $where ORDER BY product_company, product_name";
                      $query = $conn->query($sql);
                      
                      while($row = $query->fetch_assoc()) {
                        $status = '';
                        $quantity = intval($row['product_qty']);
                        
                        if($quantity <= 0) {
                          $status = '<span class="label label-danger">Out of Stock</span>';
                        } else if($quantity <= 5) {
                          $status = '<span class="label label-warning">Low Stock</span>';
                        } else {
                          $status = '<span class="label label-success">In Stock</span>';
                        }
                        
                        echo "
                          <tr>
                            <td>".$row['product_company']."</td>
                            <td>".$row['inventory_selection']."</td>
                            <td>".$row['product_name']."</td>
                            <td>".$row['product_unit']."</td>
                            <td>".($row['specification'] ?? 'N/A')."</td>
                            <td>".number_format($row['product_selling_price'], 2)."</td>
                            <td>".$quantity."</td>
                            <td>".$status."</td>
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

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  $('#stockTable').DataTable({
    responsive: true,
    "order": [[ 0, "asc" ]],
    "pageLength": 25
  });
});
</script>

<style>
/* Floating Box for forms and tables */
.floating-box {
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.floating-box:hover {
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);  
}

/* Table Styling */
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: 15px;
  margin-top: 20px;
}

.table {
  border-collapse: collapse;
  margin: 0;
  padding: 0;
  border-radius: 15px;
  overflow: hidden;
}

.table th {
  background-color: #f8f9fa;
  text-align: center;
  padding: 15px 10px;
  font-weight: bold;
}

.table td {
  padding: 12px 10px;
  text-align: center;
  border-top: 1px solid #ddd;
}

.table tbody tr:hover {
  background-color: #f5f5f5;
  cursor: pointer;
}
</style>
</body>
</html>