<?php
include 'includes/session.php';

if(isset($_POST['submit'])){
    $material_type = $_POST['material_type'];
    $project_name = $_POST['project_name'];
    $client_name = $_POST['client_name'];
    $role = $_POST['role'];
    $team_member_name = $_POST['team_member_name'];
    $task_description = $_POST['task_description'];
    $assigned_date = $_POST['assigned_date'];
    $due_date = $_POST['due_date'];
    $actual_completion_date = $_POST['actual_completion_date'];
    $on_time = $_POST['on_time'];
    $revisions_errors = $_POST['revisions_errors'];
    $error_category = $_POST['error_category'] ?? null;
    $qc_passed = $_POST['qc_passed'];
    $material_used = $_POST['material_used'];
    $waste_quantity = $_POST['waste_quantity'];
    $cost_per_unit = $_POST['cost_per_unit'];
    $reason_for_waste = $_POST['reason_for_waste'];
    $client_feedback = $_POST['client_feedback'];
    $note_issues = $_POST['note_issues'];
    $task_type = $_POST['task_type'];
    $output_percentage = $_POST['output_percentage'];
    $timeliness_percentage = $_POST['timeliness_percentage'];
    $accuracy_percentage = $_POST['accuracy_percentage'];
    $teamwork_percentage = $_POST['teamwork_percentage'];
    $material_efficiency_percentage = $_POST['material_efficiency_percentage'] ?? null;
    $overall_kpi_percentage = $_POST['overall_kpi_percentage'];
    $color_code = $_POST['color_code'];
    $created_by = $user['id'];
    
    // Wood-specific fields
    $client_satisfaction_score = ($material_type == 'Wood' && isset($_POST['client_satisfaction_score'])) ? $_POST['client_satisfaction_score'] : null;
    $planned_material_quantity = ($material_type == 'Wood' && isset($_POST['planned_material_quantity'])) ? $_POST['planned_material_quantity'] : null;
    
    // Steel-specific fields
    $production_efficiency_percentage = ($material_type == 'Steel' && isset($_POST['production_efficiency_percentage'])) ? $_POST['production_efficiency_percentage'] : null;
    $yield_percentage = ($material_type == 'Steel' && isset($_POST['yield_percentage'])) ? $_POST['yield_percentage'] : null;
    $scrap_rate_percentage = ($material_type == 'Steel' && isset($_POST['scrap_rate_percentage'])) ? $_POST['scrap_rate_percentage'] : null;
    $equipment_utilization_percentage = ($material_type == 'Steel' && isset($_POST['equipment_utilization_percentage'])) ? $_POST['equipment_utilization_percentage'] : null;
    $energy_consumption = ($material_type == 'Steel' && isset($_POST['energy_consumption'])) ? $_POST['energy_consumption'] : null;
    $safety_score_percentage = ($material_type == 'Steel' && isset($_POST['safety_score_percentage'])) ? $_POST['safety_score_percentage'] : null;
    $inventory_turnover_rate = ($material_type == 'Steel' && isset($_POST['inventory_turnover_rate'])) ? $_POST['inventory_turnover_rate'] : null;

    // Validation
    $errors = array();
    
    if(empty($material_type)) {
        $errors[] = 'KPI Type (Wood/Steel) is required';
    }
    if(empty($project_name)) {
        $errors[] = 'Project name is required';
    }
    if(empty($client_name)) {
        $errors[] = 'Client name is required';
    }
    if(empty($role)) {
        $errors[] = 'Role is required';
    }
    if(empty($team_member_name)) {
        $errors[] = 'Team member name is required';
    }
    if(empty($task_description)) {
        $errors[] = 'Task description is required';
    }
    if(empty($assigned_date)) {
        $errors[] = 'Assigned date is required';
    }
    if(empty($due_date)) {
        $errors[] = 'Due date is required';
    }
    if(empty($on_time)) {
        $errors[] = 'On-time status is required';
    }
    if(empty($qc_passed)) {
        $errors[] = 'QC passed status is required';
    }
    if(empty($material_used)) {
        $errors[] = 'Material used status is required';
    }
    if(empty($task_type)) {
        $errors[] = 'Task type is required';
    }
    if(empty($color_code)) {
        $errors[] = 'Color code is required';
    }

    // Date validation
    if(!empty($assigned_date) && !empty($due_date)) {
        if(strtotime($assigned_date) > strtotime($due_date)) {
            $errors[] = 'Assigned date cannot be later than due date';
        }
    }

    if(!empty($actual_completion_date) && !empty($assigned_date)) {
        if(strtotime($actual_completion_date) < strtotime($assigned_date)) {
            $errors[] = 'Actual completion date cannot be earlier than assigned date';
        }
    }

    // Percentage validation
    $percentages = array(
        'output_percentage' => $output_percentage,
        'timeliness_percentage' => $timeliness_percentage,
        'accuracy_percentage' => $accuracy_percentage,
        'teamwork_percentage' => $teamwork_percentage,
        'material_efficiency_percentage' => $material_efficiency_percentage,
        'overall_kpi_percentage' => $overall_kpi_percentage,
        'client_satisfaction_score' => $client_satisfaction_score
    );

    foreach($percentages as $field => $value) {
        if(!empty($value) && ($value < 0 || $value > 100)) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' must be between 0 and 100';
        }
    }

    if(empty($errors)) {
        // Prepare values for insertion
        $actual_completion_date = !empty($actual_completion_date) ? "'".$actual_completion_date."'" : "NULL";
        $waste_quantity = !empty($waste_quantity) ? "'".$waste_quantity."'" : "NULL";
        $cost_per_unit = !empty($cost_per_unit) ? $cost_per_unit : "NULL";
        $reason_for_waste = !empty($reason_for_waste) ? "'".$reason_for_waste."'" : "NULL";
        $client_feedback = !empty($client_feedback) ? "'".$client_feedback."'" : "NULL";
        $note_issues = !empty($note_issues) ? "'".$note_issues."'" : "NULL";
        $revisions_errors = !empty($revisions_errors) ? "'".$revisions_errors."'" : "NULL";
        $error_category = !empty($error_category) ? "'".$error_category."'" : "NULL";
        $output_percentage = !empty($output_percentage) ? $output_percentage : "NULL";
        $timeliness_percentage = !empty($timeliness_percentage) ? $timeliness_percentage : "NULL";
        $accuracy_percentage = !empty($accuracy_percentage) ? $accuracy_percentage : "NULL";
        $teamwork_percentage = !empty($teamwork_percentage) ? $teamwork_percentage : "NULL";
        $material_efficiency_percentage = !empty($material_efficiency_percentage) ? $material_efficiency_percentage : "NULL";
        $overall_kpi_percentage = !empty($overall_kpi_percentage) ? $overall_kpi_percentage : "NULL";
        $client_satisfaction_score = !empty($client_satisfaction_score) ? $client_satisfaction_score : "NULL";
        $planned_material_quantity = !empty($planned_material_quantity) ? $planned_material_quantity : "NULL";
        $production_efficiency_percentage = !empty($production_efficiency_percentage) ? $production_efficiency_percentage : "NULL";
        $yield_percentage = !empty($yield_percentage) ? $yield_percentage : "NULL";
        $scrap_rate_percentage = !empty($scrap_rate_percentage) ? $scrap_rate_percentage : "NULL";
        $equipment_utilization_percentage = !empty($equipment_utilization_percentage) ? $equipment_utilization_percentage : "NULL";
        $energy_consumption = !empty($energy_consumption) ? $energy_consumption : "NULL";
        $safety_score_percentage = !empty($safety_score_percentage) ? $safety_score_percentage : "NULL";
        $inventory_turnover_rate = !empty($inventory_turnover_rate) ? $inventory_turnover_rate : "NULL";

        $sql = "INSERT INTO evaluations (
            material_type, project_name, client_name, role, team_member_name, task_description, 
            assigned_date, due_date, actual_completion_date, on_time, revisions_errors, 
            error_category, qc_passed, material_used, waste_quantity, cost_per_unit, 
            reason_for_waste, client_feedback, note_issues, task_type, output_percentage, 
            timeliness_percentage, accuracy_percentage, teamwork_percentage, 
            material_efficiency_percentage, overall_kpi_percentage, color_code, created_by,
            client_satisfaction_score, planned_material_quantity,
            production_efficiency_percentage, yield_percentage, scrap_rate_percentage,
            equipment_utilization_percentage, energy_consumption, safety_score_percentage,
            inventory_turnover_rate
        ) VALUES (
            '$material_type', '$project_name', '$client_name', '$role', '$team_member_name', '$task_description',
            '$assigned_date', '$due_date', $actual_completion_date, '$on_time', $revisions_errors,
            $error_category, '$qc_passed', '$material_used', $waste_quantity, $cost_per_unit, 
            $reason_for_waste, $client_feedback, $note_issues, '$task_type', $output_percentage, 
            $timeliness_percentage, $accuracy_percentage, $teamwork_percentage,
            $material_efficiency_percentage, $overall_kpi_percentage, '$color_code', '$created_by',
            $client_satisfaction_score, $planned_material_quantity,
            $production_efficiency_percentage, $yield_percentage, $scrap_rate_percentage,
            $equipment_utilization_percentage, $energy_consumption, $safety_score_percentage,
            $inventory_turnover_rate
        )";

        if($conn->query($sql)){
            $_SESSION['success'] = 'Evaluation added successfully';
        } else {
            $_SESSION['error'] = array('Database error: ' . $conn->error);
        }
    } else {
        $_SESSION['error'] = $errors;
    }
}

header('location: evaluation.php');
?>