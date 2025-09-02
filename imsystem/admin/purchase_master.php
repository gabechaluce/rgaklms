<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Restock</h1>
      <ol class="breadcrumb">
        <li><a href="index.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>Purchases</li>
        <li class="active">Add Purchase</li>
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
        <!-- Purchase Form -->
        <div class="col-md-5">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Add New Purchase</h3>
            </div>
            <div class="box-body">
              <form action="purchase_add.php" method="POST">
                <div class="form-group">
                  <label for="inventory_selection">Select Inventory:</label>
                  <select class="form-control" name="inventory_selection" id="inventory_selection">
                    <option value="">- Select Inventory -</option>
                    <?php
                      $sql = "SELECT * FROM inventory_selection";
                      $query = $conn->query($sql);
                      while($crow = $query->fetch_assoc()){
                        echo "<option value='".$crow['inventory_selection']."'>".$crow['inventory_selection']."</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="company_name">Select Category:</label>
                  <select class="form-control" name="company_name" id="company_name">
                    <option value="">- Select Category -</option>
                    <?php
                      $sql = "SELECT * FROM company_name";
                      $query = $conn->query($sql);
                      while($crow = $query->fetch_assoc()){
                        echo "<option value='".$crow['company_name']."'>".$crow['company_name']."</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="product_name">Select Product:</label>
                  <select class="form-control" name="product_name" id="product_name" required>
                    <option value="">- Select Product -</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="unit">Select Unit:</label>
                  <select class="form-control" name="unit" id="unit" required>
                    <option value="">- Select Unit -</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="specification">Select Specification:</label>
                  <select class="form-control" name="specification" id="specification" required>
                    <option value="">- Select Specification -</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="quantity">Enter Quantity:</label>
                  <input type="number" class="form-control" name="quantity" id="quantity" value="0" min="0" required>
                </div>
                <div class="form-group">
                  <label for="price">Enter Price:</label>
                  <input type="number" class="form-control" name="price" id="price" value="0" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                  <label for="party_name">Select Distributor Name:</label>
                  <select class="form-control" name="party_name" id="party_name" required>
                    <option value="">- Select Distributor -</option>
                    <?php
                      $sql = "SELECT * FROM party_info";
                      $query = $conn->query($sql);
                      while($prow = $query->fetch_assoc()){
                        echo "<option value='".$prow['businessname']."'>".$prow['businessname']."</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="purchase_type">Select Purchase Type:</label>
                  <select class="form-control" name="purchase_type" id="purchase_type" required>
                    <option value="">- Select Type -</option>
                    <option value="Cash">Cash</option>
                    <option value="Debit">Debit</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="expiry_date">Purchase Date:</label>
                  <input type="date" class="form-control" name="expiry_date" id="expiry_date" required>
                </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-primary btn-block" name="add"><i class="fa fa-save"></i> Purchase Now</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Recent Purchases -->
        <div class="col-md-7">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Recent Purchases</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <div class="dataTables_wrapper">
                  
                  <div class="row">
                    <div class="col-sm-12">
                      <table class="table table-bordered table-hover dataTable">
                        <thead>
                          <tr>
                            <th>Inventory Type <i class="fa fa-sort"></i></th>
                            <th>Category <i class="fa fa-sort"></i></th>
                            <th>Product <i class="fa fa-sort"></i></th>
                            <th>Specification <i class="fa fa-sort"></i></th>
                            <th>Quantity <i class="fa fa-sort"></i></th>
                            <th>Unit <i class="fa fa-sort"></i></th>
                            <th>Price <i class="fa fa-sort"></i></th>
                            <th>Purchase Date <i class="fa fa-sort"></i></th>
                            <th>Actions <i class="fa fa-sort"></i></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $sql = "SELECT * FROM purchase_master ORDER BY id DESC LIMIT 10";
                            $query = $conn->query($sql);
                            while($row = $query->fetch_assoc())
                            {
                          ?>
                          <tr>
                            <td><?php echo $row['inventory_selection']; ?></td>
                            <td><?php echo $row['company_name']; ?></td>
                            <td><?php echo $row['product_name']; ?></td>
                            <td><?php echo $row['specification'] ?? 'N/A'; ?></td>
                            <td><?php echo $row['quantity'];?></td>
                            <td><?php echo $row['unit']; ?></td>
                            <td><?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['purchase_date'])); ?></td>
                            <td>
                              <button class="btn btn-info btn-sm edit" data-id="<?php echo $row['id']; ?>"><i class="fa fa-edit"></i></button>
                              <button class="btn btn-danger btn-sm delete" data-id="<?php echo $row['id']; ?>"><i class="fa fa-trash"></i></button>
                              <button class="btn btn-info btn-sm view" data-id="<?php echo $row['id']; ?>"><i class="fa fa-eye"></i></button>
                            </td>
                          </tr>
                          <?php
                            }
                          ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  
                  
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
  
  <!-- Edit Purchase Modal -->
  <div class="modal fade" id="edit">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title"><b>Edit Purchase</b></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="purchase_edit.php">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
              <label for="edit_quantity" class="col-sm-3 control-label">Quantity</label>
              <div class="col-sm-9">
                <input type="number" class="form-control" id="edit_quantity" name="quantity" required>
              </div>
            </div>
            <div class="form-group">
              <label for="edit_price" class="col-sm-3 control-label">Price</label>
              <div class="col-sm-9">
                <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
              </div>
            </div>
            <div class="form-group">
              <label for="edit_purchase_type" class="col-sm-3 control-label">Purchase Type</label>
              <div class="col-sm-9">
                <select class="form-control" id="edit_purchase_type" name="purchase_type" required>
                  <option value="Cash">Cash</option>
                  <option value="Debit">Debit</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label for="edit_expiry_date" class="col-sm-3 control-label">Date</label>
              <div class="col-sm-9">
                <input type="date" class="form-control" id="edit_expiry_date" name="expiry_date" required>
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
          <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i> Update</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Purchase Modal -->
  <div class="modal fade" id="delete">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title"><b>Deleting...</b></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" method="POST" action="purchase_delete.php">
            <input type="hidden" name="id" id="del_id">
            <div class="text-center">
              <p>DELETE PURCHASE</p>
              <h2 id="del_product" class="bold"></h2>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
          <button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>

 <!-- View Purchase Modal (Updated) -->
  <div class="modal fade" id="view">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title"><b>Purchase History</b></h4>
        </div>
        <div class="modal-body">
          <div class="text-center">
            <h3 id="history_product_name" class="bold"></h3>
            <p id="history_product_details"></p>
          </div>
          <hr>
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Inventory</th>
                  <th>Category</th>
                  <th>Specification</th>
                  <th>Quantity</th>
                  <th>Unit</th>
                  <th>Price</th>
                  <th>Distributor</th>
                  <th>Type</th>
                  <th>Price Change</th>
                </tr>
              </thead>
              <tbody id="history_table_body">
                <!-- History will be loaded here -->
              </tbody>
            </table>
          </div>
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
  // Dynamic dropdown population
  $('#inventory_selection').change(function(){
    var inventory_selection = $(this).val();
    if(inventory_selection != ''){
      $.ajax({
        type: 'POST',
        url: 'purchase_category.php',
        data: {inventory_selection: inventory_selection},
        dataType: 'json',
        success: function(response){
          $('#company_name').html(response);
          $('#product_name').html('<option value="">- Select Product -</option>');
          $('#unit').html('<option value="">- Select Unit -</option>');
          $('#specification').html('<option value="">- Select Specification -</option>');
        }
      });
    }
    else{
      $('#company_name').html('<option value="">- Select Category -</option>');
      $('#product_name').html('<option value="">- Select Product -</option>');
      $('#unit').html('<option value="">- Select Unit -</option>');
      $('#specification').html('<option value="">- Select Specification -</option>');
    }
  });

  $('#company_name').change(function(){
    var company_name = $(this).val();
    if(company_name != ''){
      $.ajax({
        type: 'POST',
        url: 'purchase_product.php',
        data: {company_name: company_name},
        dataType: 'json',
        success: function(response){
          $('#product_name').html(response);
          $('#unit').html('<option value="">- Select Unit -</option>');
          $('#specification').html('<option value="">- Select Specification -</option>');
        }
      });
    }
    else{
      $('#product_name').html('<option value="">- Select Product -</option>');
      $('#unit').html('<option value="">- Select Unit -</option>');
      $('#specification').html('<option value="">- Select Specification -</option>');
    }
  });

  $('#product_name').change(function(){
    var product_name = $(this).val();
    var company_name = $('#company_name').val();
    if(product_name != ''){
      $.ajax({
        type: 'POST',
        url: 'purchase_unit.php',
        data: {
          product_name: product_name,
          company_name: company_name
        },
        dataType: 'json',
        success: function(response){
          $('#unit').html(response);
          $('#specification').html('<option value="">- Select Specification -</option>');
        }
      });
    }
    else{
      $('#unit').html('<option value="">- Select Unit -</option>');
      $('#specification').html('<option value="">- Select Specification -</option>');
    }
  });

  $('#unit').change(function(){
    var unit = $(this).val();
    var product_name = $('#product_name').val();
    var company_name = $('#company_name').val();
    
    if(unit != '' && product_name != '' && company_name != ''){
      $.ajax({
        type: 'POST',
        url: 'purchase_specification.php',
        data: {
          unit: unit,
          product_name: product_name,
          company_name: company_name
        },
        dataType: 'json',
        success: function(response){
          $('#specification').html(response);
        }
      });
    }
    else{
      $('#specification').html('<option value="">- Select Specification -</option>');
    }
  });

  // Edit purchase
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  // Delete purchase
  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  // View purchase history (Updated)
  $(document).on('click', '.view', function(e){
    e.preventDefault();
    var id = $(this).data('id');
    
    $.ajax({
        type: 'POST',
        url: 'purchase_view.php',
        data: {id: id},
        dataType: 'json',
        success: function(response){
            if(response.success){
                $('#history_product_name').text(response.product_name);
                $('#history_product_details').html(
                    '<strong>Category:</strong> ' + response.company_name + 
                    ' | <strong>Unit:</strong> ' + response.unit +
                    ' | <strong>Specification:</strong> ' + (response.specification || 'N/A')
                );
                
                var historyHtml = '';
                response.history.forEach(function(purchase, index) {
                    historyHtml += `
                        <tr>
                            <td>${purchase.action_time}</td>
                            <td>${purchase.inventory_selection}</td>
                            <td>${purchase.company_name}</td>
                            <td>${purchase.specification || 'N/A'}</td>
                            <td>${purchase.quantity}</td>
                            <td>${purchase.unit}</td>
                            <td>${parseFloat(purchase.price).toFixed(2)}</td>
                            <td>${purchase.party_name}</td>
                            <td>${purchase.purchase_type}</td>
                            <td>${purchase.action_type}</td>
                        </tr>
                    `;
                });
                
                $('#history_table_body').html(historyHtml);
                $('#view').modal('show');
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Error loading purchase history');
        }
    });
});
  
  // Initialize DataTable
  $('.dataTable').DataTable({
    'paging': true,
    'lengthChange': true,
    'searching': true,
    'ordering': true,
    'info': true,
    'autoWidth': false
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'purchase_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('#edit_id').val(response.id);
      $('#edit_quantity').val(response.quantity);
      $('#edit_price').val(response.price);
      $('#edit_purchase_type').val(response.purchase_type);
      $('#edit_expiry_date').val(response.expiry_date);
      $('#del_id').val(response.id);
      $('#del_product').html(response.company_name + ' - ' + response.product_name);
    }
  });
}
</script>

