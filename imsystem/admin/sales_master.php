<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head>
    <link rel="icon" type="image/x-icon" href="rga.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>Withdrawal</h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Sales</li>
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
        <!-- Sales Form -->
        <div class="col-md-4">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Sales Information</h3>
            </div>
            <div class="box-body">
              <form id="salesForm">
                <div class="form-group">
                  <label for="project_name">Project Name:</label>
                  <select class="form-control select2" name="project_name" id="project_name" required style="width: 100%;">
                    <option value="">- Select Project -</option>
                    <?php
                      // Modified query to exclude "Done" projects (status = 5)
                      $sql = "SELECT id, name, full_name, location, description FROM project_list WHERE status IN (1,2,3) ORDER BY name ASC";
                      $query = $conn->query($sql);
                      while($row = $query->fetch_assoc()){
                        echo "<option value='".$row['id']."' 
                              data-customer='".htmlspecialchars($row['full_name'], ENT_QUOTES)."' 
                              data-location='".htmlspecialchars($row['location'], ENT_QUOTES)."' 
                              data-description='".htmlspecialchars($row['description'], ENT_QUOTES)."'>".$row['name']."</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label for="module_title">Module Title:</label>
                  <input type="text" class="form-control" name="module_title" id="module_title" required>
                </div>
                <div class="form-group">
                  <label for="location">Location:</label>
                  <input type="text" class="form-control" name="location" id="location" readonly style="background-color: #f9f9f9;">
                </div>
                <div class="form-group">
                  <label for="remarks">Remarks/Finish:</label>
                  <input type="text" class="form-control" name="remarks" id="remarks" required>
                </div>
                <div class="form-group">
                  <label for="designer">Designer:</label>
                  <input type="text" class="form-control" name="designer" id="designer" readonly style="background-color: #f9f9f9;">
                </div>
                <div class="form-group">
                  <label for="dimension">Dimension:</label>
                  <input type="text" class="form-control" name="dimension" id="dimension" required>
                </div>
                <div class="form-group">
                  <label for="customer_name">Customer Name:</label>
                  <input type="text" class="form-control" name="customer_name" id="customer_name" readonly style="background-color: #f9f9f9;">
                </div>
                
                <div class="form-group">
                  <label for="bill_type">Bill Type:</label>
                  <select class="form-control" name="bill_type" id="bill_type" required>
                    <option value="Cash">Cash</option>
                    <option value="Debit">Debit</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="sale_date">Date:</label>
                  <input type="date" class="form-control" name="sale_date" id="sale_date" value="<?php echo date('Y-m-d'); ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="bill_no">Bill No:</label>
                  <?php 
                    $bill_id = 0;
                    $res = $conn->query("SELECT * FROM billing_header ORDER BY id DESC LIMIT 1");
                    if($row = $res->fetch_assoc()) {
                      $bill_id = $row['id'];
                    }
                    $bill_no = generateBillNo($bill_id);
                  ?>
                  <input type="text" class="form-control" name="bill_no" id="bill_no" value="<?php echo $bill_no; ?>" readonly>
                </div>

              </form>
            </div>
          </div>
        </div>

        <!-- Product Selection -->
        <div class="col-md-8">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Select Materials</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="inventory_selection">Choose Inventory:</label>
                    <select class="form-control" name="inventory_selection" id="inventory_selection" required>
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
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="company_name">Select Category:</label>
                    <select class="form-control" name="company_name" id="company_name" required>
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
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="product_name">Select Material:</label>
                    <select class="form-control" name="product_name" id="product_name" required>
                      <option value="">- Select Material -</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="unit">Select Unit:</label>
                    <select class="form-control" name="unit" id="unit" required>
                      <option value="">- Select Unit -</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="specification">Specification:</label>
                    <select class="form-control" name="specification" id="specification" required>
                      <option value="">- Select Specification -</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" class="form-control" name="price" id="price" value="0" readonly>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" class="form-control" name="quantity" id="quantity" value="1" min="1" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="total">Total:</label>
                    <input type="number" class="form-control" name="total" id="total" value="0" readonly>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-8">
                  <button type="button" class="btn btn-primary btn-block" id="addToCart">
                    <i class="fa fa-cart-plus"></i> Add to Cart
                  </button>
                </div>
                <div class="col-md-4">
                  <button type="button" class="btn btn-info btn-block" id="viewStock">
                    <i class="fa fa-list"></i> View Stock
                  </button>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-md-12">
                  <div id="stock_status" class="mt-2"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cart Section -->
      <div class="row">
        <div class="col-md-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Taken Materials</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-danger btn-sm" id="clearCart">
                  <i class="fa fa-trash"></i> Clear Cart
                </button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive" id="cart_items">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>For</th>
                      <th>Category</th>
                      <th>Material</th>
                      <th>Unit</th>
                      <th>Specification</th>
                      <th>Price</th>
                      <th>Quantity</th>
                      <th>Total</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="cart_body">
                    <!-- Cart items will be populated here -->
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="7" align="right"><strong>Grand Total:</strong></td>
                      <td><span id="grandTotal">0.00</span></td>
                      <td></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <button type="button" class="btn btn-success btn-lg btn-block" id="generateBill">
                    <i class="fa fa-file-text"></i> Create Material Requisition Form
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Receipt Modal -->
  <div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header bg-green">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="receiptModalLabel">Transaction Receipt</h4>
        </div>
        <div class="modal-body" id="receiptModalBody">
          <!-- Receipt content will be inserted here -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="printReceipt()"><i class="fa fa-print"></i> Print Receipt</button>
        </div>
      </div>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
  $(function(){
    // Initialize cart
    if(!sessionStorage.getItem('cart')) {
      sessionStorage.setItem('cart', JSON.stringify([]));
    }
    loadCart();

    // Project selection change event
    $('#project_name').change(function(){
      var projectId = $(this).val();
      var selectedOption = $(this).find('option:selected');
      
      if(projectId != ''){
        // Auto-fill customer name and location from data attributes
        $('#customer_name').val(selectedOption.data('customer'));
        $('#location').val(selectedOption.data('location'));
        
        // Fetch designer information from the project
        $.ajax({
          type: 'POST',
          url: 'get_project_details.php',
          data: {project_id: projectId},
          dataType: 'json',
          success: function(response){
            if(response.success) {
              $('#designer').val(response.designer);
              // Optionally set dimension if available
              if(response.dimension) {
                $('#dimension').val(response.dimension);
              }
            }
          },
          error: function(){
            console.log('Error fetching project details');
          }
        });
      } else {
        // Clear fields when no project selected
        $('#customer_name').val('');
        $('#location').val('');
        $('#designer').val('');
        $('#dimension').val('');
      }
    });

    // Change Inventory Selection
$('#inventory_selection').change(function(){
  var inventory_selection = $(this).val();
  if(inventory_selection != ''){
    $.ajax({
      type: 'POST',
      url: 'sales_category.php',
      data: {inventory_selection: inventory_selection},
      dataType: 'json',
      success: function(response){
        $('#company_name').html(response);
        $('#product_name').html('<option value="">- Select Product -</option>');
        $('#unit').html('<option value="">- Select Unit -</option>');
        $('#specification').html('<option value="">- Select Specification -</option>');
        $('#price').val(0); // Reset price
        $('#stock_status').html('');
        calculateTotal();
      }
    });
  }
  else{
    $('#company_name').html('<option value="">- Select Category -</option>');
    $('#product_name').html('<option value="">- Select Product -</option>');
    $('#unit').html('<option value="">- Select Unit -</option>');
    $('#specification').html('<option value="">- Select Specification -</option>');
    $('#price').val(0);
    $('#stock_status').html('');
    calculateTotal();
  }
});
  // Category change event
$('#company_name').change(function(){
  var company_name = $(this).val();
  if(company_name != ''){
    $.ajax({
      type: 'POST',
      url: 'sales_product.php',
      data: {company_name: company_name},
      dataType: 'json',
      success: function(response){
        $('#product_name').html(response);
        $('#unit').html('<option value="">- Select Unit -</option>');
        $('#specification').html('<option value="">- Select Specification -</option>');
        $('#price').val(0); // Reset price
        $('#stock_status').html('');
        calculateTotal();
      }
    });
  }
  else{
    $('#product_name').html('<option value="">- Select Product -</option>');
    $('#unit').html('<option value="">- Select Unit -</option>');
    $('#specification').html('<option value="">- Select Specification -</option>');
    $('#price').val(0);
    $('#stock_status').html('');
    calculateTotal();
  }
});

   // Product change event
$('#product_name').change(function(){
  var product_name = $(this).val();
  var company_name = $('#company_name').val();
  if(product_name != ''){
    $.ajax({
      type: 'POST',
      url: 'sales_unit.php',
      data: {
        product_name: product_name,
        company_name: company_name
      },
      dataType: 'json',
      success: function(response){
        $('#unit').html(response);
        $('#price').val(0); // Reset price
        $('#stock_status').html('');
        calculateTotal();
        
        // Load specifications for this product
        loadSpecifications(product_name, company_name);
      }
    });
  }
  else{
    $('#unit').html('<option value="">- Select Unit -</option>');
    $('#specification').html('<option value="">- Select Specification -</option>');
    $('#price').val(0);
    $('#stock_status').html('');
    calculateTotal();
  }
});

// Function to load specifications
function loadSpecifications(product_name, company_name) {
  if(product_name && company_name) {
    // Show loading in specification dropdown
    $('#specification').html('<option value="">Loading specifications...</option>');
    
    $.ajax({
      type: 'POST',
      url: 'get_specifications.php',
      data: {
        product_name: product_name,
        company_name: company_name
      },
      dataType: 'json',
      success: function(response){
        $('#specification').html(response);
        
        // If there's only one specification (excluding default), select it automatically
        if($('#specification option').length === 2) {
          $('#specification').val($('#specification option:last').val()).trigger('change');
        } else {
          // Trigger change to check stock with the selected specification
          $('#specification').trigger('change');
        }
      },
      error: function(){
        $('#specification').html('<option value="">- Select Specification -</option>');
        // Check stock even if no specifications are available
        checkStockStatus();
      }
    });
  }
}
// Unit change event - DON'T display price when unit is selected
$('#unit').change(function(){
  var unit = $(this).val();
  var product_name = $('#product_name').val();
  var company_name = $('#company_name').val();
  
  if(unit != ''){
    // Reset price to 0 when unit changes
    $('#price').val(0);
    calculateTotal();
    
    // Clear stock status
    $('#stock_status').html('');
    
    // Only check stock if specification is already selected
    var specification = $('#specification').val();
    if(specification && specification !== '' && specification !== '0' && specification !== '- Select Specification -') {
      checkStockStatus();
      
      // Update price based on specification (not unit)
      updatePriceBySpecification();
    }
  }
  else{
    $('#price').val(0);
    $('#stock_status').html('');
    calculateTotal();
  }
});
// Update the specification change event
$('#specification').change(function(){
  var spec_id = $(this).val();
  var spec_text = $(this).find('option:selected').text();
  
  // Update price based on specification
  updatePriceBySpecification();
  
  // Check stock when specification changes
  checkStockStatus();
});
    // Quantity change event
    $('#quantity').on('input', function(){
      calculateTotal();
      checkStockStatus();
    });
    
    // Function to load specifications
    function loadSpecifications(product_name, company_name) {
      if(product_name && company_name) {
        $.ajax({
          type: 'POST',
          url: 'get_specifications.php',
          data: {
            product_name: product_name,
            company_name: company_name
          },
          dataType: 'json',
          success: function(response){
            $('#specification').html(response);
          },
          error: function(){
            $('#specification').html('<option value="">- Select Specification -</option>');
          }
        });
      }
    }
   // Function to update price based on specification
function updatePriceBySpecification() {
  var company_name = $('#company_name').val();
  var product_name = $('#product_name').val();
  var unit = $('#unit').val();
  var specification = $('#specification').val();
  
  if(company_name && product_name && unit && specification && 
     specification !== '' && specification !== '0' && specification !== '- Select Specification -') {
    
    $.ajax({
      type: 'POST',
      url: 'get_price_by_specification.php',
      data: {
        company_name: company_name,
        product_name: product_name,
        unit: unit,
        specification: specification
      },
      dataType: 'json',
      success: function(response) {
        if(response.success && response.price) {
          $('#price').val(response.price);
          calculateTotal();
        } else {
          // If no specific price found, keep price at 0
          $('#price').val(0);
          calculateTotal();
        }
      },
      error: function() {
        // If error occurs, keep price at 0
        $('#price').val(0);
        calculateTotal();
        console.log('Error fetching price for specification');
      }
    });
  }
}
// Update the checkStockStatus function to ensure proper parameter passing
function checkStockStatus() {
  var company_name = $('#company_name').val();
  var product_name = $('#product_name').val();
  var unit = $('#unit').val();
  var specification = $('#specification').val();
  var quantity = $('#quantity').val();
  
  // Only check if all required fields are filled
  if(company_name && product_name && unit) {
    
    // Show loading indicator
    $('#stock_status').html('<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Checking stock...</div>');
    
    $.ajax({
      type: 'POST',
      url: 'check_stock_status.php',
      data: {
        company_name: company_name,
        product_name: product_name,
        unit: unit,
        specification: specification,
        quantity: quantity
      },
      dataType: 'json',
      success: function(response){
        if(response.success) {
          var available = parseInt(response.available);
          var requested = parseInt(quantity);
          
          // Update the display logic here
          if(available <= 0) {
            $('#stock_status').html('<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Out of Stock</div>');
            $('#addToCart').prop('disabled', true);
          }
          else if(available < requested) {
            $('#stock_status').html('<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Insufficient Stock! Only ' + available + ' units available.</div>');
            $('#addToCart').prop('disabled', true);
          }
          else if(available <= 5) {
            $('#stock_status').html('<div class="alert alert-warning"><i class="fa fa-info-circle"></i> Low Stock! Only ' + available + ' units available.</div>');
            $('#addToCart').prop('disabled', false);
          }
          else {
            $('#stock_status').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> In Stock: ' + available + ' units available</div>');
            $('#addToCart').prop('disabled', false);
          }
        } else {
          $('#stock_status').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + response.message + '</div>');
          $('#addToCart').prop('disabled', true);
        }
      },
      error: function(){
        $('#stock_status').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error checking stock! Please try again.</div>');
      }
    });
  } else {
    // Clear stock status if not all fields are filled
    $('#stock_status').html('');
  }
}
// Add to cart button click
$('#addToCart').click(function(){
  var project_name_text = $('#project_name option:selected').text();
  var inventory_selection = $('#inventory_selection').val();
  var company_name = $('#company_name').val();
  var product_name = $('#product_name').val();
  var unit = $('#unit').val();
  var specification = $('#specification').val();
  var specification_text = $('#specification option:selected').text();
  var price = $('#price').val();
  var quantity = $('#quantity').val();
  var total = $('#total').val();

  // Check if specification is selected
  if(!specification || specification === '' || specification === '0' || specification === '- Select Specification -') {
    alert('Please select a specification before adding to cart.');
    $('#specification').focus();
    return;
  }

  if($('#project_name').val() == '' || inventory_selection == '' || company_name == '' || product_name == '' || unit == '' || specification == '' || quantity == '' || quantity < 1){
    alert('Please fill all required fields including project selection and specification');
    return;
  }

  // Check stock availability with specification
  $.ajax({
    type: 'POST',
    url: 'check_stock_status.php',
    data: {
      company_name: company_name,
      product_name: product_name,
      unit: unit,
      specification: specification,
      quantity: quantity
    },
    dataType: 'json',
    success: function(response){
      if(response.success){
        var available = parseInt(response.available);
        var requested = parseInt(quantity);
        
        if(available >= requested) {
          // Stock is available, proceed to add to cart
          var cart = JSON.parse(sessionStorage.getItem('cart'));
          
          // Check if item already exists in cart
          var existingItemIndex = -1;
          for(var i = 0; i < cart.length; i++) {
            if(cart[i].company_name === company_name && 
               cart[i].product_name === product_name && 
               cart[i].unit === unit &&
               cart[i].specification === specification) {
              existingItemIndex = i;
              break;
            }
          }
          
          if(existingItemIndex !== -1) {
            // Update existing item
            var newQuantity = parseInt(cart[existingItemIndex].quantity) + parseInt(quantity);
            
            // Check if stock is sufficient for combined quantity
            if(newQuantity > available) {
              alert('Cannot add more of this item. Insufficient stock!');
              return;
            }
            
            cart[existingItemIndex].quantity = newQuantity;
            cart[existingItemIndex].total = parseFloat(cart[existingItemIndex].price) * newQuantity;
          } else {
            // Add new item
            var itemId = Date.now();
            
            cart.push({
              id: itemId,
              inventory_selection: inventory_selection,
              company_name: company_name,
              product_name: product_name,
              unit: unit,
              specification: specification,
              specification_text: specification_text,
              price: parseFloat(price),
              quantity: parseInt(quantity),
              total: parseFloat(total)
            });
          }
          
          sessionStorage.setItem('cart', JSON.stringify(cart));
          
          // Reset product selection fields only, keep project info
          $('#inventory_selection').val('').trigger('change');
          $('#price').val(0);
          $('#quantity').val(1);
          $('#total').val(0);
          $('#stock_status').html('');
          
          // Reload cart
          loadCart();
          
          alert('Product added to cart!');
        } else {
          // Not enough stock
          alert('Insufficient stock! Only ' + available + ' units available.');
          $('#quantity').val(available).focus();
          calculateTotal();
        }
      } else {
        // Product not found or error
        alert(response.message);
      }
    },
    error: function(){
      alert('An error occurred while checking stock. Please try again.');
    }
  });
});

    // Generate bill button click
    $('#generateBill').click(function(){
      var project_id = $('#project_name').val();
      var project_name = $('#project_name option:selected').text();
      var module_title = $('#module_title').val();
      var location = $('#location').val();
      var remarks = $('#remarks').val();
      var designer = $('#designer').val();
      var dimension = $('#dimension').val();
      var customer_name = $('#customer_name').val();
      var bill_type = $('#bill_type').val();
      var bill_no = $('#bill_no').val();
      var sale_date = $('#sale_date').val();

      var cart = JSON.parse(sessionStorage.getItem('cart'));
      
      if(project_id == ''){
        alert('Please select a project');
        $('#project_name').focus();
        return;
      }
      
      if(customer_name == ''){
        alert('Please ensure project has customer name');
        return;
      }
      
      if(cart.length == 0){
        alert('Cart is empty. Please add products.');
        return;
      }
      
      // Confirm before generating bill
      if(!confirm('Are you sure you want to generate bill for project: ' + project_name + '?')) {
        return;
      }
      
      // Show loading indicator
      var $generateBtn = $('#generateBill');
      $generateBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
      
      // Prepare data for submission
      var formData = {
        project_id: project_id,
        project_name: project_name,
        module_title: module_title,
        location: location, 
        remarks: remarks,
        designer: designer,
        dimension: dimension,
        customer_name: customer_name,
        bill_type: bill_type,
        bill_no: bill_no,
        sale_date: sale_date,
        cart: cart
      };
      
      // Submit data to server
      $.ajax({
        type: 'POST',
        url: 'sales_process.php',
        data: {
          sales_data: JSON.stringify(formData)
        },
        dataType: 'json',
        success: function(response){
          if(response.success){
            // Display receipt in modal
            $('#receiptModalBody').html(response.receipt_html);
            $('#receiptModal').modal('show');
            
            // Clear cart and reload
            sessionStorage.setItem('cart', JSON.stringify([]));
            loadCart();
            
            // Reset form
            $('#salesForm')[0].reset();
            $('#sale_date').val('<?php echo date("Y-m-d"); ?>');
            $('#bill_no').val(response.next_bill_no);
          } else {
            alert('Error: ' + response.message);
          }
          $generateBtn.html('<i class="fa fa-file-text"></i> Generate Bill').prop('disabled', false);
        },
        error: function(){
          alert('An error occurred. Please try again.');
          $generateBtn.html('<i class="fa fa-file-text"></i> Generate Bill').prop('disabled', false);
        }
      });
    });

    // Remove item from cart
    $(document).on('click', '.removeItem', function(){
      var itemId = $(this).data('id');
      var cart = JSON.parse(sessionStorage.getItem('cart'));
      
      cart = cart.filter(function(item){
        return item.id != itemId;
      });
      
      sessionStorage.setItem('cart', JSON.stringify(cart));
      loadCart();
    });
    
    // Edit item quantity
    $(document).on('click', '.editItem', function(){
      var itemId = $(this).data('id');
      var cart = JSON.parse(sessionStorage.getItem('cart'));
      var item = cart.find(item => item.id == itemId);
      
      if(item) {
        var newQuantity = prompt('Enter new quantity:', item.quantity);
        if(newQuantity !== null) {
          newQuantity = parseInt(newQuantity);
          
          if(isNaN(newQuantity) || newQuantity <= 0) {
            alert('Please enter a valid quantity.');
            return;
          }
          
          // Check stock for new quantity
          $.ajax({
            type: 'POST',
            url: 'check_stock_status.php',
            data: {
              company_name: item.company_name,
              product_name: item.product_name,
              unit: item.unit,
              quantity: newQuantity
            },
            dataType: 'json',
            success: function(response){
              if(response.success && response.available >= newQuantity) {
                // Update item
                for(var i = 0; i < cart.length; i++) {
                  if(cart[i].id == itemId) {
                    cart[i].quantity = newQuantity;
                    cart[i].total = cart[i].price * newQuantity;
                    break;
                  }
                }
                
                sessionStorage.setItem('cart', JSON.stringify(cart));
                loadCart();
              } else {
                alert('Cannot update quantity. Insufficient stock!');
              }
            },
            error: function(){
              alert('An error occurred while checking stock. Please try again.');
            }
          });
        }
      }
    });
    
    // Clear cart button
    $('#clearCart').click(function(){
      if(confirm('Are you sure you want to clear the cart?')) {
        sessionStorage.setItem('cart', JSON.stringify([]));
        loadCart();
      }
    });
    
    // View Stock button click
    $('#viewStock').click(function(){
      var selectedCategory = $('#company_name').val();
      var url = 'view_stock.php';
      
      if(selectedCategory) {
        url += '?category=' + encodeURIComponent(selectedCategory);
      }
      
      window.open(url, '_blank');
    });
  });

  // Calculate total price
  function calculateTotal(){
    var price = parseFloat($('#price').val()) || 0;
    var quantity = parseInt($('#quantity').val()) || 0;
    var total = price * quantity;
    $('#total').val(total.toFixed(2));
  }

