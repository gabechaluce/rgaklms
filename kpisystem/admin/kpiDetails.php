<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>KPI Details</h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="index.php?page=performance_list"><i class="fa fa-star"></i> Performance</a></li>
        <li class="active">KPI Details</li>
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
                <span id="materialTitle">Wood</span> KPI Details
              </h3>
              <div class="pull-right">
                <button class="btn btn-info btn-sm" onclick="printKPIDetails()">
                  <i class="fa fa-print"></i> Print Details
                </button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="kpi_details_table" class="table table-bordered table-striped">
                  <thead>
                    <th style="width:15%;">Role</th>
                    <th style="width:20%;">KPI Category</th>
                    <th id="targetHeader">Description</th>
                    <th style="width:10%;">Weight</th>
                  </thead>
                  <tbody id="kpiTableBody">
                    <!-- Table content will be populated by JavaScript -->
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

<script>
// KPI details data
const woodKPIs = [
  // Designer
  { role: "Designer", category: "Design Output", description: "Number of completed design tasks (3D/Layout)", weight: "40%" },
  { role: "Designer", category: "Timeliness", description: "Submitted designs on or before due date", weight: "20%" },
  { role: "Designer", category: "Accuracy", description: "Designs passed QC with minimal revisions", weight: "20%" },
  { role: "Designer", category: "Teamwork", description: "Clear communication with PM, Estimator, and Production", weight: "20%" },
  
  // Project Manager
  { role: "Project Manager", category: "Execution & Coordination", description: "Ensures project milestones are met on time", weight: "30%" },
  { role: "Project Manager", category: "Client Handling", description: "Timely updates, professional handling of concerns", weight: "20%" },
  { role: "Project Manager", category: "Documentation", description: "Turnovers, contracts, reports are complete and on-time", weight: "20%" },
  { role: "Project Manager", category: "Team Supervision", description: "Guides team, resolves issues efficiently", weight: "15%" },
  { role: "Project Manager", category: "Reporting & Feedback", description: "Daily updates, communicates risks", weight: "15%" },
  
  // Estimator
  { role: "Estimator", category: "Cost Accuracy", description: "Accurate BOM, reflects actual usage", weight: "35%" },
  { role: "Estimator", category: "Speed of Output", description: "Timely submission of estimates", weight: "25%" },
  { role: "Estimator", category: "Coordination", description: "Communicates changes with Designers and Inventory", weight: "20%" },
  { role: "Estimator", category: "Teamwork", description: "Supports Sales/PM with pricing discussions", weight: "20%" },
  
  // Fabricator
  { role: "Fabricator", category: "Output Quantity", description: "Number of units produced vs. target", weight: "30%" },
  { role: "Fabricator", category: "Timeliness", description: "Tasks completed within schedule", weight: "25%" },
  { role: "Fabricator", category: "Accuracy", description: "Proper cuts, alignment, drilling", weight: "25%" },
  { role: "Fabricator", category: "Material Efficiency", description: "Minimizes wastage of boards/hardware", weight: "20%" },
  
  // CNC Operator
  { role: "CNC Operator", category: "Cutting Accuracy", description: "Precision and cleanliness of output", weight: "35%" },
  { role: "CNC Operator", category: "Maintenance & Operation", description: "Proper machine use, no downtime", weight: "25%" },
  { role: "CNC Operator", category: "Timeliness", description: "No bottleneck in CNC stage", weight: "20%" },
  { role: "CNC Operator", category: "Material Efficiency", description: "Optimized layouts reduce scrap", weight: "20%" },
  
  // Painter
  { role: "Painter", category: "Finish Quality", description: "Smooth, bubble-free finish, correct color", weight: "35%" },
  { role: "Painter", category: "Prep & Dry Time", description: "Proper sanding/putty, avoids delays", weight: "25%" },
  { role: "Painter", category: "Clean Workspace", description: "Maintains paint area and tools", weight: "20%" },
  { role: "Painter", category: "Team Coordination", description: "Clear turnover to next team (install/packaging)", weight: "20%" },
  
  // Electrician
  { role: "Electrician", category: "Wiring Accuracy", description: "No rewiring needed, neat output", weight: "35%" },
  { role: "Electrician", category: "Timeliness", description: "Completed on-site or pre-wired as scheduled", weight: "25%" },
  { role: "Electrician", category: "Tool Handling", description: "Uses proper gear, no damage to tools", weight: "20%" },
  { role: "Electrician", category: "Team Coordination", description: "Collaborates with fabricators and painters", weight: "20%" },
  
  // Project Coordinator / Admin
  { role: "Project Coordinator / Admin", category: "Inquiry Handling", description: "Responds to leads within expected timeframe", weight: "30%" },
  { role: "Project Coordinator / Admin", category: "Document Management", description: "Updates CRM, files contracts, etc.", weight: "30%" },
  { role: "Project Coordinator / Admin", category: "Scheduling Support", description: "Aids in planning, calendar accuracy", weight: "20%" },
  { role: "Project Coordinator / Admin", category: "Team Support", description: "Supports PM/Accounting/Design as needed", weight: "20%" },
  
  // Accounting
  { role: "Accounting", category: "Collection Timeliness", description: "Follows up, secures payments before due", weight: "35%" },
  { role: "Accounting", category: "Billing Accuracy", description: "Prepares correct SOAs and ORs", weight: "30%" },
  { role: "Accounting", category: "Reporting", description: "Provides cash flow & financial summaries", weight: "20%" },
  { role: "Accounting", category: "Coordination", description: "Works closely with PM/Admin during turnover", weight: "15%" }
];