<style>
/* Main Container Styling */
.content-wrapper {
  background-color: #f4f6f9;
  padding: 20px;
}

/* Floating Box for forms and tables */
.floating-box {
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  background-color: #fff;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.floating-box:hover {
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);  
}

.box-header {
  padding: 15px 20px;
  border-bottom: 1px solid #f4f4f4;
}

.box-header .box-title {
  font-weight: 600;
  font-size: 18px;
}

.box-body {
  padding: 20px;
}

/* Table Styling */
.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.dataTable {
  width: 100% !important;
  border-collapse: collapse;
}

.dataTable th {
  background-color: #f8f9fa;
  text-align: left;
  padding: 12px 10px;
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #dee2e6;
}

.dataTable td {
  padding: 12px 10px;
  text-align: left;
  border-top: 1px solid #dee2e6;
  vertical-align: middle;
}

.dataTable tbody tr:hover {
  background-color: #f5f5f5;
}

/* Pagination Styling */
.pagination {
  display: inline-flex;
  padding-left: 0;
  margin: 20px 0;
  border-radius: 4px;
}

.pagination > li {
  display: inline;
}

.pagination > li > a {
  position: relative;
  float: left;
  padding: 6px 12px;
  margin-left: -1px;
  line-height: 1.428571429;
  text-decoration: none;
  background-color: #fff;
  border: 1px solid #ddd;
}

