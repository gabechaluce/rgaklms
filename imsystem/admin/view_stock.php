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
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Inventory Status</h3>
              
              <!-- Filter Form -->
              <div class="pull-right">
                <form method="GET" class="form-inline" style="display: inline-block;">
                  <div class="form-group" style="margin-right: 10px;">
                    <label for="inventory_selection" style="margin-right: 5px;">Inventory:</label>
                    <select name="inventory_selection" id="inventory_selection" class="form-control input-sm" onchange="this.form.submit()">
                      <option value="">All Inventories</option>
                      <?php
                        $inventory_sql = "SELECT DISTINCT inventory_selection FROM inventory_selection ORDER BY inventory_selection";
                        $inventory_query = $conn->query($inventory_sql);
                        $selected_inventory = isset($_GET['inventory_selection']) ? $_GET['inventory_selection'] : '';
                        
                        while($inv_row = $inventory_query->fetch_assoc()) {
                          $selected = ($selected_inventory == $inv_row['inventory_selection']) ? 'selected' : '';
                          echo "<option value='".$inv_row['inventory_selection']."' $selected>".$inv_row['inventory_selection']."</option>";
                        }
                      ?>
                    </select>
                  </div>
                  
                  <div class="form-group" style="margin-right: 10px;">
                    <label for="category" style="margin-right: 5px;">Category:</label>
                    <select name="category" id="category" class="form-control input-sm" onchange="this.form.submit()">
                      <option value="">All Categories</option>
                      <?php
                        $category_where = "";
                        if(!empty($selected_inventory)) {
                          $category_where = "WHERE inventory_selection = '$selected_inventory'";
                        }
                        
                        $category_sql = "SELECT DISTINCT product_company FROM stock_master $category_where ORDER BY product_company";
                        $category_query = $conn->query($category_sql);
                        $selected_category = isset($_GET['category']) ? $_GET['category'] : '';
                        
                        while($cat_row = $category_query->fetch_assoc()) {
                          $selected = ($selected_category == $cat_row['product_company']) ? 'selected' : '';
                          echo "<option value='".$cat_row['product_company']."' $selected>".$cat_row['product_company']."</option>";
                        }
                      ?>
                    </select>
                  </div>
                  
                  <?php if(!empty($selected_inventory) || !empty($selected_category)): ?>
                    <a href="view_stock.php" class="btn btn-xs btn-default">
                      <i class="fa fa-times"></i> Clear
                    </a>
                  <?php endif; ?>
                </form>
              </div>
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
                      <?php if( $user['type'] != 3  ): ?>
                        <th>Price</th>
                      <?php endif; ?>
                      <th>Available Quantity</th>
                      <th>Stock Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $where_conditions = [];
                      
                      // Add inventory selection filter
                      if(!empty($selected_inventory)) {
                        $where_conditions[] = "inventory_selection = '$selected_inventory'";
                      }
                      
                      // Add category filter
                      if(!empty($selected_category)) {
                        $where_conditions[] = "product_company = '$selected_category'";
                      }
                      
                      $where_clause = "";
                      if(!empty($where_conditions)) {
                        $where_clause = "WHERE " . implode(" AND ", $where_conditions);
                      }
                      
                      $sql = "SELECT * FROM stock_master $where_clause ORDER BY product_company, product_name";
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
                        
                        echo "<tr>";
                        echo "<td>".$row['product_company']."</td>";
                        echo "<td>".$row['inventory_selection']."</td>";
                        echo "<td>".$row['product_name']."</td>";
                        echo "<td>".$row['product_unit']."</td>";
                        echo "<td>".($row['specification'] ?? 'N/A')."</td>";
                        
                        // Only show price if user is not a designer
                        if($user['type'] != 3) {
                          echo "<td>".number_format($row['product_selling_price'], 2)."</td>";
                        }
                        
                        echo "<td>".$quantity."</td>";
                        echo "<td>".$status."</td>";
                        echo "</tr>";
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

/* Filter Form Styling */
.form-inline .form-group {
  vertical-align: middle;
}

.form-inline label {
  font-weight: normal;
  color: #666;
}
</style>
</body>
</html>