const steelKPIs = [
  // HR & Admin
  { role: "HR & Admin", category: "Recruitment Efficiency", description: "Fill positions in ≤30 days", target: "Monthly", measurement: "Time-to-fill = days to hire", weight: "10%" },
  { role: "HR & Admin", category: "On-boarding Completion", description: "Complete orientation within 7 days", target: "Monthly", measurement: "Completed onboardings ÷ new hires", weight: "8%" },
  { role: "HR & Admin", category: "Attendance Monitoring", description: "≥95% compliance", target: "Monthly", measurement: "Compliant days ÷ total workdays", weight: "10%" },
  { role: "HR & Admin", category: "Training & Development", description: "≥85% employees trained", target: "Quarterly", measurement: "Employees trained ÷ total employees", weight: "10%" },
  { role: "HR & Admin", category: "Employee Satisfaction", description: "≥80% positive responses", target: "Semi-annual", measurement: "Positive responses ÷ total responses", weight: "8%" },
  { role: "HR & Admin", category: "Policy & Compliance", description: "Zero major incidents", target: "Monthly", measurement: "Count of incidents", weight: "8%" },
  { role: "HR & Admin", category: "Records Management", description: "100% updates within 3 days", target: "Monthly", measurement: "Records updated on time ÷ required updates", weight: "8%" },
  { role: "HR & Admin", category: "Payroll Accuracy", description: "No errors; on time", target: "Monthly", measurement: "Check payroll processing date and errors", weight: "12%" },
  { role: "HR & Admin", category: "Employee Turnover", description: "≤10% turnover per year", target: "Annual", measurement: "Resigned ÷ average headcount", weight: "10%" },
  { role: "HR & Admin", category: "Process Improvement", description: "≥2 initiatives/year", target: "Annual", measurement: "Count of initiatives implemented", weight: "6%" },
  
  // Inventory & Logistics
  { role: "Inventory & Logistics", category: "Inventory Accuracy", description: "≥98% match rate", target: "Monthly", measurement: "Recorded quantity ÷ actual count", weight: "12%" },
  { role: "Inventory & Logistics", category: "Stock Reconciliation", description: "Complete by 5th day", target: "Monthly", measurement: "Days past 5th to reconcile", weight: "8%" },
  { role: "Inventory & Logistics", category: "Stock-out Incidents", description: "≤2 incidents/month", target: "Monthly", measurement: "Count stock-out incidents", weight: "10%" },
  { role: "Inventory & Logistics", category: "Warehouse Organization", description: "≥90% audit score", target: "Monthly", measurement: "Audit score from checklist", weight: "10%" },
  { role: "Inventory & Logistics", category: "Delivery Timeliness", description: "≥95% on-time", target: "Monthly", measurement: "On-time deliveries ÷ total deliveries", weight: "12%" },
  { role: "Inventory & Logistics", category: "Truck Utilization", description: "≥85% utilization", target: "Monthly", measurement: "Actual load ÷ capacity or km", weight: "8%" },
  { role: "Inventory & Logistics", category: "Fuel Efficiency", description: "≤0.3 L/km", target: "Monthly", measurement: "Liters used ÷ km traveled", weight: "8%" },
  { role: "Inventory & Logistics", category: "Preventive Maintenance", description: "100% on schedule", target: "Monthly", measurement: "Completed PM ÷ scheduled PM", weight: "8%" },
  { role: "Inventory & Logistics", category: "Delivery Error Rate", description: "≤2 errors/month", target: "Monthly", measurement: "Count of incorrect deliveries", weight: "6%" },
  { role: "Inventory & Logistics", category: "Record Accuracy", description: "≥99% correct entries", target: "Monthly", measurement: "Correct entries ÷ total entries", weight: "8%" },
  
  // Accounting & Receivables
  { role: "Accounting & Receivables", category: "Sales Invoice Monitoring", description: "100% recorded", target: "Monthly", measurement: "Recorded SIs ÷ issued SIs", weight: "20%" },
  { role: "Accounting & Receivables", category: "Collection Monitoring", description: "100% collection within terms", target: "Monthly", measurement: "Collected within terms ÷ amount due", weight: "20%" },
  { role: "Accounting & Receivables", category: "Debit Memo Monitoring", description: "100% documented", target: "Monthly", measurement: "Documented memos ÷ issued memos", weight: "15%" },
  { role: "Accounting & Receivables", category: "Timely Payment of Bills", description: "100% paid on/before due", target: "Monthly", measurement: "Bills paid on time ÷ total bills", weight: "15%" },
  { role: "Accounting & Receivables", category: "Petty Cash Monitoring", description: "No shortages, complete docs", target: "Monthly", measurement: "Assess shortages and documentation", weight: "10%" },
  { role: "Accounting & Receivables", category: "Reporting Accuracy", description: "Accurate, on-time reports", target: "Monthly", measurement: "Evaluate report timeliness/accuracy", weight: "10%" },
  { role: "Accounting & Receivables", category: "Process Improvement", description: "≥1 suggestion/quarter", target: "Quarterly", measurement: "Count implemented suggestions", weight: "10%" },
  
  // Documentation & Projects
  { role: "Documentation & Projects", category: "Approved PO / Work Order", description: "Submitted within 2 days", target: "Per project", measurement: "Docs submitted on time ÷ required", weight: "15%" },
  { role: "Documentation & Projects", category: "Service Report", description: "Submitted within 2 days", target: "Per service call", measurement: "Reports on time ÷ total reports", weight: "20%" },
  { role: "Documentation & Projects", category: "Certificate of Acceptance", description: "Submitted within 3 days", target: "Per project", measurement: "Certificates on time ÷ total certificates", weight: "20%" },
  { role: "Documentation & Projects", category: "Delivery Receipt", description: "Same day as delivery", target: "Every delivery", measurement: "Proof of delivery on same day ÷ deliveries", weight: "15%" },
  { role: "Documentation & Projects", category: "Before-and-after Photos", description: "Compiled within 2 days", target: "Per project", measurement: "Photos compiled on time ÷ projects", weight: "10%" },
  { role: "Documentation & Projects", category: "Documentation Accuracy", description: "No errors or omissions", target: "Per submission", measurement: "Assess accuracy of documents", weight: "10%" },
  { role: "Documentation & Projects", category: "Client Feedback", description: "Positive feedback", target: "Per project", measurement: "Evaluate client satisfaction", weight: "10%" },
  
  // Production / Operations
  { role: "Production / Operations", category: "Production Efficiency", description: "Actual output ÷ maximum output", target: "Monthly", measurement: "(Actual ÷ Max output) × 100", weight: "15%" },
  { role: "Production / Operations", category: "Yield & Scrap Rate", description: "High yield, low scrap", target: "Monthly", measurement: "(Usable output ÷ input); (Scrap ÷ input)", weight: "15%" },
  { role: "Production / Operations", category: "Quality Control", description: "Low defects, high first-pass yield", target: "Monthly", measurement: "Defects ÷ total; first pass yield", weight: "15%" },
  { role: "Production / Operations", category: "Equipment Utilization", description: "High utilization rate", target: "Monthly", measurement: "Operating time ÷ available time", weight: "10%" },
  { role: "Production / Operations", category: "Energy Consumption", description: "Low kWh per ton", target: "Monthly", measurement: "Energy consumed ÷ output tonnage", weight: "10%" },
  { role: "Production / Operations", category: "Safety Metrics", description: "Low incident/injury rate", target: "Monthly", measurement: "Incidents per 1M hours worked", weight: "20%" },
  { role: "Production / Operations", category: "Inventory Turnover", description: "High turnover rate", target: "Monthly", measurement: "COGS ÷ average inventory", weight: "15%" }
];

