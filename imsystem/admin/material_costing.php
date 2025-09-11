<?php
include 'includes/session.php';
include 'includes/header.php';
?>
<head>
    <link rel="icon" type="image/x-icon" href="rga.png">
    <title>Material Costing</title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Material Costing</h1>
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
              <h3 class="box-title">Material Costing Records</h3>
              <div class="pull-right">
                <a href="material_costing_add.php" class="btn btn-success btn-sm">
                  <i class="fa fa-plus"></i> Add New Costing
                </a>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="costingTable" class="table table-bordered table-striped table-fit">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Project Name</th>
                      <th>Module Title</th>
                      <th>Designer</th>
                      <th>Date</th>
                      <th>Material Cost</th>
                      <th>Labor Cost</th>
                      <th>Grand Total</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i = 1;
                    
                    // Build WHERE clause based on user type
                    $where = "";
                    if ($_SESSION['login_type'] != 1) { // Not admin
                        $user_id = $_SESSION['login_id'];
                        $where = " WHERE created_by = $user_id";
                    }
                    
                    $qry = $conn->query("SELECT * FROM material_costing $where ORDER BY created_at DESC");
                    
                    if ($qry && $qry->num_rows > 0):
                        while($row = $qry->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['module_title'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['designer'] ?? 'N/A'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                            <td class="text-right">₱<?php echo number_format($row['overall_material_cost'], 2); ?></td>
                            <td class="text-right">₱<?php echo number_format($row['labor_total'], 2); ?></td>
                            <td class="text-right text-primary" style="font-weight: bold;">₱<?php echo number_format($row['grand_total'], 2); ?></td>
                            <td>
                                <a href="material_costing_view.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-info btn-xs" title="View Details">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                <a href="material_costing_edit.php?id=<?php echo $row['id']; ?>" 
                                   class="btn btn-warning btn-xs" title="Edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-success btn-xs" 
                                        onclick="printCosting(<?php echo $row['id']; ?>)" title="Print">
                                    <i class="fa fa-print"></i> Print
                                </button>
                                <?php if($_SESSION['login_type'] == 1): // Only admin can delete ?>
                                <button type="button" class="btn btn-danger btn-xs" 
                                        onclick="deleteCosting(<?php echo $row['id']; ?>)" title="Delete">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="9" class="text-center">No material costing records found.</td>
                        </tr>
                    <?php endif; ?>
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

.table-fit {
  width: 100%;
  border-collapse: collapse;
}

.table-fit th {
  background-color: #f8f9fa;
  text-align: center;
  padding: 15px 10px;
  font-weight: bold;
}

.table-fit td {
  padding: 12px 10px;
  text-align: center;
  border-top: 1px solid #ddd;
}

.table-fit tbody tr:hover {
  background-color: #f5f5f5;
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

.box-header.with-border {
  border-bottom: 1px solid #f4f4f4;
  padding: 15px 20px;
}

.box-title {
  font-weight: 600;
  font-size: 18px;
}

.text-primary {
  color: #337ab7 !important;
}

body, .wrapper, .content-wrapper {
    background-color: #f4f1ed !important;
}
</style>

<script>
$(document).ready(function() {
  $('#costingTable').DataTable({
    "responsive": true,
    "autoWidth": false,
    "pageLength": 10,
    "order": [[4, "desc"]], // Order by date descending
    "dom": '<"top"Bf>rt<"bottom"lip><"clear">',
    "buttons": [
      {
        extend: 'excel',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        title: 'Material Costing Records',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7] // Exclude Actions column
        }
      },
      {
        extend: 'print',
        text: '<i class="fa fa-print"></i> Print',
        title: 'Material Costing Records',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7] // Exclude Actions column
        }
      }
    ],
    "language": {
      "emptyTable": "No material costing records found",
      "info": "Showing _START_ to _END_ of _TOTAL_ records",
      "infoEmpty": "Showing 0 to 0 of 0 records",
      "infoFiltered": "(filtered from _MAX_ total records)",
      "search": "Search records:"
    }
  });
});

function printCosting(id) {
    window.open('material_costing_print.php?id=' + id, '_blank');
}

function deleteCosting(id) {
    if (confirm('Are you sure you want to delete this material costing record? This action cannot be undone.')) {
        $.ajax({
            url: 'material_costing_delete.php',
            method: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('An error occurred while deleting the record.');
            }
        });
    }
}
</script>
</body>
</html>