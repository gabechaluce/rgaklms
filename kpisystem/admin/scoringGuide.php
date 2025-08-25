<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Performance Evaluation Scoring Guide</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="index.php?page=performance_list"><i class="fa fa-star"></i> Performance</a></li>
        <li class="active">Scoring Guide</li>
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
            <h5><i class="icon fa fa-info-circle"></i> How to Use This Guide:</h5>
            <p>This scoring guide helps evaluate employee performance across key areas. Each category is scored from 0-100, and the overall performance level is determined by the average of all scores.</p>
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
                <i class="fa fa-star"></i> 
                <span id="materialTitle">Wood</span> Performance Evaluation Scoring Guide
              </h3>
              <div class="pull-right">
                <button class="btn btn-info btn-sm" onclick="printGuide()">
                  <i class="fa fa-print"></i> Print Guide
                </button>
                <button class="btn btn-primary btn-sm" onclick="location.href='evaluation.php'">
                  <i class="fa fa-plus"></i> Create New Evaluation
                </button>
                <button class="btn btn-success btn-sm" onclick="location.href='evaluationList.php'">
                  <i class="fa fa-list"></i> View All Evaluations
                </button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="scoring_guide_table" class="table table-bordered table-fit">
                  <thead>
                    <th style="text-align: center; vertical-align: middle;">Score Range</th>
                    <th style="text-align: center; vertical-align: middle;">Performance Level</th>
                    <th style="text-align: center; vertical-align: middle;">Output</th>
                    <th style="text-align: center; vertical-align: middle;">Timeliness</th>
                    <th style="text-align: center; vertical-align: middle;">Accuracy</th>
                    <th style="text-align: center; vertical-align: middle;">Teamwork</th>
                    <th style="text-align: center; vertical-align: middle; display: none;" id="materialEfficiencyHeader">Material Efficiency</th>
                  </thead>
                  <tbody id="guideTableBody">
                    <!-- Table content will be populated by JavaScript -->
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
        <h3 class="box-title"><i class="fa fa-calculator"></i> Scoring Method</h3>
      </div>
      <div class="box-body">
        <div class="info-box bg-light">
          <span class="info-box-icon bg-blue"><i class="fa fa-calculator"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Weighted Average</span>
            <span class="info-box-number">Overall Score Formula</span>
            <div class="progress">
              <div class="progress-bar" style="width: 100%"></div>
            </div>
            <span class="progress-description">
              <strong id="formulaText">KPI = (Output × Weight) + (Timeliness × Weight) + (Accuracy × Weight) + (Teamwork × Weight) + (Material Efficiency × Weight)</strong>
            </span>
            <div class="formula-explanation" style="margin-top: 10px; font-size: 13px;">
              <p><strong>Where:</strong></p>
              <ul>
                <li><strong>Output:</strong> Measures productivity and task completion</li>
                <li><strong>Timeliness:</strong> Measures adherence to deadlines</li>
                <li><strong>Accuracy:</strong> Measures quality and precision</li>
                <li><strong>Teamwork:</strong> Measures collaboration and communication</li>
                <li><strong>Material Efficiency:</strong> Measures efficient use of materials (Steel only)</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-6">
    <div class="box floating-box">
      <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-target"></i> Performance Goals</h3>
      </div>
      <div class="box-body">
        <div class="info-box bg-light">
          <span class="info-box-icon bg-green"><i class="fa fa-target"></i></span>
          <div class="info-box-content">
            <span class="info-box-text">Target Performance</span>
            <span class="info-box-number">80%+ Target</span>
            <div class="progress">
              <div class="progress-bar bg-green" style="width: 80%"></div>
            </div>
            <span class="progress-description">
              <strong>Aim for "Good" or "Excellent" performance levels</strong>
            </span>
            <div class="target-explanation" style="margin-top: 10px; font-size: 13px;">
              <p><strong>Performance Benchmarks:</strong></p>
              <ul>
                <li><strong>90-100%:</strong> Excellent - Exceeds expectations</li>
                <li><strong>80-89%:</strong> Good - Meets all expectations</li>
                <li><strong>70-79%:</strong> Needs Improvement</li>
                <li><strong>Below 70%:</strong> Poor - Requires immediate action</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-lightbulb-o"></i> Evaluation Tips</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <h4><strong>For Evaluators:</strong></h4>
                  <ul class="list-unstyled">
                    <li><i class="fa fa-check text-green"></i> Be objective and fair in scoring</li>
                    <li><i class="fa fa-check text-green"></i> Provide specific examples for ratings</li>
                    <li><i class="fa fa-check text-green"></i> Focus on observable behaviors</li>
                    <li><i class="fa fa-check text-green"></i> Consider the evaluation period context</li>
                  </ul>
                </div>
                <div class="col-md-6">
                  <h4><strong>For Employees:</strong></h4>
                  <ul class="list-unstyled">
                    <li><i class="fa fa-star text-yellow"></i> Understand expectations for each category</li>
                    <li><i class="fa fa-star text-yellow"></i> Document achievements and improvements</li>
                    <li><i class="fa fa-star text-yellow"></i> Seek feedback regularly</li>
                    <li><i class="fa fa-star text-yellow"></i> Create action plans for improvement</li>
                  </ul>
                </div>
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
// Performance guide data
const woodGuide = [
  {
    range: "90-100%",
    level: "Excellent",
    output: "Exceeded expectations, supported others",
    timeliness: "Always early or on-time",
    accuracy: "Flawless or 1 minor revision",
    teamwork: "Consistent updates, helped coordination",
    materialEfficiency: "Waste <5% of planned quantity"
  },
  {
    range: "80-89%",
    level: "Good",
    output: "Completed most tasks effectively",
    timeliness: "1 minor delay",
    accuracy: "2-3 errors, not critical",
    teamwork: "Usually responsive, small gaps",
    materialEfficiency: "Waste 6-10%"
  },
  {
    range: "70-79%",
    level: "Needs Improvement",
    output: "Multiple tasks incomplete or delayed",
    timeliness: "More than 1 delay",
    accuracy: "Significant issues or backjobs",
    teamwork: "Often unresponsive, unclear handoffs",
    materialEfficiency: "Waste 11-20%"
  },
  {
    range: "<70%",
    level: "Poor",
    output: "Failed key deliverables",
    timeliness: "Frequent or serious lateness",
    accuracy: "Major rework or client complaint",
    teamwork: "Hindered team flow",
    materialEfficiency: "Waste >20%"
  }
];

