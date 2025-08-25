<?php include 'includes/session.php'; ?>
<?php include 'includes/functions.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Task Evaluation List</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Evaluation List</li>
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

      <div class="switch-container">
        <div class="material-switch">
          <span class="switch-label active" id="woodLabel">Wood</span>
          <label class="toggle-switch">
            <input type="checkbox" id="materialToggle">
            <span class="slider"></span>
          </label>
          <span class="switch-label" id="steelLabel">Steel</span>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">
                <i class="fa fa-list-alt"></i> 
                <span id="materialTitle">Wood</span> - Evaluation Records
              </h3>
              <div class="box-tools pull-right">
                <a href="evaluation_add.php" class="btn btn-primary btn-sm btn-flat">
                  <i class="fa fa-plus"></i> Add New Evaluation
                </a>
              </div>
            </div>
            
            <div class="box-body">
              <!-- Filter Controls -->
              <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-2">
                  <label>Filter by Role:</label>
                  <select class="form-control" id="role_filter">
                    <option value="">All Roles</option>
                    <?php
                    // Wood roles
                    $wood_roles = [
                      'Designer' => 'Designer',
                      'Project Manager' => 'Project Manager',
                      'Estimator' => 'Estimator',
                      'Fabricator' => 'Fabricator',
                      'CNC Operator' => 'CNC Operator',
                      'Painter' => 'Painter',
                      'Electrician' => 'Electrician',
                      'Project Coordinator' => 'Project Coordinator',
                      'Accounting' => 'Accounting'
                    ];
                    
                    // Steel roles
                    $steel_roles = [
                      'HR & Admin' => 'HR & Admin',
                      'Inventory & Logistics' => 'Inventory & Logistics',
                      'Accounting & Receivables' => 'Accounting & Receivables',
                      'Documentation & Projects' => 'Documentation & Projects',
                      'Production / Operations' => 'Production / Operations'
                    ];
                    
                    // Default show wood roles
                    foreach($wood_roles as $key => $value) {
                      echo "<option value='".$key."' class='wood-role'>".$value."</option>";
                    }
                    foreach($steel_roles as $key => $value) {
                      echo "<option value='".$key."' class='steel-role' style='display:none;'>".$value."</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-2">
                  <label>Filter by Color Code:</label>
                  <select class="form-control" id="color_filter">
                    <option value="">All Colors</option>
                    <option value="Green">Green</option>
                    <option value="Yellow">Yellow</option>
                    <option value="Orange">Orange</option>
                    <option value="Red">Red</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label>Filter by Month:</label>
                  <select class="form-control" id="month_filter">
                    <option value="">All Months</option>
                    <?php
                    $month_sql = "SELECT DISTINCT DATE_FORMAT(assigned_date, '%Y-%m') as month_year,
                                         DATE_FORMAT(assigned_date, '%M %Y') as month_name
                                  FROM evaluations 
                                  ORDER BY month_year DESC";
                    
                    $month_query = $conn->query($month_sql);
                    
                    if ($month_query && $month_query->num_rows > 0) {
                        while ($month_row = $month_query->fetch_assoc()) {
                            echo "<option value='" . $month_row['month_year'] . "'>" . $month_row['month_name'] . "</option>";
                        }
                    }
                    ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label>Search:</label>
                  <input type="text" class="form-control" id="search_input" placeholder="Search project, client, or team member...">
                </div>
                <div class="col-md-3">
                  <label>&nbsp;</label><br>
                  <button type="button" class="btn btn-default btn-flat" id="clear_filters">
                    <i class="fa fa-refresh"></i> Clear Filters
                  </button>
                </div>
              </div>

              <!-- Data Table -->
              <div class="table-responsive">
                <table id="evaluation_table" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th style="width: 50px;">#</th>
                      <th>Project Name</th>
                      <th>Client Name</th>
                      <th>Role</th>
                      <th>Team Member Name</th>
                      <th>Task Description</th>
                      <th>Assigned Date</th>
                      <th>Due Date</th>
                      <th>Completion Date</th>
                      <th>On Time</th>
                      <th>QC Passed</th>
                      <th>Overall KPI %</th>
                      <th>Color Code</th>
                      <th>Task Type</th>
                      <th style="width: 100px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT e.*, u2.firstname as created_by_name
                            FROM evaluations e 
                            LEFT JOIN users u2 ON e.created_by = u2.id
                            ORDER BY e.id DESC";
                    
                    $query = $conn->query($sql);
                    $counter = 1;
                    
                    while ($row = $query->fetch_assoc()) {
                      $role_name = $row['role'];
                      $material_type = $row['material_type'] ?? 'Wood'; // Default to Wood if not set
                      
                      $assigned_date = date('M d, Y', strtotime($row['assigned_date']));
                      $due_date = date('M d, Y', strtotime($row['due_date']));
                      $completion_date = $row['actual_completion_date'] ? date('M d, Y', strtotime($row['actual_completion_date'])) : 'Not completed';
                      
                      $assigned_month_year = date('Y-m', strtotime($row['assigned_date']));
                      
                      $kpi_class = '';
                      $kpi_value = $row['overall_kpi_percentage'];
                      if ($kpi_value >= 90) {
                        $kpi_class = 'success';
                      } elseif ($kpi_value >= 75) {
                        $kpi_class = 'warning';
                      } elseif ($kpi_value >= 60) {
                        $kpi_class = 'info';
                      } else {
                        $kpi_class = 'danger';
                      }
                      
                      $color_style = '';
                      switch($row['color_code']) {
                        case 'Green':
                          $color_style = 'background-color: #d4edda; color: #155724; padding: 3px 8px; border-radius: 4px;';
                          break;
                        case 'Yellow':
                          $color_style = 'background-color: #fff3cd; color: #856404; padding: 3px 8px; border-radius: 4px;';
                          break;
                        case 'Orange':
                          $color_style = 'background-color: #ffeaa7; color: #b8860b; padding: 3px 8px; border-radius: 4px;';
                          break;
                        case 'Red':
                          $color_style = 'background-color: #f8d7da; color: #721c24; padding: 3px 8px; border-radius: 4px;';
                          break;
                      }
                      
                      echo "<tr data-role='".$row['role']."' data-color='".$row['color_code']."' data-month='".$assigned_month_year."' data-search='".strtolower($row['project_name'].' '.$row['client_name'].' '.$row['team_member_name'])."' data-material='".$material_type."'>";
                      echo "<td>".$counter."</td>";
                      echo "<td><strong>".$row['project_name']."</strong></td>";
                      echo "<td>".$row['client_name']."</td>";
                      echo "<td><span class='label label-info'>".$role_name."</span></td>";
                      echo "<td>".$row['team_member_name']."</td>";
                      echo "<td>".substr($row['task_description'], 0, 50).(strlen($row['task_description']) > 50 ? '...' : '')."</td>";
                      echo "<td>".$assigned_date."</td>";
                      echo "<td>".$due_date."</td>";
                      echo "<td>".$completion_date."</td>";
                      echo "<td><span class='label label-".($row['on_time'] == 'Yes' ? 'success' : 'danger')."'>".$row['on_time']."</span></td>";
                      echo "<td><span class='label label-".($row['qc_passed'] == 'Yes' ? 'success' : 'danger')."'>".$row['qc_passed']."</span></td>";
                      echo "<td><span class='label label-".$kpi_class."'>".number_format($kpi_value, 2)."%</span></td>";
                      echo "<td><span style='".$color_style."'>".$row['color_code']."</span></td>";
                      echo "<td><span class='label label-primary'>".$row['task_type']."</span></td>";
                      echo "<td>
                              <button class='btn btn-success btn-sm btn-flat view-details' data-id='".$row['id']."' title='View Details'>
                                <i class='fa fa-eye'></i>
                              </button>
                              <button class='btn btn-danger btn-sm btn-flat delete-evaluation' data-id='".$row['id']."' title='Delete'>
                                <i class='fa fa-trash'></i>
                              </button>
                            </td>";
                      echo "</tr>";
                      $counter++;
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

<!-- Evaluation Details Modal -->
<div class="modal fade" id="evaluation_modal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Evaluation Details</h4>
      </div>
      <div class="modal-body" id="modal_content">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Confirm Delete</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this evaluation record?</p>
        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger btn-flat" id="confirm_delete">Delete</button>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(document).ready(function(){
  
  // Initialize filters
  var originalRows = $('#evaluation_table tbody tr').clone();
  var currentMaterial = 'wood'; // Default to wood
  
  // Check for URL parameters and set filters accordingly
  const urlParams = new URLSearchParams(window.location.search);
  
  // Set color filter from URL parameter
  if (urlParams.has('color_filter')) {
    const colorFilter = urlParams.get('color_filter');
    $('#color_filter').val(colorFilter);
    
    // Show a notification that filter has been applied
    if (colorFilter) {
      $('#evaluation_table').before(
        '<div class="alert alert-info alert-dismissible" id="filter-notification" style="margin-bottom: 15px;">' +
          '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
          '<i class="fa fa-filter"></i> Filtered by Color Code: <strong>' + colorFilter + '</strong>' +
        '</div>'
      );
    }
  }
  
  // Set role filter from URL parameter
  if (urlParams.has('role_filter')) {
    const roleFilter = urlParams.get('role_filter');
    $('#role_filter').val(roleFilter);
  }
  
  // Set month filter from URL parameter
  if (urlParams.has('month_filter')) {
    const monthFilter = urlParams.get('month_filter');
    $('#month_filter').val(monthFilter);
  }
  
  // Set search filter from URL parameter
  if (urlParams.has('search')) {
    const searchFilter = urlParams.get('search');
    $('#search_input').val(searchFilter);
  }
  
  // Set material filter from URL parameter
  if (urlParams.has('material')) {
    const materialFilter = urlParams.get('material');
    if (materialFilter === 'steel') {
      $('#materialToggle').prop('checked', true);
      currentMaterial = 'steel';
      updateMaterialDisplay(true);
    }
  }
  
  // Apply filters after setting values
  filterTable();
  
  // Handle material toggle switch
  $('#materialToggle').change(function() {
    currentMaterial = $(this).is(':checked') ? 'steel' : 'wood';
    updateMaterialDisplay($(this).is(':checked'));
    filterTable();
    updateURL();
  });
  
  // Update material display based on toggle state
  function updateMaterialDisplay(isSteel) {
    if (isSteel) {
      $('#woodLabel').removeClass('active');
      $('#steelLabel').addClass('active');
      $('#materialTitle').text('Steel');
      $('.wood-role').hide();
      $('.steel-role').show();
    } else {
      $('#woodLabel').addClass('active');
      $('#steelLabel').removeClass('active');
      $('#materialTitle').text('Wood');
      $('.wood-role').show();
      $('.steel-role').hide();
    }
    $('#role_filter').val(''); // Reset role filter when changing material
  }
  
  // Role filter
  $('#role_filter').change(function(){
    filterTable();
    updateURL();
  });
  
  // Color filter
  $('#color_filter').change(function(){
    filterTable();
    updateURL();
  });
  
  // Month filter
  $('#month_filter').change(function(){
    filterTable();
    updateURL();
  });
  
  // Search input
  $('#search_input').on('input', function(){
    filterTable();
    updateURL();
  });
  
  // Clear filters
  $('#clear_filters').click(function(){
    $('#role_filter').val('');
    $('#color_filter').val('');
    $('#month_filter').val('');
    $('#search_input').val('');
    filterTable();
    updateURL();
    
    // Remove filter notification if it exists
    $('#filter-notification').remove();
  });
  
  // Function to update URL with current filter values
  function updateURL() {
    const roleFilter = $('#role_filter').val();
    const colorFilter = $('#color_filter').val();
    const monthFilter = $('#month_filter').val();
    const searchFilter = $('#search_input').val();
    
    const params = new URLSearchParams();
    
    if (roleFilter) params.set('role_filter', roleFilter);
    if (colorFilter) params.set('color_filter', colorFilter);
    if (monthFilter) params.set('month_filter', monthFilter);
    if (searchFilter) params.set('search', searchFilter);
    params.set('material', currentMaterial);
    
    const newURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState({}, '', newURL);
  }
  
  // Filter function
  function filterTable() {
    var roleFilter = $('#role_filter').val();
    var colorFilter = $('#color_filter').val();
    var monthFilter = $('#month_filter').val();
    var searchFilter = $('#search_input').val().toLowerCase();
    
    $('#evaluation_table tbody tr').each(function(){
      var show = true;
      var $row = $(this);
      
      // Material filter
      if (currentMaterial === 'wood' && $row.data('material') === 'Steel') {
        show = false;
      } else if (currentMaterial === 'steel' && $row.data('material') === 'Wood') {
        show = false;
      }
      
      // Role filter
      if(roleFilter && $row.data('role') != roleFilter) {
        show = false;
      }
      
      // Color filter
      if(colorFilter && $row.data('color') != colorFilter) {
        show = false;
      }
      
      // Month filter
      if(monthFilter && $row.data('month') != monthFilter) {
        show = false;
      }
      
      // Search filter
      if(searchFilter && $row.data('search').indexOf(searchFilter) === -1) {
        show = false;
      }
      
      if(show) {
        $row.show();
      } else {
        $row.hide();
      }
    });
    
    // Update row numbers
    var counter = 1;
    $('#evaluation_table tbody tr:visible').each(function(){
      $(this).find('td:first').text(counter);
      counter++;
    });
    
    // Update results count
    var visibleRows = $('#evaluation_table tbody tr:visible').length;
    var totalRows = $('#evaluation_table tbody tr').length;
    
    // Add or update results info
    if($('#results-info').length === 0) {
      $('#evaluation_table').before('<div id="results-info" class="alert alert-info" style="margin-bottom: 10px;"></div>');
    }
    $('#results-info').html('<i class="fa fa-info-circle"></i> Showing ' + visibleRows + ' of ' + totalRows + ' records');
  }
  
  // View details
  $(document).on('click', '.view-details', function(){
    var id = $(this).data('id');
    
    $.ajax({
      type: 'POST',
      url: 'get_evaluation_details.php',
      data: {id: id},
      success: function(response){
        $('#modal_content').html(response);
        $('#evaluation_modal').modal('show');
      },
      error: function(){
        alert('Error loading evaluation details');
      }
    });
  });
  
  // Delete evaluation
  var deleteId = null;
  $(document).on('click', '.delete-evaluation', function(){
    deleteId = $(this).data('id');
    $('#delete_modal').modal('show');
  });
  
  $('#confirm_delete').click(function(){
    if(deleteId) {
      $.ajax({
        type: 'POST',
        url: 'evaluation_delete.php',
        data: {id: deleteId},
        dataType: 'json',
        success: function(response){
          if(response.success) {
            location.reload();
          } else {
            alert('Error: ' + response.message);
          }
        },
        error: function(){
          alert('Error deleting evaluation');
        }
      });
    }
    $('#delete_modal').modal('hide');
  });
  
  // Tooltip initialization
  $('[title]').tooltip();
  
  // Initialize results count on page load
  filterTable();
});
</script>

