<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Task Evaluation Form</h1>
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
              <h3 class="box-title">Task Evaluation</h3>
            </div>
            <div class="box-body">
              <form class="form-horizontal" method="POST" action="evaluation_add.php">
                
                <!-- KPI Type Selection -->
                <div class="form-group">
                  <label for="material_type" class="col-sm-3 control-label">Select KPI (STEEL / WOOD)</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="material_type" name="material_type" required>
                      <option value="">Select KPI Type</option>
                      <option value="Wood">Wood</option>
                      <option value="Steel">Steel</option>
                    </select>
                  </div>
                </div>
                
                <!-- Project and Client Information -->
                <div class="form-group">
                  <label for="project_name" class="col-sm-3 control-label">Project Name</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="project_name" name="project_name" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_name" class="col-sm-3 control-label">Client Name</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="client_name" name="client_name" required>
                  </div>
                </div>

                <!-- Role and Team Member Information -->
                <div class="form-group">
                  <label for="role" class="col-sm-3 control-label">Role</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="role" name="role" required>
                      <option value="">Select Role</option>
                      <!-- Options will be populated by JavaScript -->
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="team_member_name" class="col-sm-3 control-label">Team Member Name</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="team_member_name" name="team_member_name" required>
                  </div>
                </div>

                <!-- Task Information -->
                <div class="form-group">
                  <label for="task_description" class="col-sm-3 control-label">Task Description</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="task_description" name="task_description" rows="3" required></textarea>
                  </div>
                </div>

                <!-- Date Information -->
                <div class="form-group">
                  <label for="assigned_date" class="col-sm-3 control-label">Assigned Date</label>
                  <div class="col-sm-9">
                    <input type="date" class="form-control" id="assigned_date" name="assigned_date" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="due_date" class="col-sm-3 control-label">Due Date</label>
                  <div class="col-sm-9">
                    <input type="date" class="form-control" id="due_date" name="due_date" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="actual_completion_date" class="col-sm-3 control-label">Actual Completion Date</label>
                  <div class="col-sm-9">
                    <input type="date" class="form-control" id="actual_completion_date" name="actual_completion_date">
                  </div>
                </div>

                <!-- Performance Indicators -->
                <div class="form-group">
                  <label for="on_time" class="col-sm-3 control-label">On-Time?</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="on_time" name="on_time" required>
                      <option value="">Select</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="revisions_errors" class="col-sm-3 control-label">Revisions/Errors</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="revisions_errors" name="revisions_errors" rows="2"></textarea>
                  </div>
                </div>

                <div class="form-group">
                  <label for="error_category" class="col-sm-3 control-label">Error/Revision Category</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="error_category" name="error_category">
                      <option value="">Select Category</option>
                           <option value="None">None</option>
                      <option value="Design Issue">Design Issue</option>
                      <option value="Fabrication Issue">Fabrication Issue</option>
                      <option value="Material Issue">Material Issue</option>
                      <option value="Client Change">Client Change</option>
                      <option value="Planning Issue">Planning Issue</option>
                      <option value="Quality Issue">Quality Issue</option>
                      <option value="Communication Issue">Communication Issue</option>
                      <option value="Supplier Issue">Supplier Issue</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="qc_passed" class="col-sm-3 control-label">QC Passed?</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="qc_passed" name="qc_passed" required>
                      <option value="">Select</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="material_used" class="col-sm-3 control-label">Material Used?</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="material_used" name="material_used" required>
                      <option value="">Select</option>
                      <option value="Yes">Yes</option>
                      <option value="No">No</option>
                    </select>
                  </div>
                </div>

                <!-- Material and Cost Information -->
                <div class="form-group">
                  <label for="waste_quantity" class="col-sm-3 control-label">Waste Quantity</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="waste_quantity" name="waste_quantity">
                  </div>
                </div>

                <div class="form-group">
                  <label for="cost_per_unit" class="col-sm-3 control-label">Cost per Unit (₱)</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" class="form-control" id="cost_per_unit" name="cost_per_unit">
                  </div>
                </div>

                <div class="form-group">
                  <label for="reason_for_waste" class="col-sm-3 control-label">Reason for Waste</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="reason_for_waste" name="reason_for_waste" rows="2"></textarea>
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_feedback" class="col-sm-3 control-label">Client Feedback (PM Only)</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="client_feedback" name="client_feedback" rows="3"></textarea>
                  </div>
                </div>

                <!-- Client Satisfaction Score (Wood only) -->
                <div id="wood_fields" style="display: none;">
                  <div class="form-group">
                    <label for="client_satisfaction_score" class="col-sm-3 control-label">Client Satisfaction Score (CSAT %)</label>
                    <div class="col-sm-9">
                      <input type="number" step="0.01" min="0" max="100" class="form-control" id="client_satisfaction_score" name="client_satisfaction_score">
                      <small class="help-block">Percentage score from client feedback (0-100%)</small>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="note_issues" class="col-sm-3 control-label">Note/Issues</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" id="note_issues" name="note_issues" rows="3"></textarea>
                  </div>
                </div>

                <!-- Planned Material Quantity (Wood only) -->
                <div id="wood_fields2" style="display: none;">
                  <div class="form-group">
                    <label for="planned_material_quantity" class="col-sm-3 control-label">Planned Material Quantity</label>
                    <div class="col-sm-9">
                      <input type="number" step="0.01" class="form-control" id="planned_material_quantity" name="planned_material_quantity">
                    </div>
                  </div>
                </div>

                <!-- Task Type -->
                <div class="form-group">
                  <label for="task_type" class="col-sm-3 control-label">Task Type</label>
                  <div class="col-sm-9">
                    <select class="form-control" id="task_type" name="task_type" required>
                      <option value="">Select Task Type</option>
                      <option value="Individual">Individual</option>
                      <option value="Team">Team</option>
                    </select>
                  </div>
                </div>

                <!-- Steel-specific fields (hidden by default) -->
                <div id="steel_fields" style="display: none;">
                  <div class="form-group">
                    <label for="production_efficiency_percentage" class="col-sm-3 control-label">Production Efficiency %</label>
                    <div class="col-sm-9">
                      <input type="number" step="0.01" min="0" max="100" class="form-control" id="production_efficiency_percentage" name="production_efficiency_percentage">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="yield_percentage" class="col-sm-3 control-label">Yield %</label>
                    <div class="col-sm-9">
                      <input type="number" step="0.01" min="0" max="100" class="form-control" id="yield_percentage" name="yield_percentage">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="scrap_rate_percentage" class="col-sm-3 control-label">Scrap Rate %</label>
                    <div class="col-sm-9">
                      <input type="number" step="0.01" min="0" max="100" class="form-control" id="scrap_rate_percentage" name="scrap_rate_percentage">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="equipment_utilization_percentage" class="col-sm-3 control-label">Equipment Utilization %</label>
                    <div class="col-sm-9">
                      <input type="number" step="0.01" min="0" max="100" class="form-control" id="equipment_utilization_percentage" name="equipment_utilization_percentage">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="energy_consumption" class="col-sm-3 control-label">Energy Consumption per Ton</label>
                    <div class="col-sm-9">
                      <input type="number" step="0.01" class="form-control" id="energy_consumption" name="energy_consumption">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="safety_score_percentage" class="col-sm-3 control-label">Safety Score %</label>
                    <div class="col-sm-9">
                      <input type="number" step="0.01" min="0" max="100" class="form-control" id="safety_score_percentage" name="safety_score_percentage">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="inventory_turnover_rate" class="col-sm-3 control-label">Inventory Turnover Rate</label>
                    <div class="col-sm-9">
                      <input type="number" step="0.01" class="form-control" id="inventory_turnover_rate" name="inventory_turnover_rate">
                    </div>
                  </div>
                </div>
                <!-- Performance Percentages -->
                <div class="form-group">
                  <label for="output_percentage" class="col-sm-3 control-label">Output %</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="output_percentage" name="output_percentage">
                    <small class="help-block" id="output_weight">Weight: 25%</small>
                  </div>
                </div>

                <div class="form-group">
                  <label for="timeliness_percentage" class="col-sm-3 control-label">Timeliness %</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="timeliness_percentage" name="timeliness_percentage">
                    <small class="help-block" id="timeliness_weight">Weight: 25%</small>
                  </div>
                </div>

                <div class="form-group">
                  <label for="accuracy_percentage" class="col-sm-3 control-label">Accuracy %</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="accuracy_percentage" name="accuracy_percentage">
                    <small class="help-block" id="accuracy_weight">Weight: 35%</small>
                  </div>
                </div>

                <div class="form-group">
                  <label for="teamwork_percentage" class="col-sm-3 control-label">Teamwork %</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="teamwork_percentage" name="teamwork_percentage">
                    <small class="help-block" id="teamwork_weight">Weight: 15%</small>
                  </div>
                </div>

                <!-- Material Efficiency (only for specific roles) -->
                <div class="form-group" id="material_efficiency_group" style="display: none;">
                  <label for="material_efficiency_percentage" class="col-sm-3 control-label">Material Efficiency %</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="material_efficiency_percentage" name="material_efficiency_percentage">
                    <small class="help-block" id="material_efficiency_weight">Weight: 0%</small>
                  </div>
                </div>
                

                <div class="form-group">
                  <label for="overall_kpi_percentage" class="col-sm-3 control-label">Overall KPI %</label>
                  <div class="col-sm-9">
                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="overall_kpi_percentage" name="overall_kpi_percentage" readonly>
                    <small class="help-block">Automatically calculated based on role weights</small>
                  </div>
                </div>

                <!-- Color Code -->
                <div class="form-group">
                  <label for="color_code" class="col-sm-3 control-label">Color Code</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="color_code" name="color_code" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                    <div id="color_indicator" class="color-indicator" style="display: none;">
                      <div class="color-box" id="color_box"></div>
                      <span id="color_text"></span>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn btn-primary btn-flat" name="submit">Submit Evaluation</button>
                    <button type="reset" class="btn btn-default btn-flat">Reset Form</button>
                  </div>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>

  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>
