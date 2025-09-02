<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Add New Product</h1>
     
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
            <h3 class="box-title">Product List</h3>
              <div class="pull-right">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addnew"><i class="fa fa-plus"></i> Add New</button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-fit">
                  <thead>
                    <tr>
                      <th>Inventory For</th>
                      <th>Category</th>
                      <th>Product Name</th>
                      <th>Unit</th>
                      
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sql = "SELECT * FROM products ORDER BY id DESC";
                      $query = $conn->query($sql);
                      while($row = $query->fetch_assoc())
                      {
                    ?>
                    <tr>
                      <td><?php echo $row["inventory_selection"]; ?></td>
                      <td><?php echo $row["company_name"]; ?></td>
                      <td><?php echo $row["product_name"]; ?></td>
                      <td><?php echo $row["unit"]; ?></td>
                    
                      <td>
                        <button class="btn btn-primary btn-sm edit" data-id="<?php echo $row['id']; ?>"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn btn-danger btn-sm delete" data-id="<?php echo $row['id']; ?>"><i class="fa fa-trash"></i> Delete</button>
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
    </section>   
    
  </div>
  

  <?php include 'includes/product_add_modal.php'; ?>
  
  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
  // Dynamic category loading based on inventory selection
  $('#inventory_selection').change(function(){
        var inventory_selection = $(this).val();
        if(inventory_selection != ''){
            $.ajax({
                type: 'POST',
                url: 'product_add_category.php',
                data: {inventory_selection: inventory_selection},
                dataType: 'json',
                success: function(response){
                    $('#company_name').html(response);
                },
                error: function(){
                    $('#company_name').html('<option value="">Error loading categories</option>');
                }
            });
        }
        else{
            $('#company_name').html('<option value="">- Select Category -</option>');
        }
    });

  // Edit product
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  // Delete product
  $(document).on('click', '.delete', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    $('#delete').modal('show');
    $('#delete form').attr('action', 'product_add_delete.php?id=' + id);
});
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'product_add_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
  $('.unitid').val(response.id);
  $('#edit_inventory_selection').val(response.inventory_selection); // Set inventory selection
  $('#edit_company_name').val(response.company_name);
  $('#edit_product_name').val(response.product_name);
  $('#edit_unit').val(response.unit);
  $('#del_product').html(response.product_name);
}
  });
}
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

.table-fit {
  width: 100%;
  table-layout: auto;
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
  background-color: rgb(243, 237, 55);
  cursor: pointer;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Form Styling */
.form-control {
  border-radius: 8px;
  box-shadow: none;
  border: 1px solid #ddd;
  padding: 8px 12px;
  height: auto;
}

.form-control:focus {
  border-color: #3c8dbc;
  box-shadow: 0 0 5px rgba(60, 141, 188, 0.3);
}

.form-group {
  margin-bottom: 20px;
}

.btn {
  border-radius: 8px;
  padding: 8px 16px;
  margin-right: 5px;
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

.btn-primary {
  background-color: #3c8dbc;
  border-color: #367fa9;
}

.btn-danger {
  background-color: #dd4b39;
  border-color: #d73925;
}

/* Responsive Adjustments */
@media screen and (max-width: 768px) {
  .table-fit th, .table-fit td {
    font-size: 12px;
    padding: 8px;
  }
  
  .form-group label {
    text-align: left;
  }
}
</style>
</body>
</html>