<style>
/* Floating Box */
.floating-box {
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Table styling */
#evaluation_table {
  font-size: 14px;
}

#evaluation_table th {
  background-color: #f4f4f4;
  font-weight: bold;
  text-align: center;
  vertical-align: middle;
}

#evaluation_table td {
  vertical-align: middle;
}

/* Button styling */
.btn-sm {
  margin: 1px;
}

/* Filter section styling */
.row {
  margin-bottom: 15px;
}

/* Modal styling */
.modal-lg {
  width: 90%;
}

/* Responsive table */
.table-responsive {
  overflow-x: auto;
}

/* Results info styling */
#results-info {
  border-radius: 4px;
  font-size: 13px;
}

@media screen and (max-width: 768px) {
  #evaluation_table {
    font-size: 12px;
  }
  
  .btn-sm {
    padding: 2px 5px;
  }
  
  .modal-lg {
    width: 95%;
  }
  
  /* Stack filter columns on mobile */
  .col-md-2, .col-md-3 {
    margin-bottom: 10px;
  }
}

/* Background color consistency */
body, .wrapper, .content-wrapper {
    background-color: #f4f1ed !important;
}

/* Label spacing */
.label {
  display: inline-block;
  margin-bottom: 2px;
}

/* Action buttons container */
td:last-child {
  white-space: nowrap;
}