function updateKPIDetails(isSteel) {
    const kpis = isSteel ? steelKPIs : woodKPIs;
    const tableBody = document.getElementById('kpiTableBody');
    const materialTitle = document.getElementById('materialTitle');
    const targetHeader = document.getElementById('targetHeader');
    const woodLabel = document.getElementById('woodLabel');
    const steelLabel = document.getElementById('steelLabel');
    
    // Update labels
    materialTitle.textContent = isSteel ? 'Steel' : 'Wood';
    woodLabel.classList.toggle('active', !isSteel);
    steelLabel.classList.toggle('active', isSteel);
    
    // Update header text
    targetHeader.textContent = isSteel ? 'Description / Target' : 'Description';
    
    // Clear table body
    tableBody.innerHTML = '';
    
    // Group KPIs by role
    const roles = {};
    kpis.forEach(kpi => {
        if (!roles[kpi.role]) {
            roles[kpi.role] = [];
        }
        roles[kpi.role].push(kpi);
    });
    
    // Populate table
    Object.keys(roles).forEach(role => {
        // Add role header row
        const roleRow = document.createElement('tr');
        roleRow.className = 'role-header';
        roleRow.innerHTML = `<td colspan="${isSteel ? 6 : 4}" style="background-color: #f5f5f5; font-weight: bold; font-size: 16px;">${role}</td>`;
        tableBody.appendChild(roleRow);
        
        // Add KPIs for this role
        roles[role].forEach(kpi => {
            const row = document.createElement('tr');
            
            if (isSteel) {
                row.innerHTML = `
                    <td></td>
                    <td>${kpi.category}</td>
                    <td>
                        <strong>${kpi.description}</strong>
                        ${kpi.target ? `<br><small class="text-muted"><strong>Frequency:</strong> ${kpi.target}</small>` : ''}
                        ${kpi.measurement ? `<br><small class="text-muted"><strong>Measurement:</strong> ${kpi.measurement}</small>` : ''}
                    </td>
                    <td>${kpi.weight}</td>
                `;
            } else {
                row.innerHTML = `
                    <td></td>
                    <td>${kpi.category}</td>
                    <td>${kpi.description}</td>
                    <td>${kpi.weight}</td>
                `;
            }
            
            tableBody.appendChild(row);
        });
    });
}

