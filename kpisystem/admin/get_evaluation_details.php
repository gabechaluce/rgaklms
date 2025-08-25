<?php
include 'includes/session.php';

if(isset($_POST['id'])) {
    $id = $_POST['id'];
    
    // Get evaluation details with created by name
    $sql = "SELECT e.*, u2.firstname as created_by_name, u2.lastname as created_by_lastname
            FROM evaluations e 
            LEFT JOIN users u2 ON e.created_by = u2.id
            WHERE e.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()) {
        // Get role name directly from database
        $role_name = $row['role'];
        
        // Format dates
        $assigned_date = date('F d, Y', strtotime($row['assigned_date']));
        $due_date = date('F d, Y', strtotime($row['due_date']));
        $completion_date = $row['actual_completion_date'] ? date('F d, Y', strtotime($row['actual_completion_date'])) : 'Not completed yet';
        
        // Color coding style with improved, vibrant but professional colors
        $color_style = '';
        $icon = '';
        switch($row['color_code']) {
            case 'Green':
                $color_style = 'background-color: #e8f5e9; color: #2e7d32; border-left: 4px solid #43a047; padding: 8px 12px; border-radius: 0 4px 4px 0; font-weight: bold;';
                $icon = '<i class="fa fa-check-circle" style="margin-right: 5px;"></i>';
                break;
            case 'Yellow':
                $color_style = 'background-color: #fff8e1; color: #ff8f00; border-left: 4px solid #ffc107; padding: 8px 12px; border-radius: 0 4px 4px 0; font-weight: bold;';
                $icon = '<i class="fa fa-exclamation-circle" style="margin-right: 5px;"></i>';
                break;
            case 'Orange':
                $color_style = 'background-color: #fff3e0; color: #e65100; border-left: 4px solid #fb8c00; padding: 8px 12px; border-radius: 0 4px 4px 0; font-weight: bold;';
                $icon = '<i class="fa fa-exclamation-triangle" style="margin-right: 5px;"></i>';
                break;
            case 'Red':
                $color_style = 'background-color: #ffebee; color: #c62828; border-left: 4px solid #e53935; padding: 8px 12px; border-radius: 0 4px 4px 0; font-weight: bold;';
                $icon = '<i class="fa fa-times-circle" style="margin-right: 5px;"></i>';
                break;
            default:
                $color_style = 'background-color: #f5f5f5; color: #424242; border-left: 4px solid #9e9e9e; padding: 8px 12px; border-radius: 0 4px 4px 0; font-weight: bold;';
                $icon = '<i class="fa fa-question-circle" style="margin-right: 5px;"></i>';
        }
        
        // Calculate days difference for timeline
        $assigned = new DateTime($row['assigned_date']);
        $due = new DateTime($row['due_date']);
        $completed = $row['actual_completion_date'] ? new DateTime($row['actual_completion_date']) : null;
        
        $days_assigned = $assigned->diff($due)->days;
        $days_taken = $completed ? $assigned->diff($completed)->days : 'N/A';
        
        echo "<div class='row' style='font-size: 1.05em;'>";
        echo "<div class='col-md-6'>";
        echo "<h4 style='font-size: 1.25em; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;'><i class='fa fa-info-circle' style='color: #2196F3;'></i> Basic Information</h4>";
        echo "<table class='table table-borderless table-sm'>";
        echo "<tr><td style='width: 40%'><strong>Project Name:</strong></td><td>".htmlspecialchars($row['project_name'])."</td></tr>";
        echo "<tr><td><strong>Client Name:</strong></td><td>".htmlspecialchars($row['client_name'])."</td></tr>";
        echo "<tr><td><strong>Role:</strong></td><td>".htmlspecialchars($role_name)."</td></tr>";
        echo "<tr><td><strong>Team Member:</strong></td><td>".htmlspecialchars($row['team_member_name'])."</td></tr>";
        echo "<tr><td><strong>Task Type:</strong></td><td><span class='badge badge-primary' style='background-color: #2196F3;'>".htmlspecialchars($row['task_type'])."</span></td></tr>";
        echo "<tr><td><strong>Created By:</strong></td><td>".htmlspecialchars($row['created_by_name']." ".$row['created_by_lastname'])."</td></tr>";
        echo "<tr><td><strong>Evaluation Type:</strong></td><td><span class='badge badge-".($row['material_type'] == 'Wood' ? 'warning' : 'info')."' style='background-color: ".($row['material_type'] == 'Wood' ? '#FF9800' : '#00BCD4')."'>".htmlspecialchars($row['material_type'])."</span></td></tr>";
        echo "</table>";
        echo "</div>";
        
        echo "<div class='col-md-6'>";
        echo "<h4 style='font-size: 1.25em; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;'><i class='fa fa-calendar' style='color: #4CAF50;'></i> Timeline</h4>";
        echo "<table class='table table-borderless table-sm'>";
        echo "<tr><td style='width: 40%'><strong>Assigned Date:</strong></td><td>".$assigned_date."</td></tr>";
        echo "<tr><td><strong>Due Date:</strong></td><td>".$due_date."</td></tr>";
        echo "<tr><td><strong>Days Allocated:</strong></td><td>".$days_assigned." days</td></tr>";
        echo "<tr><td><strong>Completion Date:</strong></td><td>".$completion_date."</td></tr>";
        if($completed) {
            echo "<tr><td><strong>Days Taken:</strong></td><td>".$days_taken." days</td></tr>";
        }
        echo "<tr><td><strong>On Time:</strong></td><td><span class='badge badge-".($row['on_time'] == 'Yes' ? 'success' : 'danger')."' style='background-color: ".($row['on_time'] == 'Yes' ? '#4CAF50' : '#F44336')."'>".$row['on_time']."</span></td></tr>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='row mt-3' style='font-size: 1.05em;'>";
        echo "<div class='col-md-12'>";
        echo "<h4 style='font-size: 1.25em; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;'><i class='fa fa-tasks' style='color: #FF9800;'></i> Task Description</h4>";
        echo "<div class='card bg-light p-3' style='border-left: 4px solid #FF9800; background-color: #fff8e1; border-radius: 0 4px 4px 0;'>";
        echo nl2br(htmlspecialchars($row['task_description']));
        echo "</div>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='row mt-3' style='font-size: 1.05em;'>";
        echo "<div class='col-md-6'>";
        echo "<h4 style='font-size: 1.25em; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;'><i class='fa fa-check-circle' style='color: #4CAF50;'></i> Quality Control</h4>";
        echo "<table class='table table-borderless table-sm'>";
        echo "<tr><td style='width: 40%'><strong>QC Passed:</strong></td><td><span class='badge badge-".($row['qc_passed'] == 'Yes' ? 'success' : 'danger')."' style='background-color: ".($row['qc_passed'] == 'Yes' ? '#4CAF50' : '#F44336')."'>".$row['qc_passed']."</span></td></tr>";
        echo "<tr><td><strong>Material Used:</strong></td><td><span class='badge badge-".($row['material_used'] == 'Yes' ? 'success' : 'danger')."' style='background-color: ".($row['material_used'] == 'Yes' ? '#4CAF50' : '#F44336')."'>".$row['material_used']."</span></td></tr>";
        
        // Show Error/Revision Category if exists
        if(!empty($row['error_category'])) {
            echo "<tr><td><strong>Error/Revision Category:</strong></td><td><span class='badge badge-warning' style='background-color: #FF9800;'>".htmlspecialchars($row['error_category'])."</span></td></tr>";
        }
        
        if(!empty($row['revisions_errors'])) {
            echo "<tr><td><strong>Revisions/Errors:</strong></td><td><div class='card bg-light p-2' style='border-left: 4px solid #FF9800; background-color: #fff8e1; border-radius: 0 4px 4px 0;'>".nl2br(htmlspecialchars($row['revisions_errors']))."</div></td></tr>";
        }
        echo "</table>";
        echo "</div>";
        
        echo "<div class='col-md-6'>";
        echo "<h4 style='font-size: 1.25em; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;'><i class='fa fa-bar-chart' style='color: #9C27B0;'></i> Performance Metrics</h4>";
        echo "<table class='table table-borderless table-sm'>";
        
        // Helper function for percentage bars
        function percentageBar($value, $thresholds = [70, 80, 90]) {
            $color = 'danger';
            if ($value >= $thresholds[2]) $color = 'success';
            elseif ($value >= $thresholds[1]) $color = 'primary';
            elseif ($value >= $thresholds[0]) $color = 'warning';
            
            $color_map = [
                'success' => '#4CAF50',
                'primary' => '#2196F3',
                'warning' => '#FF9800',
                'danger' => '#F44336'
            ];
            
            return "<div class='progress' style='height: 24px; border-radius: 4px;'>
                      <div class='progress-bar' role='progressbar' style='width: $value%; background-color: ".$color_map[$color]."; border-radius: 4px;' aria-valuenow='$value' aria-valuemin='0' aria-valuemax='100'>
                        <span style='font-size: 12px; line-height: 24px;'>$value%</span>
                      </div>
                    </div>";
        }
        
        if(!empty($row['output_percentage'])) {
            echo "<tr><td style='width: 40%'><strong>Output %:</strong></td><td>".percentageBar($row['output_percentage'])."</td></tr>";
        }
        if(!empty($row['timeliness_percentage'])) {
            echo "<tr><td><strong>Timeliness %:</strong></td><td>".percentageBar($row['timeliness_percentage'])."</td></tr>";
        }
        if(!empty($row['accuracy_percentage'])) {
            echo "<tr><td><strong>Accuracy %:</strong></td><td>".percentageBar($row['accuracy_percentage'])."</td></tr>";
        }
        if(!empty($row['teamwork_percentage'])) {
            echo "<tr><td><strong>Teamwork %:</strong></td><td>".percentageBar($row['teamwork_percentage'])."</td></tr>";
        }
        
        // Show Material Efficiency for Fabricator and CNC Operator roles
        if(in_array($row['role'], ['Fabricator', 'CNC Operator']) && !empty($row['material_efficiency_percentage'])) {
            echo "<tr><td><strong>Material Efficiency %:</strong></td><td>".percentageBar($row['material_efficiency_percentage'])."</td></tr>";
        }
        
        // Show Steel-specific metrics if evaluation is for Steel
        if($row['material_type'] == 'Steel') {
            if(!empty($row['production_efficiency_percentage'])) {
                echo "<tr><td><strong>Production Efficiency %:</strong></td><td>".percentageBar($row['production_efficiency_percentage'])."</td></tr>";
            }
            if(!empty($row['yield_percentage'])) {
                echo "<tr><td><strong>Yield %:</strong></td><td>".percentageBar($row['yield_percentage'])."</td></tr>";
            }
            if(!empty($row['scrap_rate_percentage'])) {
                echo "<tr><td><strong>Scrap Rate %:</strong></td><td>".percentageBar($row['scrap_rate_percentage'])."</td></tr>";
            }
            if(!empty($row['equipment_utilization_percentage'])) {
                echo "<tr><td><strong>Equipment Utilization %:</strong></td><td>".percentageBar($row['equipment_utilization_percentage'])."</td></tr>";
            }
            if(!empty($row['safety_score_percentage'])) {
                echo "<tr><td><strong>Safety Score %:</strong></td><td>".percentageBar($row['safety_score_percentage'])."</td></tr>";
            }
        }
        
        // Show Wood-specific metrics if evaluation is for Wood
        if($row['material_type'] == 'Wood') {
            if(!empty($row['client_satisfaction_score'])) {
                echo "<tr><td><strong>Client Satisfaction %:</strong></td><td>".percentageBar($row['client_satisfaction_score'])."</td></tr>";
            }
            if(!empty($row['planned_material_quantity'])) {
                echo "<tr><td><strong>Planned Material Qty:</strong></td><td>".htmlspecialchars($row['planned_material_quantity'])."</td></tr>";
            }
        }
        
        if(!empty($row['overall_kpi_percentage'])) {
            echo "<tr><td><strong>Overall KPI %:</strong></td><td>".percentageBar($row['overall_kpi_percentage'])."</td></tr>";
        }
        echo "<tr><td><strong>Color Code:</strong></td><td><span style='".$color_style."'>".$icon.$row['color_code']."</span></td></tr>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
        
        // Material and Cost Information
        if(!empty($row['waste_quantity']) || !empty($row['cost_per_unit']) || !empty($row['reason_for_waste'])) {
            echo "<div class='row mt-3' style='font-size: 1.05em;'>";
            echo "<div class='col-md-12'>";
            echo "<h4 style='font-size: 1.25em; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;'><i class='fa fa-money' style='color: #009688;'></i> Material & Cost Information</h4>";
            echo "<table class='table table-borderless table-sm'>";
            if(!empty($row['waste_quantity'])) {
                echo "<tr><td style='width: 40%'><strong>Waste Quantity:</strong></td><td>".htmlspecialchars($row['waste_quantity'])."</td></tr>";
            }
            if(!empty($row['cost_per_unit'])) {
                echo "<tr><td><strong>Cost per Unit:</strong></td><td>₱".number_format($row['cost_per_unit'], 2)."</td></tr>";
            }
            if(!empty($row['reason_for_waste'])) {
                echo "<tr><td><strong>Reason for Waste:</strong></td><td><div class='card bg-light p-2' style='border-left: 4px solid #FF9800; background-color: #fff8e1; border-radius: 0 4px 4px 0;'>".nl2br(htmlspecialchars($row['reason_for_waste']))."</div></td></tr>";
            }
            echo "</table>";
            echo "</div>";
            echo "</div>";
        }
        
        // Feedback and Notes - now inside styled boxes
        if(!empty($row['client_feedback'])) {
            echo "<div class='row mt-3' style='font-size: 1.05em;'>";
            echo "<div class='col-md-12'>";
            echo "<h4 style='font-size: 1.25em; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;'><i class='fa fa-comments' style='color: #2196F3;'></i> Client Feedback</h4>";
            echo "<div class='card' style='border-left: 4px solid #2196F3;'>";
            echo "<div class='card-body' style='background-color: #e3f2fd; padding: 15px; border-radius: 0 4px 4px 0;'>";
            echo nl2br(htmlspecialchars($row['client_feedback']));
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }

        if(!empty($row['note_issues'])) {
            echo "<div class='row mt-3' style='font-size: 1.05em;'>";
            echo "<div class='col-md-12'>";
            echo "<h4 style='font-size: 1.25em; color: #333; border-bottom: 2px solid #eee; padding-bottom: 8px;'><i class='fa fa-sticky-note' style='color: #FF9800;'></i> Notes/Issues</h4>";
            echo "<div class='card' style='border-left: 4px solid #FFC107;'>";
            echo "<div class='card-body' style='background-color: #fff8e1; padding: 15px; border-radius: 0 4px 4px 0;'>";
            echo nl2br(htmlspecialchars($row['note_issues']));
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
        
    } else {
        echo "<div class='alert alert-danger' style='font-size: 1.05em;'>Evaluation record not found.</div>";
    }
} else {
    echo "<div class='alert alert-danger' style='font-size: 1.05em;'>Invalid request.</div>";
}
?>