// Load cart items
function loadCart(){
  var cart = JSON.parse(sessionStorage.getItem('cart'));
  var html = '';
  var grandTotal = 0;
  
  if(cart.length > 0){
    cart.forEach(function(item){
      html += '<tr>';
      html += '<td>' + item.inventory_selection + '</td>';
      html += '<td>' + item.company_name + '</td>';
      html += '<td>' + item.product_name + '</td>';
      html += '<td>' + item.unit + '</td>';
      html += '<td>' + (item.specification_text || 'N/A') + '</td>'; // Use specification_text
      html += '<td>' + item.price.toFixed(2) + '</td>';
      html += '<td>' + item.quantity + '</td>';
      html += '<td>' + item.total.toFixed(2) + '</td>';
      html += '<td>';
      html += '<button type="button" class="btn btn-primary btn-sm editItem" data-id="' + item.id + '"><i class="fa fa-edit"></i></button> ';
      html += '<button type="button" class="btn btn-danger btn-sm removeItem" data-id="' + item.id + '"><i class="fa fa-trash"></i></button>';
      html += '</td>';
      html += '</tr>';
      
      grandTotal += parseFloat(item.total);
    });
  } else {
    html = '<tr><td colspan="9" class="text-center">No items in cart</td></tr>';
  }
  
  $('#cart_body').html(html);
  $('#grandTotal').text(grandTotal.toFixed(2));
}
  // Print receipt function
  function printReceipt() {
      // Get the receipt HTML
      var receiptContent = $('#receiptModalBody').html();
      
      // Create a temporary iframe for printing
      var iframe = document.createElement('iframe');
      iframe.style.position = 'absolute';
      iframe.style.left = '-9999px';
      document.body.appendChild(iframe);
      
      var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
      iframeDoc.open();
      iframeDoc.write('<html><head><title>Receipt</title>');
      iframeDoc.write('<style>body { font-family: Arial; margin: 20px; }');
      iframeDoc.write('table { width: 100%; border-collapse: collapse; }');
      iframeDoc.write('th, td { border: 1px solid #000; padding: 8px; text-align: left; }');
      iframeDoc.write('.text-right { text-align: right; }');
      iframeDoc.write('</style></head><body>');
      iframeDoc.write(receiptContent);
      iframeDoc.write('</body></html>');
      iframeDoc.close();
      
      // Print and clean up
      setTimeout(function() {
          iframe.contentWindow.focus();
          iframe.contentWindow.print();
          document.body.removeChild(iframe);
      }, 300);
  }