function printKPIDetails() {
    const isSteel = document.getElementById('materialToggle').checked;
    const materialType = isSteel ? 'Steel' : 'Wood';
    const kpis = isSteel ? steelKPIs : woodKPIs;
    
    var printWindow = window.open('', '_blank');
    var printContent = `
        <html>
        <head>
            <title>KPI Details - ${materialType}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #337ab7; color: white; }
                .role-header { background-color: #f5f5f5; font-weight: bold; }
                h1 { text-align: center; color: #337ab7; }
                .header-info { text-align: center; margin-bottom: 30px; }
                @media print { 
                    body { margin: 0; }
                    table { page-break-inside: auto; }
                    tr { page-break-inside: avoid; page-break-after: auto; }
                }
            </style>
        </head>
        <body>
            <h1>KPI Details - ${materialType}</h1>
            <div class="header-info">
                <p>Printed on ${new Date().toLocaleDateString()}</p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width:15%;">Role</th>
                        <th style="width:20%;">KPI Category</th>
                        <th>${isSteel ? 'Description / Target' : 'Description'}</th>
                        <th style="width:10%;">Weight</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    // Group KPIs by role for printing
    const roles = {};
    kpis.forEach(kpi => {
        if (!roles[kpi.role]) {
            roles[kpi.role] = [];
        }
        roles[kpi.role].push(kpi);
    });
    
    Object.keys(roles).forEach(role => {
        printContent += `
            <tr class="role-header">
                <td colspan="${isSteel ? 4 : 3}" style="background-color: #f5f5f5; font-weight: bold;">${role}</td>
            </tr>
        `;
        
        roles[role].forEach(kpi => {
            if (isSteel) {
                printContent += `
                    <tr>
                        <td></td>
                        <td>${kpi.category}</td>
                        <td>
                            <strong>${kpi.description}</strong>
                            ${kpi.target ? `<br><small><strong>Frequency:</strong> ${kpi.target}</small>` : ''}
                            ${kpi.measurement ? `<br><small><strong>Measurement:</strong> ${kpi.measurement}</small>` : ''}
                        </td>
                        <td>${kpi.weight}</td>
                    </tr>
                `;
            } else {
                printContent += `
                    <tr>
                        <td></td>
                        <td>${kpi.category}</td>
                        <td>${kpi.description}</td>
                        <td>${kpi.weight}</td>
                    </tr>
                `;
            }
        });
    });
    
    printContent += `
                </tbody>
            </table>
        </body>
        </html>
    `;
    
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

$(function(){
    // Initialize with wood KPIs
    updateKPIDetails(false);

    // Handle toggle switch
    document.getElementById('materialToggle').addEventListener('change', function() {
        updateKPIDetails(this.checked);
    });
    
    // Add hover effects to table rows (excluding role headers)
    $('#kpi_details_table').on('mouseenter', 'tbody tr:not(.role-header)', function() {
        $(this).addClass('hover-highlight');
    }).on('mouseleave', 'tbody tr:not(.role-header)', function() {
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

.table tbody tr:hover:not(.role-header),
.table tbody tr.hover-highlight {
  cursor: pointer;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.role-header {
  background-color: #f5f5f5 !important;
  font-weight: bold;
  font-size: 16px;
}

.role-header:hover {
  transform: none !important;
  box-shadow: none !important;
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
  .table th, .table td {
    font-size: 12px;
    padding: 8px;
    white-space: normal;
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