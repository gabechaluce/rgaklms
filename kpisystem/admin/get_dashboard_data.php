<?php
include 'includes/session.php';

header('Content-Type: application/json');

if(isset($_POST['action'])) {
    $action = $_POST['action'];
    $response = array();
    
    // Get month and material parameters
    $month = isset($_POST['month']) ? $_POST['month'] : date('Y-m');
    $material = isset($_POST['material']) ? $_POST['material'] : 'Wood';
    
    // Role mappings
    $wood_roles = array(
        1 => 'Admin',
        2 => 'Project Coordinator',
        3 => 'Employee',
        4 => 'Designer',
        5 => 'Inventory Coordinator',
        6 => 'Estimator',
        7 => 'Accounting',
        8 => 'Production Supervisor',
        9 => 'Fabricator',
        10 => 'CNC Operator',
        11 => 'Painter',
        12 => 'Electrician',
        13 => 'Human Resource',
        14 => 'Project Manager'
    );
    
    $steel_roles = array(
        1 => 'HR & Admin',
        2 => 'Inventory & Logistics',
        3 => 'Accounting & Receivables',
        4 => 'Documentation & Projects',
        5 => 'Production / Operations'
    );
    
    $type_arr = ($material == 'Wood') ? $wood_roles : $steel_roles;

    try {
        switch($action) {
            case 'color_distribution':
                // Get color code distribution for selected month and material
                $start_date = date('Y-m-01', strtotime($month));
                $end_date = date('Y-m-t', strtotime($month));
                
                $color_sql = "SELECT 
                                color_code,
                                COUNT(*) as count
                              FROM evaluations 
                              WHERE assigned_date BETWEEN '$start_date' AND '$end_date'
                              AND material_type = '$material'
                              GROUP BY color_code
                              ORDER BY 
                                CASE color_code
                                  WHEN 'Green' THEN 1
                                  WHEN 'Yellow' THEN 2
                                  WHEN 'Orange' THEN 3
                                  WHEN 'Red' THEN 4
                                  ELSE 5
                                END";
                
                $color_result = $conn->query($color_sql);
                
                $labels = array();
                $values = array();
                
                while($row = $color_result->fetch_assoc()) {
                    $labels[] = $row['color_code'];
                    $values[] = (int)$row['count'];
                }
                
                // Ensure all color codes are present even if count is 0
                $all_colors = ['Green', 'Yellow', 'Orange', 'Red'];
                foreach($all_colors as $color) {
                    if(!in_array($color, $labels)) {
                        $labels[] = $color;
                        $values[] = 0;
                    }
                }
                
                $response = array(
                    'labels' => $labels,
                    'values' => $values
                );
                break;
                
            case 'role_performance':
                // Get performance by role for selected month and material
                $start_date = date('Y-m-01', strtotime($month));
                $end_date = date('Y-m-t', strtotime($month));
                
                $role_sql = "SELECT 
                               role,
                               COUNT(*) as total_tasks,
                               AVG(overall_kpi_percentage) as avg_kpi,
                               SUM(CASE WHEN on_time = 'Yes' THEN 1 ELSE 0 END) / COUNT(*) * 100 as ontime_rate,
                               SUM(CASE WHEN qc_passed = 'Yes' THEN 1 ELSE 0 END) / COUNT(*) * 100 as qc_rate
                             FROM evaluations 
                             WHERE overall_kpi_percentage IS NOT NULL
                             AND assigned_date BETWEEN '$start_date' AND '$end_date'
                             AND material_type = '$material'
                             GROUP BY role 
                             ORDER BY total_tasks DESC";
                
                $role_result = $conn->query($role_sql);
                
                $roles = array();
                $total_tasks = array();
                $avg_kpis = array();
                $ontime_rates = array();
                $qc_rates = array();
                
                while($row = $role_result->fetch_assoc()) {
                    $role_name = isset($type_arr[$row['role']]) ? $type_arr[$row['role']] : 'Unknown';
                    $roles[] = $role_name;
                    $total_tasks[] = (int)$row['total_tasks'];
                    $avg_kpis[] = round($row['avg_kpi'], 1);
                    $ontime_rates[] = round($row['ontime_rate'], 1);
                    $qc_rates[] = round($row['qc_rate'], 1);
                }
                
                $response = array(
                    'roles' => $roles,
                    'total_tasks' => $total_tasks,
                    'avg_kpis' => $avg_kpis,
                    'ontime_rates' => $ontime_rates,
                    'qc_rates' => $qc_rates
                );
                break;
                
            case 'top_performers':
                // Get top performers for selected month and material
                $start_date = date('Y-m-01', strtotime($month));
                $end_date = date('Y-m-t', strtotime($month));
                
                $top_sql = "SELECT 
                              e.team_member_name,
                              CONCAT(u.firstname, ' ', u.lastname) as member_name,
                              COUNT(*) as task_count,
                              AVG(e.overall_kpi_percentage) as avg_kpi
                            FROM evaluations e
                            LEFT JOIN users u ON e.team_member_name = u.id
                            WHERE e.assigned_date BETWEEN '$start_date' AND '$end_date'
                              AND e.overall_kpi_percentage IS NOT NULL
                              AND e.material_type = '$material'
                            GROUP BY e.team_member_name
                            HAVING task_count >= 2
                            ORDER BY avg_kpi DESC, task_count DESC
                            LIMIT 5";
                
                $top_result = $conn->query($top_sql);
                $top_performers = array();
                
                while($row = $top_result->fetch_assoc()) {
                    $stars = min(5, floor($row['avg_kpi'] / 20));
                    $stars_html = str_repeat('★', $stars) . str_repeat('☆', 5 - $stars);
                    
                    $top_performers[] = array(
                        'name' => $row['member_name'] ? $row['member_name'] : $row['team_member_name'],
                        'task_count' => (int)$row['task_count'],
                        'avg_kpi' => round($row['avg_kpi'], 1),
                        'stars' => $stars_html
                    );
                }
                
                $response = array(
                    'top_performers' => $top_performers
                );
                break;
                
            case 'recent_evaluations':
                // Get recent evaluations for selected month and material
                $start_date = date('Y-m-01', strtotime($month));
                $end_date = date('Y-m-t', strtotime($month));
                
                $recent_sql = "SELECT 
                                 e.*, 
                                 CONCAT(u.firstname, ' ', u.lastname) as member_name
                               FROM evaluations e 
                               LEFT JOIN users u ON e.team_member_name = u.id 
                               WHERE e.assigned_date BETWEEN '$start_date' AND '$end_date'
                               AND e.material_type = '$material'
                               ORDER BY e.id DESC 
                               LIMIT 10";
                
                $recent_result = $conn->query($recent_sql);
                $recent_evaluations = array();
                
                while($row = $recent_result->fetch_assoc()) {
                    $role_name = isset($type_arr[$row['role']]) ? $type_arr[$row['role']] : 'Unknown';
                    
                    // Status based on completion and QC
                    $status = 'In Progress';
                    $status_class = 'warning';
                    if($row['actual_completion_date']) {
                        if($row['qc_passed'] == 'Yes') {
                            $status = 'Completed';
                            $status_class = 'success';
                        } else {
                            $status = 'Needs Review';
                            $status_class = 'danger';
                        }
                    }
                    
                    $recent_evaluations[] = array(
                        'project_name' => $row['project_name'],
                        'member_name' => $row['member_name'] ? $row['member_name'] : $row['team_member_name'],
                        'role' => $role_name,
                        'assigned_date' => date('M d, Y', strtotime($row['assigned_date'])),
                        'status' => $status,
                        'status_class' => $status_class,
                        'kpi' => number_format($row['overall_kpi_percentage'], 1),
                        'color_code' => $row['color_code']
                    );
                }
                
                $response = array(
                    'recent_evaluations' => $recent_evaluations
                );
                break;
                
            case 'kpi_stats':
                // Get KPI statistics for selected month and material
                $start_date = date('Y-m-01', strtotime($month));
                $end_date = date('Y-m-t', strtotime($month));
                
                // Total evaluations
                $total_sql = "SELECT COUNT(*) as total FROM evaluations WHERE material_type = '$material'";
                $total_result = $conn->query($total_sql);
                $total_evaluations = $total_result->fetch_assoc()['total'];
                
                // Selected month evaluations
                $month_sql = "SELECT COUNT(*) as monthly FROM evaluations 
                              WHERE assigned_date BETWEEN '$start_date' AND '$end_date'
                              AND material_type = '$material'";
                $month_result = $conn->query($month_sql);
                $monthly_evaluations = $month_result->fetch_assoc()['monthly'];
                
                // On-time completion rate
                $ontime_sql = "SELECT 
                                 COUNT(*) as total,
                                 SUM(CASE WHEN on_time = 'Yes' THEN 1 ELSE 0 END) as on_time
                               FROM evaluations 
                               WHERE actual_completion_date IS NOT NULL
                               AND assigned_date BETWEEN '$start_date' AND '$end_date'
                               AND material_type = '$material'";
                $ontime_result = $conn->query($ontime_sql);
                $ontime_data = $ontime_result->fetch_assoc();
                $ontime_rate = $ontime_data['total'] > 0 ? round(($ontime_data['on_time'] / $ontime_data['total']) * 100, 1) : 0;
                
                // QC Pass rate
                $qc_sql = "SELECT 
                             COUNT(*) as total,
                             SUM(CASE WHEN qc_passed = 'Yes' THEN 1 ELSE 0 END) as qc_passed
                           FROM evaluations 
                           WHERE assigned_date BETWEEN '$start_date' AND '$end_date'
                           AND material_type = '$material'";
                $qc_result = $conn->query($qc_sql);
                $qc_data = $qc_result->fetch_assoc();
                $qc_rate = $qc_data['total'] > 0 ? round(($qc_data['qc_passed'] / $qc_data['total']) * 100, 1) : 0;
                
                // Average KPI
                $kpi_sql = "SELECT AVG(overall_kpi_percentage) as avg_kpi 
                            FROM evaluations 
                            WHERE overall_kpi_percentage IS NOT NULL
                            AND assigned_date BETWEEN '$start_date' AND '$end_date'
                            AND material_type = '$material'";
                $kpi_result = $conn->query($kpi_sql);
                $avg_kpi = $kpi_result->fetch_assoc()['avg_kpi'];
                $avg_kpi = $avg_kpi ? round($avg_kpi, 1) : 0;
                
                $response = array(
                    'total_evaluations' => (int)$total_evaluations,
                    'monthly_evaluations' => (int)$monthly_evaluations,
                    'ontime_rate' => $ontime_rate,
                    'qc_rate' => $qc_rate,
                    'avg_kpi' => $avg_kpi
                );
                break;
                
            default:
                $response = array('error' => 'Invalid action');
                break;
        }
        
    } catch (Exception $e) {
        $response = array('error' => 'Database error: ' . $e->getMessage());
    }
    
} else {
    $response = array('error' => 'No action specified');
}

// Return JSON response
echo json_encode($response);
?>