</script>

<?php
function generateBillNo($id) {
  $nextId = $id + 1;
  $len = strlen($nextId);
  
  if($len == 1) {
    return "0000" . $nextId;
  } else if($len == 2) {
    return "000" . $nextId;
  } else if($len == 3) {
    return "00" . $nextId;
  } else if($len == 4) {
    return "0" . $nextId;
  } else {
    return $nextId;
  }
}
?>

<style>
/* Enhanced styling for readonly fields */
.form-control[readonly] {
  background-color: #f9f9f9 !important;
  cursor: not-allowed;
  opacity: 0.8;
}

/* Project info styling */
.project-info-section {
  background-color: #f8f9fa;
  padding: 10px;
  border-radius: 5px;
  border-left: 4px solid #007bff;
  margin-bottom: 15px;
}

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
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Receipt Styling */
.receipt-container {
  font-family: Arial, sans-serif;
  font-size: 14px;
}

.receipt-container h2 {
  margin-top: 0;
  margin-bottom: 20px;
  font-size: 18px;
  font-weight: bold;
  text-align: center;
}

.receipt-container table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 15px;
}

.receipt-container table th, 
.receipt-container table td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

.receipt-container .text-right {
  text-right;
}

.receipt-info {
  margin-top: 20px;
}

.receipt-info p {
  margin-bottom: 5px;
}

.mt-10 {
  margin-top: 10px;
}

/* Responsive Adjustments */
@media screen and (max-width: 768px) {
  .table th, .table td {
    font-size: 12px;
    padding: 8px;
  }
  
  .form-group label {
    text-align: left;
  }
}

/* Alert styling */
.alert {
  border-radius: 8px;
  padding: 10px 15px;
  margin-bottom: 15px;
}

/* Margin top utility */
.mt-2 {
  margin-top: 10px;
}

/* Auto-filled field indication */
.auto-filled {
  background-color: #e8f5e8 !important;
  border-color: #28a745;
}

.manual-entry {
  background-color: #fff3cd !important;
  border-color: #ffc107;
}
</style>
</body>
</html>