const steelGuide = [
  {
    range: "90-100%",
    level: "Excellent",
    output: "Exceeded expectations, supported others",
    timeliness: "Always early or on-time",
    accuracy: "Flawless or 1 minor revision",
    teamwork: "Consistent updates, helped coordination",
    materialEfficiency: "Material waste <3%"
  },
  {
    range: "80-89%",
    level: "Good",
    output: "Completed most tasks effectively",
    timeliness: "1 minor delay",
    accuracy: "2-3 errors, not critical",
    teamwork: "Usually responsive, small gaps",
    materialEfficiency: "Material waste 4-7%"
  },
  {
    range: "70-79%",
    level: "Needs Improvement",
    output: "Multiple tasks incomplete or delayed",
    timeliness: "More than 1 delay",
    accuracy: "Significant issues or backjobs",
    teamwork: "Often unresponsive, unclear handoffs",
    materialEfficiency: "Material waste 8-15%"
  },
  {
    range: "<70%",
    level: "Poor",
    output: "Failed key deliverables",
    timeliness: "Frequent or serious lateness",
    accuracy: "Major rework or client complaint",
    teamwork: "Hindered team flow",
    materialEfficiency: "Material waste >15%"
  }
];

function updateGuide(isSteel) {
    const guide = isSteel ? steelGuide : woodGuide;
    const tableBody = document.getElementById('guideTableBody');
    const materialTitle = document.getElementById('materialTitle');
    const materialEfficiencyHeader = document.getElementById('materialEfficiencyHeader');
    const formulaText = document.getElementById('formulaText');
    const woodLabel = document.getElementById('woodLabel');
    const steelLabel = document.getElementById('steelLabel');
    
    // Update labels
    materialTitle.textContent = isSteel ? 'Steel' : 'Wood';
    woodLabel.classList.toggle('active', !isSteel);
    steelLabel.classList.toggle('active', isSteel);
    
    // Show/hide material efficiency column
    materialEfficiencyHeader.style.display = isSteel ? '' : 'none';
    
    // Update formula text
    formulaText.textContent = isSteel 
        ? 'KPI = (Output × Weight) + (Timeliness × Weight) + (Accuracy × Weight) + (Teamwork × Weight) + (Material Efficiency × Weight)'
        : 'KPI = (Output × Weight) + (Timeliness × Weight) + (Accuracy × Weight) + (Teamwork × Weight)';
    
    // Clear table body
    tableBody.innerHTML = '';
    
    // Populate table
    guide.forEach(item => {
        const row = document.createElement('tr');
        row.setAttribute('data-level', item.level.toLowerCase().replace(' ', '-'));
        
        row.innerHTML = `
            <td style="text-align: center; font-weight: bold; vertical-align: middle;"><strong>${item.range}</strong></td>
            <td style="text-align: center; vertical-align: middle;">
                <span class="label ${getLevelClass(item.level)}">${item.level}</span>
            </td>
            <td style="vertical-align: middle;">
                <strong>${item.output}</strong>
                <br><small class="text-muted">${getOutputDescription(item.level)}</small>
            </td>
            <td style="vertical-align: middle;">
                <strong>${item.timeliness}</strong>
                <br><small class="text-muted">${getTimelinessDescription(item.level)}</small>
            </td>
            <td style="vertical-align: middle;">
                <strong>${item.accuracy}</strong>
                <br><small class="text-muted">${getAccuracyDescription(item.level)}</small>
            </td>
            <td style="vertical-align: middle;">
                <strong>${item.teamwork}</strong>
                <br><small class="text-muted">${getTeamworkDescription(item.level)}</small>
            </td>
            ${isSteel ? `<td style="vertical-align: middle;">
                <strong>${item.materialEfficiency}</strong>
                <br><small class="text-muted">${getMaterialEfficiencyDescription(item.level)}</small>
            </td>` : ''}
        `;
        
        tableBody.appendChild(row);
    });
}