/* Search and filter controls */
.form-control {
  border-radius: 4px;
}

/* Table hover effect */
#evaluation_table tbody tr:hover {
  background-color: #f5f5f5;
}

/* Status indicators */
.label-success { background-color: #5cb85c; }
.label-danger { background-color: #d9534f; }
.label-warning { background-color: #f0ad4e; }
.label-info { background-color: #5bc0de; }
.label-primary { background-color: #337ab7; }

/* Filter section improvements */
.row .col-md-2, .row .col-md-3 {
  padding-left: 5px;
  padding-right: 5px;
}

/* Ensure all filters are visible */
@media screen and (min-width: 992px) {
  .col-md-2 {
    width: 16.66666667%;
    float: left;
  }
  .col-md-3 {
    width: 25%;
    float: left;
  }
}

/* Material switch styles */
.switch-container {
  text-align: center;
  margin: 20px 0;
}

.material-switch {
  display: inline-flex;
  align-items: center;
  gap: 15px;
  background: white;
  padding: 15px 30px;
  border-radius: 50px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  border: 2px solid #e0e0e0;
}

.switch-label {
  font-weight: bold;
  font-size: 16px;
  color: #555;
  transition: color 0.3s ease;
}

.switch-label.active {
  color: #337ab7;
}

.toggle-switch {
  position: relative;
  width: 60px;
  height: 34px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 34px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #337ab7;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

/* Responsive adjustments for switch */
@media screen and (max-width: 768px) {
  .material-switch {
    padding: 10px 20px;
    gap: 10px;
  }
  
  .switch-label {
    font-size: 14px;
  }
  
  .toggle-switch {
    width: 50px;
    height: 28px;
  }
  
  .slider:before {
    height: 20px;
    width: 20px;
  }
  
  input:checked + .slider:before {
    transform: translateX(22px);
  }
}
</style>

</body>
</html>