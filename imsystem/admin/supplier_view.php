<?php
include 'includes/session.php';
include 'includes/header.php';
?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Supplier Details</h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="supplier_add.php">Suppliers</a></li>
        <li class="active">Supplier Details</li>
      </ol>
    </section>

    <section class="content">
      <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> Error!</h4>
                    <ul>';
            foreach ($_SESSION['error'] as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul></div>';
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
            unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Supplier Information</h3>
              <div class="pull-right">
                <a href="supplier_add.php" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i> Back to Suppliers</a>
              </div>
            </div>
            <div class="box-body">
              <?php
                if(isset($_GET['id'])) {
                  $id = $_GET['id'];
                  $sql = "SELECT * FROM party_info WHERE id = '$id'";
                  $query = $conn->query($sql);
                  if($query->num_rows > 0) {
                    $row = $query->fetch_assoc();
                    
                    // Calculate total purchases from this supplier
                    $supplier_name = $row['businessname'];
                    $total_sql = "SELECT SUM(quantity * price) as total_purchases, COUNT(*) as total_orders 
                                  FROM purchase_master 
                                  WHERE party_name = '$supplier_name'";
                    $total_query = $conn->query($total_sql);
                    $total_row = $total_query->fetch_assoc();
                    $total_purchases = $total_row['total_purchases'] ? $total_row['total_purchases'] : 0;
                    $total_orders = $total_row['total_orders'] ? $total_row['total_orders'] : 0;
              ?>
              <div class="row">
                <div class="col-md-4">
                  <table class="table table-bordered table-fit">
                    <tr>
                      <th width="40%">First Name</th>
                      <td><?php echo $row['firstname']; ?></td>
                    </tr>
                    <tr>
                      <th>Last Name</th>
                      <td><?php echo $row['lastname']; ?></td>
                    </tr>
                    <tr>
                      <th>Company Name</th>
                      <td><?php echo $row['businessname']; ?></td>
                    </tr>
                    <tr>
                      <th>Contact</th>
                      <td><?php echo $row['contact']; ?></td>
                    </tr>
                  </table>
                </div>
                <div class="col-md-4">
                  <table class="table table-bordered table-fit">
                    <tr>
                      <th width="40%">Address</th>
                      <td><?php echo $row['address']; ?></td>
                    </tr>
                    <tr>
                      <th>City</th>
                      <td><?php echo $row['city']; ?></td>
                    </tr>
                    <tr>
                      <th>Remarks</th>
                      <td><?php echo !empty($row['remarks']) ? $row['remarks'] : 'No remarks'; ?></td>
                    </tr>
                  </table>
                </div>
                <div class="col-md-4">
                  <table class="table table-bordered table-fit">
                    <tr>
                      <th width="40%" class="purchase-summary-header">Total Orders</th>
                      <td class="purchase-summary-value"><?php echo number_format($total_orders); ?></td>
                    </tr>
                    <tr>
                      <th class="purchase-summary-header">Total Amount</th>
                      <td class="purchase-summary-total">₱<?php echo number_format($total_purchases, 2); ?></td>
                    </tr>
                
                  </table>
                </div>
              </div>
              <?php
                  } else {
                    echo "<div class='alert alert-warning'>Supplier not found.</div>";
                  }
                } else {
                  echo "<div class='alert alert-warning'>No supplier selected.</div>";
                }
              ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Items Purchased from This Supplier</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-striped table-fit">
                  <thead>
                    <tr>
                      <th>Purchase Date</th>
                      <th>Product Name</th>
                      <th>Category</th>
                      <th>Unit</th>
                      <th>Quantity</th>
                      <th>Price</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      if(isset($_GET['id'])) {
                        $supplier_id = $_GET['id'];
                        // Get supplier business name
                        $supplier_sql = "SELECT businessname FROM party_info WHERE id = '$supplier_id'";
                        $supplier_query = $conn->query($supplier_sql);
                        if($supplier_query->num_rows > 0) {
                          $supplier_row = $supplier_query->fetch_assoc();
                          $supplier_name = $supplier_row['businessname'];
                          
                          // Get items purchased from this supplier
                          $sql = "SELECT p.purchase_date, p.product_name, p.company_name, p.unit, p.quantity, p.price, (p.quantity * p.price) as total 
                                  FROM purchase_master p 
                                  WHERE p.party_name = '$supplier_name' 
                                  ORDER BY p.purchase_date DESC";
                          $query = $conn->query($sql);
                          
                          if($query->num_rows > 0) {
                            while($item = $query->fetch_assoc()) {
                              echo "<tr>
                                      <td>" . date('M d, Y', strtotime($item['purchase_date'])) . "</td>
                                      <td>" . $item['product_name'] . "</td>
                                      <td>" . $item['company_name'] . "</td>
                                      <td>" . $item['unit'] . "</td>
                                      <td>" . $item['quantity'] . "</td>
                                      <td>₱" . number_format($item['price'], 2) . "</td>
                                      <td>₱" . number_format($item['total'], 2) . "</td>
                                    </tr>";
                            }
                          } else {
                            echo "<tr><td colspan='7' class='text-center'>No items purchased from this supplier yet.</td></tr>";
                          }
                        }
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

<style>
.floating-box {
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.floating-box:hover {
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);  
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: 15px;
  margin-top: 20px;
}

.table-fit {
  width: 100%;
  border-collapse: collapse;
}

.table-fit th {
  background-color: #f8f9fa;
  text-align: left;
  padding: 15px 10px;
  font-weight: bold;
}

.table-fit td {
  padding: 12px 10px;
  text-align: left;
  border-top: 1px solid #ddd;
}

.table-fit tbody tr:hover {
  background-color: #f5f5f5;
}

.btn {
  border-radius: 8px;
  padding: 8px 16px;
  margin-right: 5px;
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.box-header.with-border {
  border-bottom: 1px solid #f4f4f4;
  padding: 15px 20px;
}

.box-title {
  font-weight: 600;
  font-size: 18px;
}

/* Purchase Summary Table Styles */
.purchase-summary-header {
  background-color: #3c8dbc !important;
  color: white !important;
  font-weight: bold !important;
}

.purchase-summary-value {
  font-weight: 600;
  color: #333;
  font-size: 16px;
}

.purchase-summary-total {
  font-weight: 700;
  color: #00a65a;
  font-size: 18px;
}
</style>
</body>
</html>