function getLevelClass(level) {
    switch(level) {
        case 'Excellent': return 'label-success';
        case 'Good': return 'label-info';
        case 'Needs Improvement': return 'label-warning';
        case 'Poor': return 'label-danger';
        default: return 'label-default';
    }
}

function getOutputDescription(level) {
    switch(level) {
        case 'Excellent': return 'Goes above and beyond assigned tasks, mentors colleagues, takes initiative';
        case 'Good': return 'Meets expectations consistently, reliable performance';
        case 'Needs Improvement': return 'Performance below expectations, requires support and guidance';
        case 'Poor': return 'Consistently misses important objectives, requires immediate intervention';
        default: return '';
    }
}

function getTimelinessDescription(level) {
    switch(level) {
        case 'Excellent': return 'Consistently meets deadlines, often completes work ahead of schedule';
        case 'Good': return 'Generally punctual with occasional minor delays that don\'t impact others';
        case 'Needs Improvement': return 'Frequent delays that may impact team schedules';
        case 'Poor': return 'Chronic tardiness significantly impacting operations';
        default: return '';
    }
}

function getAccuracyDescription(level) {
    switch(level) {
        case 'Excellent': return 'Work requires minimal to no corrections, high attention to detail';
        case 'Good': return 'Minor mistakes that are easily correctable, good overall quality';
        case 'Needs Improvement': return 'Multiple errors requiring substantial corrections';
        case 'Poor': return 'Serious quality issues requiring extensive corrections';
        default: return '';
    }
}

function getTeamworkDescription(level) {
    switch(level) {
        case 'Excellent': return 'Proactive communication, facilitates team collaboration';
        case 'Good': return 'Good team player with minor communication gaps';
        case 'Needs Improvement': return 'Communication issues affecting team productivity';
        case 'Poor': return 'Poor communication negatively affects team performance';
        default: return '';
    }
}

function getMaterialEfficiencyDescription(level) {
    switch(level) {
        case 'Excellent': return 'Exceptional material usage, minimal waste';
        case 'Good': return 'Good material usage with minor waste';
        case 'Needs Improvement': return 'Noticeable material waste, needs improvement';
        case 'Poor': return 'Excessive material waste, requires immediate attention';
        default: return '';
    }
}

