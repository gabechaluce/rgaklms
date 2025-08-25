<?php

ob_start();
include 'includes/session.php';
include 'includes/timezone.php';
$today = date('Y-m-d');
$year = date('Y');
if(isset($_GET['year'])){
    $year = $_GET['year'];
}
?>
<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
  <head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2c3e50;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #34495e;
        }
        
        .stat-box .inner h3, 
        .stat-box .inner p {
            color: white !important;
        }

        .floating-box {
            border-radius: 12px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
            background: #fff;
            transition: transform 0.25s ease-out, box-shadow 0.25s ease-out;
            margin-bottom: 20px;
        }

        .small-box {
            border-radius: 12px;
            padding: 20px;
            transition: transform 0.25s ease-out, box-shadow 0.25s ease-out;
            min-height: 120px;
        }

        .small-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .box-body .table {
            margin-bottom: 0;
        }

        .label {
            padding: 5px 8px;
            border-radius: 4px;
            font-weight: 500;
            display: inline-block;
        }

        .label-danger {
            background-color: #d9534f;
            color: white;
        }

        .label-warning {
            background-color: #f0ad4e;
            color: white;
        }

        .label-success {
            background-color: #5cb85c;
            color: white;
        }

        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 20px;
        }

        .action-card {
            flex: 1;
            min-width: 200px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            border-top: 4px solid var(--primary);
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: var(--success);
        }

        .action-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .action-card h3 {
            margin: 10px 0;
            font-size: 1.2rem;
            color: var(--dark);
        }

        .action-card p {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .action-card .btn {
            width: 100%;
        }

        .alert-panel {
            border-left: 4px solid;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
            background: white;
            display: flex;
            align-items: center;
        }

        .alert-panel i {
            font-size: 1.8rem;
            margin-right: 15px;
        }

        .alert-danger {
            border-left-color: var(--danger);
        }

        .alert-warning {
            border-left-color: var(--warning);
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .alert-description {
            color: #666;
            font-size: 0.9rem;
        }
        
        .summary-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .summary-box {
            flex: 1;
            min-width: 300px;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .summary-box:hover {
            transform: translateY(-7px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .summary-box.total {
            background: linear-gradient(135deg, #3498db, #1a5f9e);
        }
        
        .summary-box.low {
            background: linear-gradient(135deg, #f39c12, #d35400);
        }
        
        .summary-box.out {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .summary-value {
            font-size: 2.8rem;
            font-weight: 700;
            margin: 15px 0;
        }
        
        .summary-label {
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .summary-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        @media (max-width: 768px) {
            .quick-actions {
                flex-direction: column;
            }
            
            .action-card {
                min-width: 100%;
            }
            
            .summary-box {
                min-width: 100%;
            }
        }
    </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Inventory Dashboard
      </h1>
    </section>

    <!-- Main content -->
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
      
      <!-- Inventory Summary -->
      <div class="summary-container">
        <!-- Total Products -->
        <div class="summary-box total">
          <div class="summary-icon">
            <i class="fa fa-boxes"></i>
          </div>
          <div class="summary-value">
            <?php
              $sql = "SELECT COUNT(*) AS total FROM stock_master";
              $query = $conn->query($sql);
              $row = $query->fetch_assoc();
              echo $row['total'];
            ?>
          </div>
          <div class="summary-label">Total Products</div>
          <a href="view_stock.php?stock_status=all" class="btn btn-default btn-sm" style="margin-top: 15px;">
            View All Products
          </a>
        </div>
        
        <!-- Low Stock Items -->
        <div class="summary-box low">
          <div class="summary-icon">
            <i class="fa fa-exclamation-triangle"></i>
          </div>
          <div class="summary-value">
            <?php
              $sql = "SELECT COUNT(*) AS low_stock FROM stock_master WHERE product_qty > 0 AND product_qty <= 5";
              $query = $conn->query($sql);
              $row = $query->fetch_assoc();
              echo $row['low_stock'];
            ?>
          </div>
          <div class="summary-label">Low Stock Items</div>
          <a href="view_stock.php?stock_status=low" class="btn btn-warning btn-sm" style="margin-top: 15px;">
            View Low Stock
          </a>
        </div>
        
        <!-- Out-of-Stock Items -->
        <div class="summary-box out">
          <div class="summary-icon">
            <i class="fa fa-ban"></i>
          </div>
          <div class="summary-value">
            <?php
              $sql = "SELECT COUNT(*) AS out_of_stock FROM stock_master WHERE product_qty <= 0";
              $query = $conn->query($sql);
              $row = $query->fetch_assoc();
              echo $row['out_of_stock'];
            ?>
          </div>
          <div class="summary-label">Out-of-Stock Items</div>
          <a href="view_stock.php?stock_status=out" class="btn btn-danger btn-sm" style="margin-top: 15px;">
            View Out-of-Stock
          </a>
        </div>
      </div>
      
      <!-- Quick Actions -->
      <div class="row">
        <div class="col-md-12">
          <div class="box stat-box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Quick Actions</h3>
            </div>
            <div class="box-body">
              <div class="quick-actions">
                <div class="action-card">
                  <i class="fa fa-plus-circle"></i>
                  <h3>Add New Stock</h3>
                  <p>Add new inventory items to the system</p>
                  <a href="purchase_master.php" class="btn btn-primary">
                    <i class="fa fa-arrow-right"></i> Add Stock
                  </a>
                </div>
                
                <div class="action-card">
                  <i class="fa fa-file-invoice"></i>
                  <h3>Create Purchase Order</h3>
                  <p>Generate new purchase orders</p>
                  <a href="sales_master.php" class="btn btn-success">
                    <i class="fa fa-arrow-right"></i> Create PO
                  </a>
                </div>
                
                <div class="action-card">
                  <i class="fa fa-chart-line"></i>
                  <h3>Generate Reports</h3>
                  <p>Create stock report</p>
                  <a href="stock_report.php" class="btn btn-info">
                    <i class="fa fa-arrow-right"></i> View Reports
                  </a>
                </div>
              
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Stock Alerts -->
      <div class="row">
        <div class="col-md-12">
          <div class="box stat-box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Stock Alerts</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <!-- Out of Stock Alerts -->
              <div class="alert-panel alert-danger">
                <i class="fa fa-ban text-danger"></i>
                <div class="alert-content">
                  <div class="alert-title">Out of Stock Items</div>
                  <div class="alert-description">Immediate action required for these products</div>
                </div>
              </div>
              
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current Qty</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM stock_master 
                            WHERE product_qty <= 0
                            ORDER BY product_name ASC
                            LIMIT 5";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      echo "
                        <tr>
                          <td>".$row['product_name']."</td>
                          <td>".$row['product_company']."</td>
                          <td>0</td>
                          <td>
                            <a href='purchase_master.php?product_id=".$row['id']."' class='btn btn-xs btn-primary'>
                              <i class='fa fa-shopping-cart'></i> Reorder
                            </a>
                          </td>
                        </tr>
                      ";
                    }
                    
                    if ($query->num_rows == 0) {
                      echo '<tr><td colspan="4" class="text-center text-muted">No out-of-stock items</td></tr>';
                    }
                  ?>
                </tbody>
              </table>
              
              <!-- Low Stock Alerts -->
              <div class="alert-panel alert-warning">
                <i class="fa fa-exclamation-triangle text-warning"></i>
                <div class="alert-content">
                  <div class="alert-title">Low Stock Items</div>
                  <div class="alert-description">These products are running low and need attention</div>
                </div>
              </div>
              
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current Qty</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM stock_master 
                            WHERE product_qty > 0 AND product_qty <= 5
                            ORDER BY product_qty ASC
                            LIMIT 5";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      $quantity = intval($row['product_qty']);
                      echo "
                        <tr>
                          <td>".$row['product_name']."</td>
                          <td>".$row['product_company']."</td>
                          <td>".$quantity."</td>
                          <td>
                            <a href='purchase_master.php?product_id=".$row['id']."' class='btn btn-xs btn-success'>
                              <i class='fa fa-plus'></i> Restock
                            </a>
                          </td>
                        </tr>
                      ";
                    }
                    
                    if ($query->num_rows == 0) {
                      echo '<tr><td colspan="4" class="text-center text-muted">No low stock items</td></tr>';
                    }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="box-footer text-center">
              <a href="stock_report.php?stock_status=low_and_out" class="btn btn-default">
                <i class="fa fa-list"></i> View All Stock Alerts
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
</script>
</body>
</html>
<?php ob_end_flush(); ?>