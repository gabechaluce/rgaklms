<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Task Evaluation Dashboard
        <small>Performance Overview & Analytics</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>

    <section class="content">
      <?php
      // Get distinct months with evaluations
      $months_sql = "SELECT DISTINCT DATE_FORMAT(assigned_date, '%Y-%m') as month 
                     FROM evaluations 
                     ORDER BY month DESC";
      $months_result = $conn->query($months_sql);
      $available_months = [];
      while($month_row = $months_result->fetch_assoc()) {
          $available_months[] = $month_row['month'];
      }
      
      // Get selected month from URL or default to current month
      $selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
      $material_type = isset($_GET['material']) ? $_GET['material'] : 'Wood';
      $prev_month = date('Y-m', strtotime($selected_month . ' -1 month'));
      $next_month = date('Y-m', strtotime($selected_month . ' +1 month'));
      $current_month = date('Y-m');
      
      // Format for display
      $display_month = date('F Y', strtotime($selected_month));
      
      // Get dashboard statistics for selected month
      $start_date = date('Y-m-01', strtotime($selected_month));
      $end_date = date('Y-m-t', strtotime($selected_month));
      
      // Total evaluations
      $total_sql = "SELECT COUNT(*) as total FROM evaluations WHERE material_type = '$material_type'";
      $total_result = $conn->query($total_sql);
      $total_evaluations = $total_result->fetch_assoc()['total'];
      
      // Selected month evaluations
      $month_sql = "SELECT COUNT(*) as monthly FROM evaluations 
                    WHERE assigned_date BETWEEN '$start_date' AND '$end_date'
                    AND material_type = '$material_type'";
      $month_result = $conn->query($month_sql);
      $monthly_evaluations = $month_result->fetch_assoc()['monthly'];
      
      // On-time completion rate for selected month
      $ontime_sql = "SELECT 
                       COUNT(*) as total,
                       SUM(CASE WHEN on_time = 'Yes' THEN 1 ELSE 0 END) as on_time
                     FROM evaluations 
                     WHERE actual_completion_date IS NOT NULL
                     AND assigned_date BETWEEN '$start_date' AND '$end_date'
                     AND material_type = '$material_type'";
      $ontime_result = $conn->query($ontime_sql);
      $ontime_data = $ontime_result->fetch_assoc();
      $ontime_rate = $ontime_data['total'] > 0 ? round(($ontime_data['on_time'] / $ontime_data['total']) * 100, 1) : 0;
      
      // QC Pass rate for selected month
      $qc_sql = "SELECT 
                   COUNT(*) as total,
                   SUM(CASE WHEN qc_passed = 'Yes' THEN 1 ELSE 0 END) as qc_passed
                 FROM evaluations 
                 WHERE assigned_date BETWEEN '$start_date' AND '$end_date'
                 AND material_type = '$material_type'";
      $qc_result = $conn->query($qc_sql);
      $qc_data = $qc_result->fetch_assoc();
      $qc_rate = $qc_data['total'] > 0 ? round(($qc_data['qc_passed'] / $qc_data['total']) * 100, 1) : 0;
      
      // Average KPI for selected month
      $kpi_sql = "SELECT AVG(overall_kpi_percentage) as avg_kpi 
                  FROM evaluations 
                  WHERE overall_kpi_percentage IS NOT NULL
                  AND assigned_date BETWEEN '$start_date' AND '$end_date'
                  AND material_type = '$material_type'";
      $kpi_result = $conn->query($kpi_sql);
      $avg_kpi = $kpi_result->fetch_assoc()['avg_kpi'];
      $avg_kpi = $avg_kpi ? round($avg_kpi, 1) : 0;
      
      // Role mapping for both material types
      $wood_roles = array(
        1 => 'Admin', 2 => 'Project Coordinator', 3 => 'Employee', 4 => 'Designer',
        5 => 'Inventory Coordinator', 6 => 'Estimator', 7 => 'Accounting',
        8 => 'Production Supervisor', 9 => 'Fabricator', 10 => 'CNC Operator',
        11 => 'Painter', 12 => 'Electrician', 13 => 'Human Resource', 14 => 'Project Manager'
      );
      
      $steel_roles = array(
        1 => 'HR & Admin', 2 => 'Inventory & Logistics', 3 => 'Accounting & Receivables',
        4 => 'Documentation & Projects', 5 => 'Production / Operations'
      );
      
      $type_arr = ($material_type == 'Wood') ? $wood_roles : $steel_roles;
      ?>

      <!-- Material Type Toggle -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-body text-center">
              <div class="btn-group">
                <a href="home.php?month=<?php echo $selected_month; ?>&material=Wood" 
                   class="btn btn-<?php echo ($material_type == 'Wood') ? 'primary' : 'default'; ?>">
                  Wood KPI
                </a>
                <a href="home.php?month=<?php echo $selected_month; ?>&material=Steel" 
                   class="btn btn-<?php echo ($material_type == 'Steel') ? 'primary' : 'default'; ?>">
                  Steel KPI
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Month Navigation -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-body text-center">
              <div class="btn-group">
                <a href="home.php?month=<?php echo $prev_month; ?>&material=<?php echo $material_type; ?>" class="btn btn-default">
                  <i class="fa fa-chevron-left"></i> Previous Month
                </a>
                <div class="btn-group">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" 
                          aria-expanded="false" id="monthDropdownBtn">
                    <i class="fa fa-calendar"></i> <?php echo $display_month; ?> 
                    <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-center" role="menu" 
                      aria-labelledby="monthDropdownBtn">
                    <?php foreach($available_months as $month): ?>
                      <?php 
                        $month_display = date('F Y', strtotime($month));
                        $is_current = ($month == $selected_month) ? 'active' : '';
                      ?>
                      <li class="<?php echo $is_current; ?>">
                        <a href="home.php?month=<?php echo $month; ?>&material=<?php echo $material_type; ?>">
                          <?php echo $month_display; ?>
                        </a>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                </div>
                <?php if ($next_month <= $current_month): ?>
                  <a href="home.php?month=<?php echo $next_month; ?>&material=<?php echo $material_type; ?>" class="btn btn-default">
                    Next Month <i class="fa fa-chevron-right"></i>
                  </a>
                <?php else: ?>
                  <button class="btn btn-default" disabled>
                    Next Month <i class="fa fa-chevron-right"></i>
                  </button>
                <?php endif; ?>
                <?php if ($selected_month != $current_month): ?>
                  <a href="home.php?material=<?php echo $material_type; ?>" class="btn btn-success">
                    <i class="fa fa-refresh"></i> Current Month
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards Row -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua floating-box">
            <div class="inner">
              <h3><?php echo number_format($total_evaluations); ?></h3>
              <p>Total Evaluations</p>
            </div>
            <div class="icon">
              <i class="fa fa-tasks"></i>
            </div>
            <a href="evaluationList.php?material=<?php echo $material_type; ?>" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green floating-box">
            <div class="inner">
              <h3><?php echo number_format($monthly_evaluations); ?></h3>
              <p>This Month</p>
            </div>
            <div class="icon">
              <i class="fa fa-calendar"></i>
            </div>
            <a href="evaluationList.php?month=<?php echo $selected_month; ?>&material=<?php echo $material_type; ?>" class="small-box-footer">
              More info <i class="fa fa-arrow-circle-right"></i>
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow floating-box">
            <div class="inner">
              <h3><?php echo $ontime_rate; ?>%</h3>
              <p>On-Time Rate</p>
            </div>
            <div class="icon">
              <i class="fa fa-clock-o"></i>
            </div>
            <a href="#" class="small-box-footer">
              Performance Metric
            </a>
          </div>
        </div>

        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red floating-box">
            <div class="inner">
              <h3><?php echo $avg_kpi; ?>%</h3>
              <p>Average KPI</p>
            </div>
            <div class="icon">
              <i class="fa fa-bar-chart"></i>
            </div>
            <a href="#" class="small-box-footer">
              Overall Performance 
            </a>
          </div>
        </div>
      </div>

      <!-- Performance Distribution Chart -->
      <div class="row">
        <div class="col-md-12">
          <div class="box floating-box chart-loading">
            <div class="box-header with-border">
              <h3 class="box-title">
                <i class="fa fa-pie-chart"></i> Performance Distribution
              </h3>
            </div>
            <div class="box-body">
              <div class="chart-3d-wrapper">
                <div class="chart-3d-container">
                  <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                      <canvas id="colorDistributionChart" style="height: 350px;"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Chart Statistics -->
              <div class="row" style="margin-top: 20px;">
                <div class="col-md-3 col-sm-6">
                  <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Excellent</span>
                      <span class="info-box-number" id="greenCount">-</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Good</span>
                      <span class="info-box-number" id="yellowCount">-</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <div class="info-box bg-orange">
                    <span class="info-box-icon"><i class="fa fa-warning"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Fair</span>
                      <span class="info-box-number" id="orangeCount">-</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 col-sm-6">
                  <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-times-circle"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Needs Improvement</span>
                      <span class="info-box-number" id="redCount">-</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Role Performance & QC Statistics -->
      <div class="row">
     <!-- Performance by Role -->