function printGuide() {
    const isSteel = document.getElementById('materialToggle').checked;
    const materialType = isSteel ? 'Steel' : 'Wood';
    const guide = isSteel ? steelGuide : woodGuide;
    
    var printWindow = window.open('', '_blank');
    var printContent = `
        <html>
        <head>
            <title>Performance Evaluation Scoring Guide - ${materialType}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #337ab7; color: white; text-align: center; }
                .label { padding: 4px 8px; border-radius: 4px; color: white; font-size: 11px; }
                .label-success { background-color: #22c55e; }
                .label-info { background-color: #ffeb3b; color: #333; }
                .label-warning { background-color: #f97316; }
                .label-danger { background-color: #ef4444; }
                h1 { text-align: center; color: #337ab7; }
                .header-info { text-align: center; margin-bottom: 30px; padding: 15px; background-color: #f9f9f9; border-radius: 5px; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <h1>Performance Evaluation Scoring Guide - ${materialType}</h1>
            <div class="header-info">
                <p><strong>How to Use:</strong> This guide helps evaluate employee performance across key areas for ${materialType.toLowerCase()} projects.</p>
                <p><strong>Scoring Method:</strong> ${isSteel ? 'KPI = (Output × Weight) + (Timeliness × Weight) + (Accuracy × Weight) + (Teamwork × Weight) + (Material Efficiency × Weight)' : 'KPI = (Output × Weight) + (Timeliness × Weight) + (Accuracy × Weight) + (Teamwork × Weight)'}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Score Range</th>
                        <th>Performance Level</th>
                        <th>Output</th>
                        <th>Timeliness</th>
                        <th>Accuracy</th>
                        <th>Teamwork</th>
                        ${isSteel ? '<th>Material Efficiency</th>' : ''}
                    </tr>
                </thead>
                <tbody>
    `;
    
    guide.forEach(item => {
        printContent += `
            <tr>
                <td style="text-align: center; font-weight: bold;">${item.range}</td>
                <td style="text-align: center;"><span class="label ${getLevelClass(item.level)}">${item.level}</span></td>
                <td><strong>${item.output}</strong><br><small>${getOutputDescription(item.level)}</small></td>
                <td><strong>${item.timeliness}</strong><br><small>${getTimelinessDescription(item.level)}</small></td>
                <td><strong>${item.accuracy}</strong><br><small>${getAccuracyDescription(item.level)}</small></td>
                <td><strong>${item.teamwork}</strong><br><small>${getTeamworkDescription(item.level)}</small></td>
                ${isSteel ? `<td><strong>${item.materialEfficiency}</strong><br><small>${getMaterialEfficiencyDescription(item.level)}</small></td>` : ''}
            </tr>
        `;
    });
    
    printContent += `
                </tbody>
            </table>
            <div style="margin-top: 30px; page-break-inside: avoid;">
                <h3>Performance Level Definitions:</h3>
                <ul>
                    <li><strong>Excellent (90-100%):</strong> Exceeds expectations consistently, demonstrates leadership qualities</li>
                    <li><strong>Good (80-89%):</strong> Meets expectations reliably, solid performance</li>
                    <li><strong>Needs Improvement (70-79%):</strong> Below expectations, requires support and development</li>
                    <li><strong>Poor (&lt;70%):</strong> Significantly below expectations, immediate intervention required</li>
                </ul>
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

$(function(){
    // Initialize with wood guide
    updateGuide(false);

    // Handle toggle switch
    document.getElementById('materialToggle').addEventListener('change', function() {
        updateGuide(this.checked);
    });
    
    // Add hover effects to table rows
    $('#scoring_guide_table').on('mouseenter', 'tbody tr', function() {
        $(this).addClass('hover-highlight');
    }).on('mouseleave', 'tbody tr', function() {
        $(this).removeClass('hover-highlight');
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
  text-align: left;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  vertical-align: middle;
}

.table tbody tr:hover,
.table tbody tr.hover-highlight {
  cursor: pointer;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.table-striped tbody tr:nth-child(odd) {
  background-color: #f9f9f9;
}

.label {
  border-radius: 4px;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.label-success {
  background-color: #22c55e !important;
}

.label-info {
  background-color: #ffeb3b !important;
  color: #333 !important;
}

.label-warning {
  background-color: #f97316 !important;
}

.label-danger {
  background-color: #ef4444 !important;
}

.info-box {
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert {
  border-radius: 8px;
  border-left: 4px solid #5bc0de;
}

.btn {
  border-radius: 6px;
  font-weight: 500;
}

/* Performance level row colors */
tr[data-level="excellent"] {
  background-color: rgba(34, 197, 94, 0.15) !important;
}

tr[data-level="excellent"]:hover,
tr[data-level="excellent"].hover-highlight {
  background-color: rgba(34, 197, 94, 0.25) !important;
}

tr[data-level="good"] {
  background-color: rgba(255, 235, 59, 0.15) !important;
}

tr[data-level="good"]:hover,
tr[data-level="good"].hover-highlight {
  background-color: rgba(255, 235, 59, 0.25) !important;
}

tr[data-level="needs-improvement"] {
  background-color: rgba(249, 115, 22, 0.15) !important;
}

tr[data-level="needs-improvement"]:hover,
tr[data-level="needs-improvement"].hover-highlight {
  background-color: rgba(249, 115, 22, 0.25) !important;
}

tr[data-level="poor"] {
  background-color: rgba(239, 68, 68, 0.15) !important;
}

tr[data-level="poor"]:hover,
tr[data-level="poor"].hover-highlight {
  background-color: rgba(239, 68, 68, 0.25) !important;
}

/* Toggle switch styles */
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

@media screen and (max-width: 768px) {
  .table-fit th, .table-fit td {
    font-size: 12px;
    padding: 8px;
    white-space: normal;
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
}
</style>

</body>
</html>