<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Product Specifications</h1>
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
              <h3 class="box-title">Specifications List</h3>
              <div class="pull-right">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSpecModal"><i class="fa fa-plus"></i> Add Specification</button>
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
                      <th>Specification Name</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $sql = "SELECT s.*, p.product_name 
                              FROM specifications s 
                              JOIN products p ON s.product_id = p.id 
                              ORDER BY s.id DESC";
                      $query = $conn->query($sql);
                      while($row = $query->fetch_assoc()) {
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($row["inventory_selection"]); ?></td>
                      <td><?php echo htmlspecialchars($row["company_name"]); ?></td>
                      <td><?php echo htmlspecialchars($row["product_name"]); ?></td>
                      <td><?php echo htmlspecialchars($row["unit"]); ?></td>
                      <td><?php echo htmlspecialchars($row["spec_name"]); ?></td>
                      <td>
                        <button class="btn btn-primary btn-sm edit-spec" data-id="<?php echo $row['id']; ?>"><i class="fa fa-edit"></i> Edit</button>
                        <button class="btn btn-danger btn-sm delete-spec" data-id="<?php echo $row['id']; ?>"><i class="fa fa-trash"></i> Delete</button>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>


   <?php include 'includes/specification_modal.php'; ?>
  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
  // Dynamic category loading based on inventory selection for Add Modal
  $('#spec_inventory_selection').change(function(){
    var inventory_selection = $(this).val();
    
    // Reset dependent dropdowns
    $('#spec_company_name').html('<option value="">- Select Category -</option>');
    $('#spec_product').html('<option value="">- Select Product -</option>');
    $('#spec_unit').val('');
    
    if(inventory_selection != ''){
      // Show loading state
      $('#spec_company_name').html('<option value="">Loading categories...</option>');
      
      $.ajax({
        type: 'POST',
        url: 'product_add_category.php',
        data: {inventory_selection: inventory_selection},
        dataType: 'json',
        success: function(response){
          console.log('Category response:', response);
          $('#spec_company_name').html(response);
        },
        error: function(xhr, status, error){
          console.error('Category loading error:', error);
          $('#spec_company_name').html('<option value="">Error loading categories</option>');
        }
      });
    }
  });

  // Dynamic product loading based on category for Add Modal
  $('#spec_company_name').change(function(){
    var company_name = $(this).val();
    var inventory_selection = $('#spec_inventory_selection').val();
    
    // Reset dependent dropdowns
    $('#spec_product').html('<option value="">- Select Product -</option>');
    $('#spec_unit').val('');
    
    if(company_name != '' && inventory_selection != ''){
      // Show loading state
      $('#spec_product').html('<option value="">Loading products...</option>');
      
      $.ajax({
        type: 'POST',
        url: 'get_products_by_category.php',
        data: {
          company_name: company_name, 
          inventory_selection: inventory_selection
        },
        dataType: 'json',
        success: function(response){
          console.log('Product response:', response);
          $('#spec_product').html(response);
        },
        error: function(xhr, status, error){
          console.error('Product loading error:', error);
          $('#spec_product').html('<option value="">Error loading products</option>');
        }
      });
    }
  });

  // Get product details when product is selected for Add Modal
  $('#spec_product').change(function(){
    var product_id = $(this).val();
    
    if(product_id != ''){
      $.ajax({
        type: 'POST',
        url: 'get_product_details.php',
        data: {product_id: product_id},
        dataType: 'json',
        success: function(response){
          console.log('Product details response:', response);
          
          if(response.error){
            console.error('Product details error:', response.error);
            $('#spec_unit').val('');
          } else {
            $('#spec_unit').val(response.unit || '');
          }
        },
        error: function(xhr, status, error){
          console.error('Product details loading error:', error);
          $('#spec_unit').val('');
        }
      });
    } else {
      $('#spec_unit').val('');
    }
  });

  // Edit specification
  $(document).on('click', '.edit-spec', function(e){
    e.preventDefault();
    $('#editSpecModal').modal('show');
    var id = $(this).data('id');
    getSpecRow(id);
  });

  // Delete specification
  $(document).on('click', '.delete-spec', function(e){
    e.preventDefault();
    $('#deleteSpecModal').modal('show');
    var id = $(this).data('id');
    getSpecRow(id);
  });
});

function getSpecRow(id){
  $.ajax({
    type: 'POST',
    url: 'specification_row.php',
    data: {id: id},
    dataType: 'json',
    success: function(response){
      console.log('Spec row response:', response);
      
      // Fill edit modal
      $('#edit_spec_id').val(response.id);
      $('#edit_spec_inventory_selection').val(response.inventory_selection).trigger('change');
      
      // Set values with delay to ensure dropdowns are populated
      setTimeout(function(){
        $('#edit_spec_company_name').val(response.company_name).trigger('change');
        
        setTimeout(function(){
          $('#edit_spec_product').val(response.product_id).trigger('change');
          $('#edit_spec_unit').val(response.unit);
          $('#edit_spec_name').val(response.spec_name);
        }, 500);
      }, 500);
      
      // Fill delete modal
      $('#del_spec_id').val(response.id);
      $('#del_spec').html(response.spec_name);
    },
    error: function(xhr, status, error){
      console.error('Spec row loading error:', error);
      alert('Error loading specification details. Please try again.');
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

/* Modal Styling */
.modal-content {
  border-radius: 10px;
}

.modal-header {
  background-color: #f8f9fa;
  border-radius: 10px 10px 0 0;
}

/* Loading States */
.loading {
  color: #999;
  font-style: italic;
}

/* Error States */
.error {
  color: #d9534f;
  font-weight: bold;
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
  
  .modal-dialog {
    margin: 10px;
  }
}
</style>
</body>
</html>