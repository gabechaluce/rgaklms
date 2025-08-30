<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Manage Suppliers</h1>
      
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
              <h3 class="box-title">Supplier List</h3>
              <div class="pull-right">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addnew"><i class="fa fa-plus"></i> Add New Supplier</button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-fit">
               <!-- In the table header -->
<thead>
  <tr>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Company Name</th>
    <th>Contact</th>
    <th>Address</th>
    <th>City</th>
    <th>Remarks</th>
    <th>Actions</th>
  </tr>
</thead>

<!-- In the table body -->
<tbody>
  <?php
    $sql = "SELECT * FROM party_info ORDER BY id DESC";
    $query = $conn->query($sql);
    while($row = $query->fetch_assoc())
    {
  ?>
  <tr>
    <td><?php echo $row["firstname"]; ?></td>
    <td><?php echo $row["lastname"]; ?></td>
    <td><?php echo $row["businessname"]; ?></td>
    <td><?php echo $row["contact"]; ?></td>
    <td><?php echo $row["address"]; ?></td>
    <td><?php echo $row["city"]; ?></td>
    <td><?php echo !empty($row["remarks"]) ? $row["remarks"] : 'No remarks'; ?></td>
    <td>
      <a href="supplier_view.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm view" data-id="<?php echo $row['id']; ?>"><i class="fa fa-eye"></i> View</a>
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
  
  <?php include 'includes/footer.php'; ?>
  <?php include 'includes/supplier_add_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){

  // Edit supplier
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  // Delete supplier
  $(document).on('click', '.delete', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    $('#delete').modal('show');
    getRow(id);
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'supplier_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.supplierid').val(response.id);
      $('#edit_firstname').val(response.firstname);
      $('#edit_lastname').val(response.lastname);
      $('#edit_businessname').val(response.businessname);
      $('#edit_contact').val(response.contact);
      $('#edit_address').val(response.address);
      $('#edit_city').val(response.city);
      $('#edit_remarks').val(response.remarks);
      $('#del_supplier').html(response.businessname);
    }
  });
}
</script>
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
</style>
</body>
</html>