<div class="col-md-6">
  <div class="box floating-box">
    <div class="box-header with-border">
      <h3 class="box-title">
        <i class="fa fa-users"></i> Performance by Role
      </h3>
    </div>
    <div class="box-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>Role</th>
              <th>Total Tasks</th>
              <th>Avg KPI</th>
              <th>On-Time Rate</th>
              <th>QC Pass Rate</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $role_sql = "SELECT 
                           role,
                           COUNT(*) as total_tasks,
                           AVG(overall_kpi_percentage) as avg_kpi,
                           SUM(CASE WHEN on_time = 'Yes' THEN 1 ELSE 0 END) / COUNT(*) * 100 as ontime_rate,
                           SUM(CASE WHEN qc_passed = 'Yes' THEN 1 ELSE 0 END) / COUNT(*) * 100 as qc_rate
                         FROM evaluations 
                         WHERE assigned_date BETWEEN '$start_date' AND '$end_date'
                         AND material_type = '$material_type'
                         GROUP BY role 
                         ORDER BY total_tasks DESC";
            $role_result = $conn->query($role_sql);
            
            while($role_row = $role_result->fetch_assoc()) {
              // FIXED: Proper role mapping
              $role_id = $role_row['role'];
              $role_name = isset($type_arr[$role_id]) ? $type_arr[$role_id] : $role_id;
              
              $kpi_class = $role_row['avg_kpi'] >= 80 ? 'success' : ($role_row['avg_kpi'] >= 60 ? 'warning' : 'danger');
              
              echo "<tr>";
              echo "<td><strong>".$role_name."</strong></td>";
              echo "<td><span class='badge bg-blue'>".$role_row['total_tasks']."</span></td>";
              echo "<td><span class='label label-".$kpi_class."'>".number_format($role_row['avg_kpi'], 1)."%</span></td>";
              echo "<td><span class='label label-info'>".number_format($role_row['ontime_rate'], 1)."%</span></td>";
              echo "<td><span class='label label-primary'>".number_format($role_row['qc_rate'], 1)."%</span></td>";
              echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Top Performers -->
<div class="col-md-6">
  <div class="box floating-box">
    <div class="box-header with-border">
      <h3 class="box-title">
        <i class="fa fa-trophy"></i> Top Performers (This Month)
      </h3>
    </div>
    <div class="box-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Rank</th>
              <th>Team Member</th>
              <th>Tasks</th>
              <th>Avg KPI</th>
              <th>Performance</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Get all team members with their task counts and avg KPI
            $top_sql = "SELECT 
                          e.team_member_name,
                          CONCAT(u.firstname, ' ', u.lastname) as member_name,
                          COUNT(*) as task_count,
                          AVG(e.overall_kpi_percentage) as avg_kpi
                        FROM evaluations e
                        LEFT JOIN users u ON e.team_member_name = u.id
                        WHERE e.assigned_date BETWEEN '$start_date' AND '$end_date'
                          AND e.overall_kpi_percentage IS NOT NULL
                          AND e.material_type = '$material_type'
                        GROUP BY e.team_member_name
                        ORDER BY 
                          CASE WHEN COUNT(*) >= 2 THEN 0 ELSE 1 END,  -- Priority: 2+ tasks first
                          CASE WHEN COUNT(*) >= 2 THEN AVG(e.overall_kpi_percentage) ELSE 0 END DESC,  -- Then by avg KPI for 2+ tasks
                          CASE WHEN COUNT(*) = 1 THEN AVG(e.overall_kpi_percentage) ELSE 0 END DESC   -- Then by avg KPI for 1 task
                        LIMIT 10";
            
            $top_result = $conn->query($top_sql);
            $rank = 1;
            
            while($top_row = $top_result->fetch_assoc()) {
              // Handle missing names
              $member_name = !empty($top_row['member_name']) ? $top_row['member_name'] : $top_row['team_member_name'];
              
              $performance_class = $top_row['avg_kpi'] >= 90 ? 'success' : 
                                 ($top_row['avg_kpi'] >= 80 ? 'warning' : 
                                 ($top_row['avg_kpi'] >= 70 ? 'info' : 'danger'));
              
              $medal = '';
              if($rank == 1) $medal = '<i class="fa fa-trophy text-yellow"></i>';
              elseif($rank == 2) $medal = '<i class="fa fa-trophy text-gray"></i>';
              elseif($rank == 3) $medal = '<i class="fa fa-trophy text-orange"></i>';
              
              // Add visual indicator for task priority
              $task_indicator = '';
              if($top_row['task_count'] >= 2) {
                $task_indicator = '<i class="fa fa-star text-blue" title="Multiple tasks priority"></i> ';
              }
              
              echo "<tr>";
              echo "<td>".$medal." ".$rank."</td>";
              echo "<td>".$task_indicator."<strong>".$member_name."</strong></td>";
              echo "<td><span class='badge bg-blue'>".$top_row['task_count']."</span></td>";
              echo "<td><span class='label label-".$performance_class."'>".number_format($top_row['avg_kpi'], 1)."%</span></td>";
              echo "<td>";
              $stars = floor($top_row['avg_kpi'] / 20);
              for($i = 0; $i < $stars; $i++) {
                echo '<i class="fa fa-star text-yellow"></i>';
              }
              for($i = $stars; $i < 5; $i++) {
                echo '<i class="fa fa-star-o text-muted"></i>';
              }
              echo "</td>";
              echo "</tr>";
              $rank++;
            }
            
            // If no results found
            if($top_result->num_rows == 0) {
              echo "<tr><td colspan='5' class='text-center text-muted'>No performance data available for this period</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="box-footer">
        <small class="text-muted">
          <i class="fa fa-info-circle"></i> 
          Team members with 2+ tasks are prioritized in ranking, regardless of average KPI.
        </small>
      </div>
    </div>
  </div>
</div>

<!-- Recent Evaluations -->
<div class="row">
  <div class="col-md-12">
    <div class="box floating-box">
      <div class="box-header with-border">
        <h3 class="box-title">
          <i class="fa fa-history"></i> Recent Evaluations
        </h3>
        <div class="box-tools pull-right">
          <a href="evaluationList.php?material=<?php echo $material_type; ?>" class="btn btn-primary btn-sm btn-flat">
            <i class="fa fa-list"></i> View All
          </a>
        </div>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Project</th>
                <th>Team Member</th>
                <th>Role</th>
                <th>Assigned Date</th>
                <th>Status</th>
                <th>KPI</th>
                <th>Color Code</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $recent_sql = "SELECT e.*, 
                                    CONCAT(u.firstname, ' ', u.lastname) as member_name,
                                    u.id as user_id
                             FROM evaluations e 
                             LEFT JOIN users u ON e.team_member_name = u.id 
                             WHERE e.assigned_date BETWEEN '$start_date' AND '$end_date'
                             AND e.material_type = '$material_type'
                             ORDER BY e.id DESC 
                             LIMIT 10";
              $recent_result = $conn->query($recent_sql);
              
              while($recent_row = $recent_result->fetch_assoc()) {
                // FIXED: Proper role mapping
                $role_id = $recent_row['role'];
                $role_name = isset($type_arr[$role_id]) ? $type_arr[$role_id] : $role_id;
                
                // FIXED: Handle missing names
                $member_name = !empty($recent_row['member_name']) ? 
                              $recent_row['member_name'] : 
                              $recent_row['team_member_name'];
                
                $assigned_date = date('M d, Y', strtotime($recent_row['assigned_date']));
                
                // Status based on completion and QC
                $status = 'In Progress';
                $status_class = 'warning';
                if($recent_row['actual_completion_date']) {
                  if($recent_row['qc_passed'] == 'Yes') {
                    $status = 'Completed';
                    $status_class = 'success';
                  } else {
                    $status = 'Needs Review';
                    $status_class = 'danger';
                  }
                }
                
                // Color code styling
                $color_style = '';
                switch($recent_row['color_code']) {
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
                
                echo "<tr>";
                echo "<td><strong>".$recent_row['project_name']."</strong></td>";
                echo "<td>".$member_name."</td>"; // FIXED: Use correct name
                echo "<td><span class='label label-info'>".$role_name."</span></td>"; // FIXED: Use mapped role
                echo "<td>".$assigned_date."</td>";
                echo "<td><span class='label label-".$status_class."'>".$status."</span></td>";
                echo "<td><span class='label label-primary'>".number_format($recent_row['overall_kpi_percentage'], 1)."%</span></td>";
                echo "<td><span style='".$color_style."'>".$recent_row['color_code']."</span></td>";
                echo "</tr>";
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

<?php include 'includes/scripts.php'; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
$(document).ready(function(){
    // Get selected month and material from URL
    const urlParams = new URLSearchParams(window.location.search);
    const selectedMonth = urlParams.get('month') || '<?php echo date('Y-m'); ?>';
    const materialType = urlParams.get('material') || 'Wood';
    
    // Click handlers for the color statistic boxes
    $('.info-box.bg-green').on('click', function() {
        window.location.href = 'evaluationList.php?color_filter=Green&month=' + selectedMonth + '&material=' + materialType;
    });
    
    $('.info-box.bg-yellow').on('click', function() {
        window.location.href = 'evaluationList.php?color_filter=Yellow&month=' + selectedMonth + '&material=' + materialType;
    });
    
    $('.info-box.bg-orange').on('click', function() {
        window.location.href = 'evaluationList.php?color_filter=Orange&month=' + selectedMonth + '&material=' + materialType;
    });
    
    $('.info-box.bg-red').on('click', function() {
        window.location.href = 'evaluationList.php?color_filter=Red&month=' + selectedMonth + '&material=' + materialType;
    });
    
    // Color Distribution Chart
    $.ajax({
        url: 'get_dashboard_data.php',
        type: 'POST',
        data: {
            action: 'color_distribution',
            month: selectedMonth,
            material: materialType
        },
        dataType: 'json',
        success: function(data) {
            const ctx = document.getElementById('colorDistributionChart').getContext('2d');
            
            // Color mappings
            const colorConfig = {
                'Green': { bg: '#28a745', hover: '#34ce57', icon: 'check-circle' },
                'Yellow': { bg: '#ffc107', hover: '#ffcd39', icon: 'exclamation-triangle' },
                'Orange': { bg: '#fd7e14', hover: '#ff8c42', icon: 'warning' },
                'Red': { bg: '#dc3545', hover: '#e55a5a', icon: 'times-circle' }
            };
            
            // Update statistic boxes
            updateStatistics(data);
            
            // Create the chart
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: data.labels.map(label => colorConfig[label]?.bg || '#6c757d'),
                        borderWidth: 4,
                        borderColor: '#ffffff',
                        hoverBackgroundColor: data.labels.map(label => colorConfig[label]?.hover || '#8a939b'),
                        hoverBorderWidth: 6,
                        hoverOffset: 20,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.85)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#fff',
                            borderWidth: 2,
                            cornerRadius: 12,
                            displayColors: true,
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                title: function(context) {
                                    return context[0].label + ' Performance';
                                },
                                label: function(context) {
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return [
                                        `Count: ${value} evaluations`,
                                        `Percentage: ${percentage}%`,
                                        `Click to view details`
                                    ];
                                }
                            }
                        }
                    },
                    onClick: (event, activeElements) => {
                        if (activeElements.length > 0) {
                            const clickedIndex = activeElements[0].index;
                            const clickedLabel = data.labels[clickedIndex];
                            
                            if (clickedLabel) {
                                window.location.href = 'evaluationList.php?color_filter=' + encodeURIComponent(clickedLabel) + 
                                    '&month=' + selectedMonth + '&material=' + materialType;
                            }
                        }
                    }
                }
            });
        }
    });
    
    // Helper function to update statistics
    function updateStatistics(data) {
        const colorMap = {
            'Green': 'greenCount',
            'Yellow': 'yellowCount', 
            'Orange': 'orangeCount',
            'Red': 'redCount'
        };
        
        data.labels.forEach((label, index) => {
            const elementId = colorMap[label];
            if (elementId) {
                $('#' + elementId).text(data.values[index]);
            }
        });
    }
    
    // Highlight current month in dropdown
    $('.dropdown-menu li.active a').css({
        'font-weight': 'bold',
        'background-color': '#3c8dbc',
        'color': 'white'
    });

    // Make dropdown menu scrollable
    $('.dropdown-menu').css({
        'max-height': '300px',
        'overflow-y': 'auto'
    });
});
</script>