<script>
$(document).ready(function(){
  // Define role-based weights for each KPI component for Wood and Steel
  const roleWeights = {
    wood: {
      'Designer': { 
        output: 0.25,
        timeliness: 0.25,
        accuracy: 0.35,
        teamwork: 0.15,
        material_efficiency: 0
      },
      'Project Manager': { 
        output: 0.2,
        timeliness: 0.3,
        accuracy: 0.2,
        teamwork: 0.3,
        material_efficiency: 0
      },
      'Estimator': { 
        output: 0.2,
        timeliness: 0.3,
        accuracy: 0.4,
        teamwork: 0.1,
        material_efficiency: 0
      },
      'Fabricator': { 
        output: 0.25,
        timeliness: 0.2,
        accuracy: 0.2,
        teamwork: 0.2,
        material_efficiency: 0.15
      },
      'CNC Operator': { 
        output: 0.25,
        timeliness: 0.2,
        accuracy: 0.3,
        teamwork: 0.1,
        material_efficiency: 0.15
      },
      'Painter': { 
        output: 0.3,
        timeliness: 0.2,
        accuracy: 0.35,
        teamwork: 0.15,
        material_efficiency: 0
      },
      'Electrician': { 
        output: 0.1,
        timeliness: 0.3,
        accuracy: 0.4,
        teamwork: 0.2,
        material_efficiency: 0
      },
      'Project Coordinator': { 
        output: 0.3,
        timeliness: 0.35,
        accuracy: 0.2,
        teamwork: 0.15,
        material_efficiency: 0
      },
      'Accounting': { 
        output: 0.1,
        timeliness: 0.4,
        accuracy: 0.4,
        teamwork: 0.1,
        material_efficiency: 0
      }
    },
    steel: {
      'HR & Admin': { 
        output: 0.2,
        timeliness: 0.2,
        accuracy: 0.3,
        teamwork: 0.3,
        material_efficiency: 0
      },
      'Inventory & Logistics': { 
        output: 0.25,
        timeliness: 0.25,
        accuracy: 0.2,
        teamwork: 0.15,
        material_efficiency: 0.15
      },
      'Accounting & Receivables': { 
        output: 0.25,
        timeliness: 0.3,
        accuracy: 0.3,
        teamwork: 0.15,
        material_efficiency: 0
      },
      'Documentation & Projects': { 
        output: 0.3,
        timeliness: 0.4,
        accuracy: 0.2,
        teamwork: 0.1,
        material_efficiency: 0
      },
      'Production / Operations': { 
        output: 0.25,
        timeliness: 0.2,
        accuracy: 0.25,
        teamwork: 0.1,
        material_efficiency: 0.2
      }
    }
  };
  
  // Steel roles
  const steelRoles = [
    'HR & Admin',
    'Inventory & Logistics',
    'Accounting & Receivables',
    'Documentation & Projects',
    'Production / Operations'
  ];
  
  // Default weights (fallback)
  let currentWeights = {
    output: 0.25,
    timeliness: 0.25,
    accuracy: 0.35,
    teamwork: 0.15,
    material_efficiency: 0
  };
  
  // Current material type
  let currentMaterial = 'wood';
  
  // Function to update weight display
  function updateWeightDisplay() {
    $('#output_weight').text('Weight: ' + (currentWeights.output * 100) + '%');
    $('#timeliness_weight').text('Weight: ' + (currentWeights.timeliness * 100) + '%');
    $('#accuracy_weight').text('Weight: ' + (currentWeights.accuracy * 100) + '%');
    $('#teamwork_weight').text('Weight: ' + (currentWeights.teamwork * 100) + '%');
    
    // Show/hide material efficiency based on role and material
    if (currentWeights.material_efficiency > 0) {
      $('#material_efficiency_group').show();
      $('#material_efficiency_weight').text('Weight: ' + (currentWeights.material_efficiency * 100) + '%');
    } else {
      $('#material_efficiency_group').hide();
      $('#material_efficiency_percentage').val('');
    }
  }
  
  // Function to update color indicator
  function updateColorIndicator(colorCode, kpiValue) {
    if (colorCode && kpiValue !== '') {
      $('#color_indicator').show();
      $('#color_text').text(colorCode + ' (' + kpiValue + '%)');
      
      // Set background color based on color code
      let backgroundColor;
      let textColor;
      
      switch(colorCode) {
        case 'Green':
          backgroundColor = '#d4edda';
          textColor = '#155724';
          break;
        case 'Yellow':
          backgroundColor = '#fff3cd';
          textColor = '#856404';
          break;
        case 'Orange':
          backgroundColor = '#ffeaa7';
          textColor = '#b8860b';
          break;
        case 'Red':
          backgroundColor = '#f8d7da';
          textColor = '#721c24';
          break;
        default:
          backgroundColor = '#f8f9fa';
          textColor = '#495057';
      }
      
      $('#color_box').css({
        'background-color': backgroundColor,
        'border': '1px solid ' + textColor
      });
      $('#color_text').css('color', textColor);
    } else {
      $('#color_indicator').hide();
    }
  }
  
  // Update roles based on material type
  function updateRoles() {
    const material = $('#material_type').val();
    const roleSelect = $('#role');
    
    // Clear existing options
    roleSelect.empty().append('<option value="">Select Role</option>');
    
    // Add roles based on material type
    if (material === 'Wood') {
      currentMaterial = 'wood';
      // Add wood roles
      $.each(roleWeights.wood, function(role) {
        roleSelect.append($('<option>').val(role).text(role));
      });
      // Hide steel fields and show wood fields
      $('#steel_fields').hide();
      $('#wood_fields').show();
      $('#wood_fields2').show();
    } else if (material === 'Steel') {
      currentMaterial = 'steel';
      // Add steel roles
      $.each(roleWeights.steel, function(role) {
        roleSelect.append($('<option>').val(role).text(role));
      });
      // Show steel fields and hide wood fields
      $('#steel_fields').show();
      $('#wood_fields').hide();
      $('#wood_fields2').hide();
    } else {
      // Default to wood roles if no material selected
      $.each(roleWeights.wood, function(role) {
        roleSelect.append($('<option>').val(role).text(role));
      });
      $('#steel_fields').hide();
      $('#wood_fields').hide();
      $('#wood_fields2').hide();
    }
    
    // Reset role weights
    currentWeights = {
      output: 0.25,
      timeliness: 0.25,
      accuracy: 0.35,
      teamwork: 0.15,
      material_efficiency: 0
    };
    updateWeightDisplay();
    calculateOverallKPI();
  }
  
  // Update weights when role is selected
  $('#role').change(function(){
    var selectedRole = $(this).val();
    const material = currentMaterial;
    
    // Update current weights based on selected role and material
    if(selectedRole && roleWeights[material][selectedRole]) {
      currentWeights = roleWeights[material][selectedRole];
      updateWeightDisplay();
      console.log('Updated weights for role ' + selectedRole + ':', currentWeights);
      
      // For Steel, show material efficiency only for specific roles
      if (material === 'steel') {
        const showMaterialEfficiency = [
          'Inventory & Logistics', 
          'Production / Operations'
        ].includes(selectedRole);
        
        if (showMaterialEfficiency) {
          $('#material_efficiency_group').show();
        } else {
          $('#material_efficiency_group').hide();
          $('#material_efficiency_percentage').val('');
          currentWeights.material_efficiency = 0;
        }
        updateWeightDisplay();
      }
      
      // Recalculate overall KPI if percentages are already entered
      calculateOverallKPI();
    } else {
      // Reset to default weights if no role selected
      currentWeights = {
        output: 0.25,
        timeliness: 0.25,
        accuracy: 0.35,
        teamwork: 0.15,
        material_efficiency: 0
      };
      updateWeightDisplay();
    }
  });
  
  // Update roles when material type changes
  $('#material_type').change(updateRoles);
  
  // Function to calculate overall KPI using current weights
  function calculateOverallKPI() {
    var output = parseFloat($('#output_percentage').val()) || 0;
    var timeliness = parseFloat($('#timeliness_percentage').val()) || 0;
    var accuracy = parseFloat($('#accuracy_percentage').val()) || 0;
    var teamwork = parseFloat($('#teamwork_percentage').val()) || 0;
    var material_efficiency = parseFloat($('#material_efficiency_percentage').val()) || 0;
    
    // Calculate weighted overall KPI using current role weights
    var overall = (output * currentWeights.output) + 
                  (timeliness * currentWeights.timeliness) + 
                  (accuracy * currentWeights.accuracy) + 
                  (teamwork * currentWeights.teamwork) +
                  (material_efficiency * currentWeights.material_efficiency);
    
    $('#overall_kpi_percentage').val(overall.toFixed(2));
    
    // Auto-set color code based on overall KPI
    let colorCode = '';
    if (overall >= 90) {
      colorCode = 'Green';
    } else if (overall >= 75) {
      colorCode = 'Yellow';
    } else if (overall >= 60) {
      colorCode = 'Orange';
    } else if (overall > 0) {
      colorCode = 'Red';
    }
    
    $('#color_code').val(colorCode);
    updateColorIndicator(colorCode, overall.toFixed(2));
  }
  
  // Auto-calculate overall KPI when any percentage changes
  $('#output_percentage, #timeliness_percentage, #accuracy_percentage, #teamwork_percentage, #material_efficiency_percentage').on('input', function(){
    calculateOverallKPI();
  });

  // Auto-determine on-time status based on dates
  $('#due_date, #actual_completion_date').change(function(){
    var dueDate = new Date($('#due_date').val());
    var completionDate = new Date($('#actual_completion_date').val());
    
    if(dueDate && completionDate) {
      if(completionDate <= dueDate) {
        $('#on_time').val('Yes');
      } else {
        $('#on_time').val('No');
      }
    }
  });
  
  // Initialize on page load
  updateRoles();
  updateWeightDisplay();
  
  // Reset form functionality
  $('button[type="reset"]').click(function(){
    setTimeout(function(){
      // Reset to default weights
      currentWeights = {
        output: 0.25,
        timeliness: 0.25,
        accuracy: 0.35,
        teamwork: 0.15,
        material_efficiency: 0
      };
      updateWeightDisplay();
      $('#overall_kpi_percentage').val('');
      $('#color_code').val('');
      $('#color_indicator').hide();
      // Reset material type and roles
      updateRoles();
    }, 100);
  });
});
</script>