.pagination > .active > a {
  z-index: 2;
  color: #fff;
  cursor: default;
  background-color: #337ab7;
  border-color: #337ab7;
}

/* Form Styling */
.form-control {
  border-radius: 4px;
  box-shadow: none;
  border: 1px solid #ddd;
  padding: 8px 12px;
  height: auto;
}

.form-control:focus {
  border-color: #337ab7;
  box-shadow: 0 0 5px rgba(51, 122, 183, 0.3);
}

.form-group {
  margin-bottom: 15px;
}

label {
  font-weight: 600;
  color: #333;
  margin-bottom: 5px;
  display: block;
}

/* Button Styling */
.btn {
  border-radius: 4px;
  padding: 6px 12px;
  margin-right: 5px;
  transition: all 0.3s ease;
}

.btn:hover {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.btn-primary {
  background-color: #337ab7;
  border-color: #2e6da4;
}

.btn-info {
  background-color: #5bc0de;
  border-color: #46b8da;
}

.btn-danger {
  background-color: #d9534f;
  border-color: #d43f3a;
}

/* DataTable Wrapper */
.dataTables_wrapper .row {
  margin: 10px 0;
}

.dataTables_length select {
  width: 70px;
  display: inline-block;
  margin: 0 5px;
}

.dataTables_filter input {
  margin-left: 5px;
  width: 200px;
}

/* Responsive Adjustments */
@media screen and (max-width: 768px) {
  .dataTables_filter input {
    width: 100%;
    margin-left: 0;
    margin-top: 5px;
  }
  
  .box-body {
    padding: 15px 10px;
  }
  
  .form-group label {
    text-align: left;
  }
}
</style>
</body>
</html>