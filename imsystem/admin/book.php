<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<?php include 'includes/session.php'; ?>
<?php
  $catid = 0;
  $where = '';
  if(isset($_GET['category'])){
    $catid = $_GET['category'];
    $where = 'WHERE books.category_id = '.$catid;
  }

?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Equipment List
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
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
              <div class="box-tools pull-right">
                <form class="form-inline">
                  <div class="form-group">
                    <label>Category: </label>
                    <select class="form-control input-sm" id="select_category">
                      <option value="0">ALL</option>
                      <?php
                        $sql = "SELECT * FROM category";
                        $query = $conn->query($sql);
                        while($catrow = $query->fetch_assoc()){
                          $selected = ($catid == $catrow['id']) ? " selected" : "";
                          echo "
                            <option value='".$catrow['id']."' ".$selected.">".$catrow['name']."</option>
                          ";
                        }
                      ?>
                    </select>
                  </div>
                </form>
              </div>
            </div>
           <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-fit">
                  <thead>
                    <th>Category</th>
                    <th>Equipment</th>
                    <th>No. of Equipment</th>
                    <th>Status</th>
                    <th>Tools</th>
                  </thead>
                  <tbody>
                  <?php
$sql = "SELECT *, books.id AS bookid FROM books LEFT JOIN category ON category.id=books.category_id $where";
$query = $conn->query($sql);
while($row = $query->fetch_assoc()){
  if($row['equip_qty'] == 0){
    $status = '<span class="label label-warning">Not Available</span>';
  } else {
    if($row['status']){
      $status = '<span class="label label-danger">borrowed</span>';
    }
    else{
      $status = '<span class="label label-success">Available</span>';
    }
  }
  echo "
    <tr>
      <td>".$row['name']."</td>
      <td>".$row['title']."</td>
      <td><center><b>".$row['equip_qty']."</b></center></td>
      <td>".$status."</td>
      <td>
        <button class='btn btn-success btn-sm edit btn-flat' data-id='".$row['bookid']."'><i class='fa fa-edit'></i> Edit</button>
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
  <style>
/* Floating Box for the entire row */
.floating-box {
  border-radius: 15px; /* Rounded corners */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Floating shadow effect */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  padding: 20px;
}

/* Apply rounded corners and floating effect to .box */
.box {
  border-radius: 15px; /* Rounded corners */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Floating shadow effect */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Styling for Scrollable Table */
.table-responsive {
  overflow-x: auto; /* Enables horizontal scrolling */
  -webkit-overflow-scrolling: touch; /* Smooth scrolling for mobile */
  border-radius: 15px; /* Rounded corners for the container */
  margin-top: 20px; /* Spacing above the table */
}

/* Ensures the table fits within the screen width */
.table-fit {
  width: 100%; /* Makes the table take up the full width of its container */
  table-layout: auto; /* Columns adjust automatically to fit the content */
}

/* General Table Styling */
.table {
  border-collapse: collapse; /* Combines border spacing for table cells */
  margin: 0;
  padding: 0;
  border-radius: 15px; /* Rounded corners for the table */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Floating shadow effect */
  overflow: hidden; /* Ensures proper border radius for rows */
}

/* Table Header Styling */
.table th {
  background-color: #f8f9fa; /* Light background for header */
  text-align: center; /* Center the header text */
  padding: 15px 10px; /* Add more padding for better appearance */
  font-weight: bold;
  border-top-left-radius: 15px; /* Rounded corners for top left */
  border-top-right-radius: 15px; /* Rounded corners for top right */
  white-space: nowrap; /* Prevent text from wrapping in headers */
}

/* Table Row Styling */
.table td {
  padding: 12px 10px; /* Add padding for better spacing */
  text-align: center; /* Center align the data */
  border-top: 1px solid #ddd; /* Border between rows */
  border-bottom: 1px solid #ddd; /* Border between rows */
  border-left: 1px solid #ddd; /* Border for left side */
  border-right: 1px solid #ddd; /* Border for right side */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  white-space: nowrap; /* Prevent text from wrapping inside cells */
}

/* Highlight the newest entry */
.table tbody tr:first-child {
  background-color: #f0f7ff !important; /* Light blue background for newest entry */
  font-weight: bold;
}

/* Hover Effect for Table Rows */
.table tbody tr:hover {
  background-color: rgb(243, 237, 55); /* Light hover effect for rows */
  cursor: pointer; /* Change cursor to pointer when hovering */
  transform: translateY(-2px); /* Slightly lift the row on hover */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Floating shadow effect */
}

/* Stripe Effect for Rows */
.table-striped tbody tr:nth-child(odd) {
  background-color: #f9f9f9; /* Stripe effect on odd rows */
}

/* Styling for the Table Row Button */
.table .btn {
  border-radius: 8px; /* Rounded corners for buttons inside table */
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow effect */
  transition: all 0.3s ease; /* Smooth transition for hover effects */
}

/* Hover effect for buttons inside table */
.table .btn:hover {
  transform: translateY(-2px); /* Slightly lift button on hover */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15); /* Increase shadow on hover */
}

/* Style for Category Filter Dropdown */
.form-group select {
  border-radius: 8px; /* Rounded corners for the dropdown */
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Floating shadow effect for dropdown */
  padding: 5px 10px; /* Add padding for better appearance */
  transition: all 0.3s ease;
}

/* Hover effect for category dropdown */
.form-group select:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Slightly increased shadow on hover */
}

/* Responsive Adjustments */
@media screen and (max-width: 768px) {
  .table-fit th, .table-fit td {
    font-size: 12px; /* Adjust font size for smaller screens */
    padding: 8px; /* Adjust padding for smaller screens */
  }
} 

</style>

  <?php include 'includes/book_modal.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  $('#select_category').change(function(){
    var value = $(this).val();
    if(value == 0){
      window.location = 'book.php';
    }
    else{
      window.location = 'book.php?category='+value;
    }
  });

  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'book_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.bookid').val(response.bookid);
      $('#edit_isbn').val(response.isbn);
      $('#edit_title').val(response.title);
      $('#catselect').val(response.category_id).html(response.name);
      $('#edit_author').val(response.author);
      $('#edit_publisher').val(response.publisher);
      $('#datepicker_edit').val(response.publish_date);
      $('#del_book').html(response.title);
    }
  });
}
</script>
</body>
</html>
