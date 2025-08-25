<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Performance Evaluation Scoring Weights</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="index.php?page=performance_list"><i class="fa fa-star"></i> Performance</a></li>
        <li class="active">Scoring Weights</li>
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
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo '</ul></div>';
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['success'])) {
            echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . htmlspecialchars($_SESSION['success']) . "
            </div>
          ";
            unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="alert alert-info">
            <h5><i class="icon fa fa-info-circle"></i> About Scoring Weights:</h5>
            <p>Different roles have different weight distributions for the performance categories. These weights are used to calculate the weighted average score for each employee based on their specific role requirements and material type (Wood vs Steel).</p>
          </div>
        </div>
      </div>

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
                <i class="fa fa-balance-scale"></i> 
                <span id="materialTitle">Wood</span> - Role-Based Scoring Weights
              </h3>
           
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="scoring_weights_table" class="table table-bordered table-striped table-fit">
                  <thead>
                    <tr>
                      <th style="text-align: center; vertical-align: middle;">Role</th>
                      <th style="text-align: center; vertical-align: middle;">
                        <i class="fa fa-tasks"></i><br>Output Weight
                      </th>
                      <th style="text-align: center; vertical-align: middle;">
                        <i class="fa fa-clock-o"></i><br>Timeliness Weight
                      </th>
                      <th style="text-align: center; vertical-align: middle;">
                        <i class="fa fa-check-circle"></i><br>Accuracy Weight
                      </th>
                      <th style="text-align: center; vertical-align: middle;">
                        <i class="fa fa-users"></i><br>Teamwork Weight
                      </th>
                      <th style="text-align: center; vertical-align: middle;" id="materialEfficiencyHeader" style="display: none;">
                        <i class="fa fa-cogs"></i><br>Material Efficiency Weight
                      </th>
                      <th style="text-align: center; vertical-align: middle;">
                        <i class="fa fa-calculator"></i><br>Total
                      </th>
                    </tr>
                  </thead>
                  <tbody id="weightsTableBody">
                    <!-- Table rows will be populated by JavaScript -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-info-circle"></i> Weight Explanation</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-xs-6">
                  <div class="small-box bg-blue">
                    <div class="inner">
                      <h4><i class="fa fa-tasks"></i></h4>
                      <p><strong>Output</strong></p>
                    </div>
                  </div>
                  <p class="text-sm">Measures productivity and task completion</p>
                </div>
                <div class="col-xs-6">
                  <div class="small-box bg-green">
                    <div class="inner">
                      <h4><i class="fa fa-clock-o"></i></h4>
                      <p><strong>Timeliness</strong></p>
                    </div>
                  </div>
                  <p class="text-sm">Measures adherence to deadlines and schedules</p>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <div class="small-box bg-orange">
                    <div class="inner">
                      <h4><i class="fa fa-check-circle"></i></h4>
                      <p><strong>Accuracy</strong></p>
                    </div>
                  </div>
                  <p class="text-sm">Measures quality and precision of work</p>
                </div>
                <div class="col-xs-6">
                  <div class="small-box bg-red">
                    <div class="inner">
                      <h4><i class="fa fa-users"></i></h4>
                      <p><strong>Teamwork</strong></p>
                    </div>
                  </div>
                  <p class="text-sm">Measures collaboration and communication</p>
                </div>
              </div>
              <div class="row" id="materialEfficiencyExplanation" style="display: none;">
                <div class="col-xs-12">
                  <div class="small-box bg-purple">
                    <div class="inner">
                      <h4><i class="fa fa-cogs"></i></h4>
                      <p><strong>Material Efficiency</strong></p>
                    </div>
                  </div>
                  <p class="text-sm">Measures efficient use of materials and waste reduction</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Usage Notes</h3>
            </div>
            <div class="box-body">
              <div class="callout callout-info">
                <h4><i class="fa fa-info"></i> How Weights Work:</h4>
                <p id="usageDescription">Each role has different weight distributions based on job requirements for wood projects.</p>
              </div>
              <div class="callout callout-warning">
                <h4><i class="fa fa-warning"></i> Important:</h4>
                <p>All weights for each role must sum to 100%. These weights are automatically applied when calculating performance scores.</p>
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

<script>
// Weight data based on the images
const woodWeights = {
    'Designer': { output: 25, timeliness: 25, accuracy: 35, teamwork: 15, materialEfficiency: 0 },
    'Project Manager': { output: 20, timeliness: 30, accuracy: 20, teamwork: 30, materialEfficiency: 0 },
    'Estimator': { output: 20, timeliness: 30, accuracy: 40, teamwork: 10, materialEfficiency: 0 },
    'Fabricator': { output: 25, timeliness: 20, accuracy: 20, teamwork: 20, materialEfficiency: 15 },
    'CNC Operator': { output: 25, timeliness: 20, accuracy: 30, teamwork: 10, materialEfficiency: 15 },
    'Painter': { output: 30, timeliness: 20, accuracy: 35, teamwork: 15, materialEfficiency: 0 },
    'Electrician': { output: 10, timeliness: 30, accuracy: 40, teamwork: 20, materialEfficiency: 0 },
    'Project Coordinator / Admin': { output: 30, timeliness: 35, accuracy: 20, teamwork: 15, materialEfficiency: 0 },
    'Accounting': { output: 10, timeliness: 40, accuracy: 40, teamwork: 10, materialEfficiency: 0 }
};

// Updated steel weights to match the evaluation form
const steelWeights = {
    'HR & Admin': { output: 0.2, timeliness: 0.2, accuracy: 0.3, teamwork: 0.3, materialEfficiency: 0 },
    'Inventory & Logistics': { output: 0.25, timeliness: 0.25, accuracy: 0.2, teamwork: 0.15, materialEfficiency: 0.15 },
    'Accounting & Receivables': { output: 0.25, timeliness: 0.3, accuracy: 0.3, teamwork: 0.15, materialEfficiency: 0 },
    'Documentation & Projects': { output: 0.3, timeliness: 0.4, accuracy: 0.2, teamwork: 0.1, materialEfficiency: 0 },
    'Production / Operations': { output: 0.25, timeliness: 0.2, accuracy: 0.25, teamwork: 0.1, materialEfficiency: 0.2 }
};

const roleIcons = {
    'Designer': 'fa-paint-brush text-purple',
    'Project Manager': 'fa-briefcase text-blue',
    'Estimator': 'fa-calculator text-green',
    'Fabricator': 'fa-wrench text-orange',
    'CNC Operator': 'fa-cogs text-gray',
    'Painter': 'fa-paint-brush text-red',
    'Electrician': 'fa-bolt text-yellow',
    'Project Coordinator / Admin': 'fa-clipboard text-teal',
    'Accounting': 'fa-money text-green',
    'HR & Admin': 'fa-users text-blue',
    'Inventory & Logistics': 'fa-truck text-orange',
    'Accounting & Receivables': 'fa-dollar text-green',
    'Documentation & Projects': 'fa-file-text text-purple',
    'Production / Operations': 'fa-industry text-red'
};

function updateTable(isSteel) {
    const weights = isSteel ? steelWeights : woodWeights;
    const tableBody = document.getElementById('weightsTableBody');
    const materialTitle = document.getElementById('materialTitle');
    const materialEfficiencyHeader = document.getElementById('materialEfficiencyHeader');
    const materialEfficiencyExplanation = document.getElementById('materialEfficiencyExplanation');
    const usageDescription = document.getElementById('usageDescription');
    const woodLabel = document.getElementById('woodLabel');
    const steelLabel = document.getElementById('steelLabel');
    
    // Update labels
    materialTitle.textContent = isSteel ? 'Steel' : 'Wood';
    woodLabel.classList.toggle('active', !isSteel);
    steelLabel.classList.toggle('active', isSteel);
    
    // Show/hide material efficiency column
    const hasMaterialEfficiency = Object.values(weights).some(w => w.materialEfficiency > 0);
    materialEfficiencyHeader.style.display = hasMaterialEfficiency ? '' : 'none';
    materialEfficiencyExplanation.style.display = hasMaterialEfficiency ? '' : 'none';
    
    // Update usage description
    usageDescription.textContent = `Each role has different weight distributions based on job requirements for ${isSteel ? 'steel' : 'wood'} projects.`;
    
    // Clear table body
    tableBody.innerHTML = '';
    
    // Populate table
    Object.entries(weights).forEach(([role, weight]) => {
        const icon = roleIcons[role] || 'fa-user';
        const row = document.createElement('tr');
        row.setAttribute('data-role', role.toLowerCase().replace(/\s+/g, '-').replace('/', '-'));
        
        // Convert decimal weights to percentages for display
        const displayWeights = isSteel ? {
            output: Math.round(weight.output * 100),
            timeliness: Math.round(weight.timeliness * 100),
            accuracy: Math.round(weight.accuracy * 100),
            teamwork: Math.round(weight.teamwork * 100),
            materialEfficiency: Math.round(weight.materialEfficiency * 100)
        } : weight;
        
        row.innerHTML = `
            <td style="font-weight: bold; vertical-align: middle;">
                <i class="fa ${icon}"></i> ${role}
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <span class="weight-badge bg-blue">${displayWeights.output}%</span>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <span class="weight-badge bg-green">${displayWeights.timeliness}%</span>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <span class="weight-badge bg-orange">${displayWeights.accuracy}%</span>
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <span class="weight-badge bg-red">${displayWeights.teamwork}%</span>
            </td>
            ${hasMaterialEfficiency ? `<td style="text-align: center; vertical-align: middle;">
                <span class="weight-badge bg-purple">${displayWeights.materialEfficiency}%</span>
            </td>` : ''}
            <td style="text-align: center; vertical-align: middle; font-weight: bold;">
                <span class="label label-success">100%</span>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
}


$(function(){
    // Initialize with wood weights
    updateTable(false);

    // Handle toggle switch
    document.getElementById('materialToggle').addEventListener('change', function() {
        updateTable(this.checked);
    });
    
    // Add hover effects to table rows
    $('#scoring_weights_table').on('mouseenter', 'tbody tr', function() {
        $(this).addClass('hover-highlight');
    }).on('mouseleave', 'tbody tr', function() {
        $(this).removeClass('hover-highlight');
    });
    
    // Add click to highlight functionality
    $('#scoring_weights_table').on('click', 'tbody tr', function() {
        $(this).toggleClass('selected-row');
    });
});
</script>

<style>
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
  background-color: #337ab7;
  color: white;
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
  transition: all 0.3s ease;
  vertical-align: middle;
}

.table tbody tr:hover,
.table tbody tr.hover-highlight {
  background-color: rgba(243, 237, 55, 0.3);
  cursor: pointer;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table tbody tr.selected-row {
  background-color: rgba(91, 192, 222, 0.3);
  border-left: 4px solid #5bc0de;
}

.weight-badge {
  display: inline-block;
  padding: 6px 12px;
  color: white;
  font-weight: bold;
  border-radius: 20px;
  font-size: 12px;
  min-width: 50px;
  text-align: center;
}

.bg-blue { background-color: #337ab7; }
.bg-green { background-color: #00a65a; }
.bg-orange { background-color: #ff851b; }
.bg-red { background-color: #dd4b39; }
.bg-purple { background-color: #9b59b6; }

.label {
  border-radius: 4px;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.small-box {
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 10px;
  text-align: center;
}

.small-box .inner h4 {
  margin: 0;
  font-size: 24px;
}

.small-box .inner p {
  margin: 5px 0 0 0;
  font-size: 14px;
  font-weight: bold;
}

.callout {
  border-radius: 8px;
  padding: 15px;
  margin: 15px 0;
  border-left: 4px solid #5bc0de;
}

.callout-info {
  background-color: #d9edf7;
  border-left-color: #5bc0de;
}

.callout-warning {
  background-color: #fcf8e3;
  border-left-color: #f0ad4e;
}

.alert {
  border-radius: 8px;
  border-left: 4px solid #5bc0de;
}

.btn {
  border-radius: 6px;
  font-weight: 500;
}

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

/* Role-specific row styling */
tr[data-role*="designer"] { background-color: rgba(155, 89, 182, 0.05) !important; }
tr[data-role*="manager"] { background-color: rgba(52, 152, 219, 0.05) !important; }
tr[data-role*="estimator"] { background-color: rgba(46, 204, 113, 0.05) !important; }
tr[data-role*="fabricator"] { background-color: rgba(230, 126, 34, 0.05) !important; }
tr[data-role*="operator"] { background-color: rgba(149, 165, 166, 0.05) !important; }
tr[data-role*="painter"] { background-color: rgba(231, 76, 60, 0.05) !important; }
tr[data-role*="electrician"] { background-color: rgba(241, 196, 15, 0.05) !important; }
tr[data-role*="coordinator"] { background-color: rgba(26, 188, 156, 0.05) !important; }
tr[data-role*="accounting"] { background-color: rgba(39, 174, 96, 0.05) !important; }
tr[data-role*="admin"] { background-color: rgba(41, 128, 185, 0.05) !important; }
tr[data-role*="inventory"] { background-color: rgba(243, 156, 18, 0.05) !important; }
tr[data-role*="production"] { background-color: rgba(192, 57, 43, 0.05) !important; }
tr[data-role*="documentation"] { background-color: rgba(142, 68, 173, 0.05) !important; }

@media screen and (max-width: 768px) {
  .table-fit th, .table-fit td {
    font-size: 11px;
    padding: 6px;
    white-space: normal;
  }
  
  .weight-badge {
    padding: 4px 8px;
    font-size: 10px;
    min-width: 40px;
  }
  
  .col-md-6 {
    margin-bottom: 20px;
  }
  
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

/* Print styles */
@media print {
  .box-header .pull-right,
  .btn,
  .switch-container {
    display: none !important;
  }
  
  .floating-box {
    box-shadow: none;
    border: 1px solid #ddd;
  }
  
  .weight-badge {
    border: 1px solid #333;
    color: #333 !important;
    background: white !important;
  }
}
</style>

</body>
</html>