<style>
/* Floating Box */
.floating-box {
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.floating-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Small boxes for statistics */
.small-box.floating-box .inner h3 {
    font-size: 38px;
    font-weight: bold;
}

.small-box.floating-box .inner p {
    font-size: 15px;
}

/* Background color consistency */
body, .wrapper, .content-wrapper {
    background-color: #f4f1ed !important;
}

/* Table styling */
.table th {
    background-color: #f8f9fa;
    font-weight: bold;
    text-align: center;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

/* Badge and label styling */
.badge, .label {
    font-size: 11px;
    padding: 4px 8px;
}

/* Chart containers */
.box-body canvas {
    max-height: 300px !important;
}

/* Trophy colors */
.text-yellow { color: #f39c12 !important; }
.text-gray { color: #95a5a6 !important; }
.text-orange { color: #e67e22 !important; }

/* Star ratings */
.fa-star, .fa-star-o {
    font-size: 12px;
    margin-right: 1px;
}

/* Performance metrics */
.label-success { background-color: #5cb85c !important; }
.label-warning { background-color: #f0ad4e !important; }
.label-danger { background-color: #d9534f !important; }
.label-info { background-color: #5bc0de !important; }
.label-primary { background-color: #337ab7 !important; }

/* Responsive adjustments */
@media (max-width: 768px) {
    .small-box.floating-box .inner h3 {
        font-size: 24px;
    }
    
    .small-box.floating-box .inner p {
        font-size: 12px;
    }
    
    .table {
        font-size: 12px;
    }
}

/* Box header improvements */
.box-header.with-border {
    border-bottom: 1px solid #e0e0e0;
    padding: 15px;
}

.box-title {
    font-size: 16px;
    font-weight: bold;
    color: #333;
}

/* Scrollable tables on mobile */
@media (max-width: 768px) {
    .table-responsive {
        border: none;
    }
}

/* Month navigation styles */
.box-primary {
    border-radius: 10px;
    margin-bottom: 20px;
}

.btn-group .btn {
    border-radius: 5px;
    margin: 0 5px;
    font-weight: bold;
}

.btn-group .btn-default {
    background-color: #f8f9fa;
    border-color: #ddd;
}

.btn-group .btn-default:hover {
    background-color: #e9ecef;
}

.btn-group .btn-primary {
    background-color: #3c8dbc;
    border-color: #367fa9;
}

.btn-group .btn-success {
    background-color: #00a65a;
    border-color: #008d4c;
}

/* Center dropdown menu */
.dropdown-menu-center {
    left: 50% !important;
    right: auto !important;
    text-align: center !important;
    transform: translate(-50%, 0) !important;
}

/* Highlight active month */
.dropdown-menu li.active a {
    font-weight: bold;
    background-color: #3c8dbc;
    color: white !important;
}

/* Hover effect */
.dropdown-menu li a:hover {
    background-color: #f5f5f5;
}

/* Material toggle buttons */
.btn-group .btn.active {
    background-color: #3c8dbc;
    color: white;
}
</style>

</body>
</html>