<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Borrow Equipment</h1>
      
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
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Borrow</a>
            </div>
            <div class="box-body">
  <div class="table-responsive">
    <table id="example1" class="table table-bordered table-fit">
      <thead>
        <th class="hidden"></th>
        <th>Date</th>
        <th>Project</th>
        <th>Technician</th>
        <th>Equipment</th>
        <th>No. of Equipment</th>
        <th>Store</th>
        <th>Status</th>
      </thead>
      <tbody>
        <?php
        // Updated SQL query to include project and store columns
        $sql = "SELECT borrow.id, 
                       borrow.date_borrow, 
                       borrow.project,
                       borrow.store,
                       CONCAT(users.firstname, ' ', users.lastname) AS fullname, 
                       books.title AS book_title, 
                       borrow.quantity, 
                       borrow.status AS barstat 
                FROM borrow 
                LEFT JOIN users ON users.id = borrow.user_id
                LEFT JOIN books ON books.id = borrow.book_id 
                ORDER BY borrow.date_borrow DESC, borrow.id DESC";

        $query = $conn->query($sql);
        while ($row = $query->fetch_assoc()) {
            $status = $row['barstat'] ? '<span class="label label-success">Returned</span>' : '<span class="label label-danger">Not Returned</span>';
            echo "
                <tr>
                    <td class='hidden'></td>
                    <td>" . date('M d, Y', strtotime($row['date_borrow'])) . "</td>
                    <td>" . (!empty($row['project']) ? $row['project'] : 'N/A') . "</td>
                    <td>" . $row['fullname'] . "</td>
                    <td>" . $row['book_title'] . "</td>
                    <td><center><b>" . $row['quantity'] . "</b></center></td>
                    <td>" . (!empty($row['store']) ? $row['store'] : 'N/A') . "</td>
                    <td>" . $status . "</td>
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

  <?php include 'includes/borrow_modal.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
 

  $(document).on('click', '#append', function(e){
    e.preventDefault();
    $('#append-div').append(
      `<div class="appended-form-group">
        <div class="form-group">
          <label for="equipment" class="col-sm-3 control-label">Equipment</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="title[]" required>
          </div>
        </div>
        <div class="form-group">
          <label for="quantity" class="col-sm-3 control-label">Quantity</label>
          <div class="col-sm-9">
            <input type="number" class="form-control" name="quantity[]" min="1" max="999" required>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-12 text-right">
            <button type="button" class="btn btn-danger btn-sm remove-appended">Remove</button>
          </div>
        </div>
      </div>`
    );
  });

  // Add remove functionality for appended forms
  $(document).on('click', '.remove-appended', function(e){
    e.preventDefault();
    $(this).closest('.appended-form-group').remove();
  });

});
</script>
<style>
/* Floating Box for the entire table and header */
.floating-box {
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
}

/* Table Row Styling */
.table td {
  padding: 12px 10px; /* Add padding for better spacing */
  text-align: center; /* Center align the data */
  border-top: 1px solid #ddd; /* Border between rows */
  border-bottom: 1px solid #ddd; /* Border between rows */
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

/* Responsive Adjustments */
@media screen and (max-width: 768px) {
  .table-fit th, .table-fit td {
    font-size: 12px; /* Adjust font size for smaller screens */
    padding: 8px; /* Adjust padding for smaller screens */
  }
}
</style>

</body>
</html>