<style>
/* Floating Box for the entire form */
.floating-box {
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

/* Form styling */
.form-horizontal .form-group {
  margin-bottom: 20px;
}

.form-horizontal .control-label {
  font-weight: bold;
  text-align: right;
  padding-top: 7px;
}

.form-control {
  border-radius: 8px;
  border: 1px solid #ddd;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

/* Button styling */
.btn {
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

/* Color indicator styling */
.color-indicator {
  margin-top: 10px;
  padding: 8px;
  border-radius: 5px;
  display: flex;
  align-items: center;
  gap: 10px;
  background-color: #f8f9fa;
  border: 1px solid #ddd;
}

.color-box {
  width: 30px;
  height: 30px;
  border-radius: 4px;
  display: inline-block;
}

#color_text {
  font-weight: bold;
  font-size: 14px;
}

/* Weight display styling */
.help-block {
  font-weight: bold;
  color: #007bff;
  margin-top: 5px;
}

/* Overall KPI field styling */
#overall_kpi_percentage {
  background-color: #f8f9fa;
  font-weight: bold;
}

/* Color code field styling (read-only) */
#color_code {
  background-color: #f8f9fa !important;
  cursor: not-allowed !important;
  font-weight: bold;
  color: #495057;
}

/* Responsive adjustments */
@media screen and (max-width: 768px) {
  .form-horizontal .control-label {
    text-align: left;
    margin-bottom: 5px;
  }
  
  .form-horizontal .col-sm-3,
  .form-horizontal .col-sm-9 {
    width: 100%;
    float: none;
  }
  
  .col-sm-offset-3 {
    margin-left: 0;
  }
  
  .color-indicator {
    flex-direction: column;
    align-items: flex-start;
    text-align: center;
  }
  
  .color-box {
    align-self: center;
  }
}

body, .wrapper, .content-wrapper {
    background-color: #f4f1ed !important;
}
</style>

</body>
</html>