<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Track List</h1>
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
              <h3 class="box-title">Track Details</h3>
              <div class="pull-right">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addtrack"><i class="fa fa-plus"></i> Add New Track</button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="example1" class="table table-bordered table-fit">
                  <thead>
                    <th class="hidden"></th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Destination</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Actions</th>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT * FROM track";
                    $query = $conn->query($sql);
                    while ($row = $query->fetch_assoc()) {
                        // Convert time to 12-hour format
                        $time_12h = date("h:i A", strtotime($row['time']));
                        
                        echo "
                            <tr>
                                <td class='hidden'></td>
                                <td>" . date("M d, Y", strtotime($row['date'])) . "</td>
                                <td>" . $time_12h . "</td>
                                <td>" . $row['destination'] . "</td>
                                <td>" . $row['vehicle'] . "</td>
                                <td>" . $row['driver'] . "</td>
                                <td>
                                    <button class='btn btn-success btn-sm edit btn-flat' data-id='" . $row['id'] . "'><i class='fa fa-edit'></i> Edit</button>
                                    <button class='btn btn-danger btn-sm delete btn-flat' data-id='" . $row['id'] . "'><i class='fa fa-trash'></i> Delete</button>
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

  <?php include 'includes/track_modal.php'; ?>
  
  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<script>
$(function(){
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edittrack').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#deletetrack').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'track_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('.trackid').val(response.id);
      $('#edit_date').val(response.date);
      $('#edit_time').val(response.time);
      $('#edit_destination').val(response.destination);
      $('#edit_address').val(response.address);
      $('#edit_purpose').val(response.purpose);
      $('#edit_vehicle').val(response.vehicle);
      $('#edit_driver').val(response.driver);
      $('#edit_dept').val(response.dept);
      $('#del_track').html(response.destination);
    }
  });
}
</script>

<style>
/* (Using the exact same style as in the category list document) */
.floating-box {
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

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
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.table th {
  background-color: #f8f9fa;
  text-align: center;
  padding: 15px 10px;
  font-weight: bold;
  border-top-left-radius: 15px;
  border-top-right-radius: 15px;
}

.table td {
  padding: 12px 10px;
  text-align: center;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  white-space: nowrap;
}

.table tbody tr:hover {
  background-color: rgb(243, 237, 55);
  cursor: pointer;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table-striped tbody tr:nth-child(odd) {
  background-color: #f9f9f9;
}

.table .btn {
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.table .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

@media screen and (max-width: 768px) {
  .table-fit th, .table-fit td {
    font-size: 12px;
    padding: 8px;
  }
}
</style>

</body>
</html>