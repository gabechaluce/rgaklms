<?php include('db_connect.php'); ?>
<?php

// Check user role for visibility and access
$twhere = "";
if ($_SESSION['login_type'] != 1) {  // If the user is not admin
    $twhere = "";
}

// Get all projects (filtered by assigned user)
$where = "";
$user_id = $_SESSION['login_id'];

if ($_SESSION['login_type'] == 1) { // Admin - no filter
    $where = "";
} elseif ($_SESSION['login_type'] == 2) { // Project Coordinator
    // FIXED: Use FIND_IN_SET for manager_id (comma-separated values)
    $where = " WHERE FIND_IN_SET('$user_id', manager_id) > 0 OR FIND_IN_SET('$user_id', user_ids) > 0 ";
} elseif ($_SESSION['login_type'] == 14) { // Project Manager
    // FIXED: Use FIND_IN_SET for manager_id (comma-separated values)
    $where = " WHERE FIND_IN_SET('$user_id', manager_id) > 0 OR FIND_IN_SET('$user_id', user_ids) > 0 ";
} elseif (in_array($_SESSION['login_type'], [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13])) { // Team Members
    $where = " WHERE FIND_IN_SET('$user_id', user_ids) > 0 ";
}

?>
<body>
<!-- Info boxes -->
<div class="col-12">
    <div class="card">
        <div class="card-body">
        Welcome <span style="font-weight: bold; font-style: italic;"><?php echo $_SESSION['login_name']; ?></span>!
        </div>
        <?php
$user_id = $_SESSION['login_id'];
$notifications = [];

// Notification system for all users except admin
if ($_SESSION['login_type'] != 1) {  
    // Check for new project assignments (immediate notification)
    if (in_array($_SESSION['login_type'], [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13])) { // All employee types 
        $new_project_query = $conn->query("SELECT COUNT(*) as count FROM project_list 
            WHERE FIND_IN_SET('$user_id', user_ids) > 0 AND notified_users NOT LIKE '%,$user_id,%' 
            AND TIMESTAMPDIFF(HOUR, date_created, NOW()) <= 24");
    } else if (in_array($_SESSION['login_type'], [2, 14])) { // Project Manager (14) or Project Coordinator (2)
        $new_project_query = $conn->query("SELECT COUNT(*) as count FROM project_list 
            WHERE manager_id = '$user_id' AND notified_manager = 0 
            AND TIMESTAMPDIFF(HOUR, date_created, NOW()) <= 24");
    } else {
        // Default case as fallback
        $new_project_query = $conn->query("SELECT COUNT(*) as count FROM project_list 
            WHERE FIND_IN_SET('$user_id', user_ids) > 0 AND notified_users NOT LIKE '%,$user_id,%' 
            AND TIMESTAMPDIFF(HOUR, date_created, NOW()) <= 24");
    }
    
    $new_project = $new_project_query->fetch_assoc();
    
    if ($new_project['count'] > 0) {
        $notifications[] = "<div class='alert alert-success'>
            <a href='index.php?page=project_list&filter=new' class='text-dark font-weight-bold'>
                You have {$new_project['count']} new assigned project(s). Click here to view.
            </a>
        </div>";
        
        // Mark notifications as seen after displaying
        if (in_array($_SESSION['login_type'], [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13])) { // All employee types
            $conn->query("UPDATE project_list 
                SET notified_users = CONCAT(IFNULL(notified_users,''),',{$user_id},') 
                WHERE FIND_IN_SET('$user_id', user_ids) > 0 
                AND notified_users NOT LIKE '%,$user_id,%'");
        } else if (in_array($_SESSION['login_type'], [2, 14])) { // Project Manager or Project Coordinator
            $conn->query("UPDATE project_list 
                SET notified_manager = 1 
                WHERE manager_id = '$user_id' AND notified_manager = 0");
        } else {
            // Default case
            $conn->query("UPDATE project_list 
                SET notified_users = CONCAT(IFNULL(notified_users,''),',{$user_id},') 
                WHERE FIND_IN_SET('$user_id', user_ids) > 0 
                AND notified_users NOT LIKE '%,$user_id,%'");
        }
    }
    
    // Check for projects assigned more than 24 hours ago (reminder notification)
    if (in_array($_SESSION['login_type'], [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13])) { // All employee types
        $reminder_query = $conn->query("SELECT COUNT(*) as count FROM project_list 
            WHERE FIND_IN_SET('$user_id', user_ids) > 0 
            AND TIMESTAMPDIFF(HOUR, date_created, NOW()) > 24
            AND reminder_sent_users NOT LIKE '%,$user_id,%'
            AND status NOT IN (3, 5)"); // Not On-Hold or Done
    } else if (in_array($_SESSION['login_type'], [2, 14])) { // Project Manager or Project Coordinator
        $reminder_query = $conn->query("SELECT COUNT(*) as count FROM project_list 
            WHERE manager_id = '$user_id' 
            AND TIMESTAMPDIFF(HOUR, date_created, NOW()) > 24
            AND reminder_sent_manager = 0
            AND status NOT IN (3, 5)"); // Not On-Hold or Done
    } else {
        // Default case
        $reminder_query = $conn->query("SELECT COUNT(*) as count FROM project_list 
            WHERE FIND_IN_SET('$user_id', user_ids) > 0 
            AND TIMESTAMPDIFF(HOUR, date_created, NOW()) > 24
            AND reminder_sent_users NOT LIKE '%,$user_id,%'
            AND status NOT IN (3, 5)"); // Not On-Hold or Done
    }
    
    $reminder = $reminder_query->fetch_assoc();
    
    if ($reminder['count'] > 0) {
        $notifications[] = "<div class='alert alert-warning'>
            <a href='index.php?page=project_list&filter=pending' class='text-dark font-weight-bold'>
                You have {$reminder['count']} project(s) assigned more than 24 hours ago that need attention. Click here to view.
            </a>
        </div>";
        
        // Mark reminders as sent after displaying
        if (in_array($_SESSION['login_type'], [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13])) { // All employee types
            $conn->query("UPDATE project_list 
                SET reminder_sent_users = CONCAT(IFNULL(reminder_sent_users,''),',{$user_id},') 
                WHERE FIND_IN_SET('$user_id', user_ids) > 0 
                AND reminder_sent_users NOT LIKE '%,$user_id,%'");
        } else if (in_array($_SESSION['login_type'], [2, 14])) { // Project Manager or Project Coordinator
            $conn->query("UPDATE project_list 
                SET reminder_sent_manager = 1 
                WHERE manager_id = '$user_id' AND reminder_sent_manager = 0");
        } else {
            // Default case
            $conn->query("UPDATE project_list 
                SET reminder_sent_users = CONCAT(IFNULL(reminder_sent_users,''),',{$user_id},') 
                WHERE FIND_IN_SET('$user_id', user_ids) > 0 
                AND reminder_sent_users NOT LIKE '%,$user_id,%'");
        }
    }
    
    // Task notifications for all users (new feature)
    if ($conn->query("SHOW COLUMNS FROM task_list LIKE 'notified_users'")->num_rows > 0) {
        $task_query = $conn->query("SELECT COUNT(*) as count FROM task_list t
            INNER JOIN project_list p ON t.project_id = p.id
            WHERE (FIND_IN_SET('$user_id', t.user_ids) > 0 OR t.employee_id = '$user_id')  
            AND t.notified_users NOT LIKE '%,$user_id,%'
            AND TIMESTAMPDIFF(HOUR, t.date_created, NOW()) <= 24");
        
        $task_count = $task_query->fetch_assoc();
        
        if ($task_count['count'] > 0) {
            $notifications[] = "<div class='alert alert-info'>
                <a href='index.php?page=task_list&filter=new' class='text-dark font-weight-bold'>
                    You have {$task_count['count']} new assigned task(s). Click here to view.
                </a>
            </div>";
            
            // Mark task notifications as seen after displaying
            $conn->query("UPDATE task_list 
                SET notified_users = CONCAT(IFNULL(notified_users,''),',{$user_id},') 
                WHERE (FIND_IN_SET('$user_id', user_ids) > 0 OR employee_id = '$user_id')
                AND notified_users NOT LIKE '%,$user_id,%'");
        }
    }
}

// Display all collected notifications
foreach ($notifications as $notification) {
    echo $notification;
}
        ?>

        <?php if (in_array($_SESSION['login_type'], [1, 2, 14])): // Only show inquiry notifications to Admin, Project Coordinator and Project Manager ?>
            <?php
            // Get the count of inquiries that are not marked as "Done" within 24 hours
            $inquiry_count = $conn->query("SELECT COUNT(*) as count FROM inquiry_list WHERE inquiry_status != 2 AND TIMESTAMPDIFF(HOUR, date_created, NOW()) > 24")->fetch_assoc()['count'];
            ?>
            <?php if ($inquiry_count > 0): ?>
            <div class="alert alert-warning">
                <a href="index.php?page=inquiry_list&filter=pending" class="text-dark font-weight-bold">
                    You have <?php echo $inquiry_count; ?> pending inquiry older than 24 hours. Click here to review.
                </a>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<hr>
<div class="row">
    <!-- Total Projects -->
    <div class="col-lg-3 col-6">
        <a href="./index.php?page=project_list">
        <div class="small-box bg-info">
            <div class="inner">
            <h3><?php echo $conn->query("SELECT * FROM project_list")->num_rows; ?></h3>
            <p>Total Projects</p>
            </div>
            <div class="icon">
                <i class="fa fa-folder"></i>
            </div>
        </div>
        </a>
    </div>

<!-- Projects Done -->  
    <div class="col-lg-3 col-6">
    <a href="./index.php?page=project_completed">
        <div class="small-box bg-success"> <!-- Changed from warning to success to match status -->
            <div class="inner">
                <h3><?php echo $conn->query("SELECT * FROM project_list WHERE status = 5")->num_rows; ?></h3>
                <p>Completed Projects</p>
            </div>
            <div class="icon">
                <i class="fa fa-check"></i>
            </div>
        </div>
    </a>
    </div>

    <!-- Projects Not Done (FIXED) -->
    <div class="col-lg-3 col-6">
        <a href="./index.php?page=ongoing_project">
        <div class="small-box bg-warning">
            <div class="inner">
            <h3><?php echo $conn->query("SELECT * FROM project_list WHERE status IN (0, 1, 2)")->num_rows; ?></h3>
                <p>Ongoing Project</p>
            </div>
            <div class="icon">
                <i class="fa fa-tasks"></i>
            </div>
        </div>
        </a>
    </div>
</div>
<!-- Project List for All Users -->
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-success">
            <div class="card-header">
                <b>Project Progress</b>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table m-0 table-hover">
                        <colgroup>
                            <col width="5%">
                            <col width="30%">
                            <col width="35%">
                            <col width="15%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <th>#</th>
                            <th>Project</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            $stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
                            // Fetch projects assigned to the logged-in user
                            // Modified query to order by new projects first (created in last 24 hours), then by name
                            $qry = $conn->query("SELECT *, 
                                (TIMESTAMPDIFF(HOUR, date_created, NOW()) <= 24) as is_new 
                                FROM project_list $where 
                                ORDER BY is_new DESC, name ASC");
                            
                            while ($row = $qry->fetch_assoc()):
                                // Get task counts
                                $tprog = $conn->query("SELECT * FROM task_list WHERE project_id = {$row['id']}")->num_rows;
                                $cprog = $conn->query("SELECT * FROM task_list WHERE project_id = {$row['id']} AND status = 3")->num_rows;
                                
                                // Calculate progress based on project status and task completion
                                $prog = 0;
                                
                                if ($row['status'] == 5) { // Project is Done
                                    $prog = 100;
                                } elseif ($tprog > 0) {
                                    // Calculate base progress from completed tasks (max 80% for non-done projects)
                                    $task_progress_percentage = ($cprog / $tprog) * 80; // Tasks contribute max 80%
                                    
                                    // Add status-based progress bonus
                                    $status_bonus = 0;
                                    switch ($row['status']) {
                                        case 0: // Pending
                                            $status_bonus = 0;
                                            break;
                                        case 1: // Started
                                            $status_bonus = 5;
                                            break;
                                        case 2: // On-Progress
                                            $status_bonus = 10;
                                            break;
                                        case 3: // On-Hold
                                            $status_bonus = 0; // No bonus for on-hold
                                            break;
                                        case 4: // Over Due
                                            $status_bonus = 0; // No bonus for overdue
                                            break;
                                    }
                                    
                                    $prog = min(95, $task_progress_percentage + $status_bonus); // Cap at 95% for non-done projects
                                } else {
                                    // No tasks yet, progress based only on status
                                    switch ($row['status']) {
                                        case 0: // Pending
                                            $prog = 0;
                                            break;
                                        case 1: // Started
                                            $prog = 10;
                                            break;
                                        case 2: // On-Progress
                                            $prog = 20;
                                            break;
                                        case 3: // On-Hold
                                            $prog = 5;
                                            break;
                                        case 4: // Over Due
                                            $prog = 5;
                                            break;
                                    }
                                }
                                
                                $prog = $prog > 0 ? number_format($prog, 1) : 0;
                                $prod = $conn->query("SELECT * FROM user_productivity WHERE project_id = {$row['id']}")->num_rows;

                                // Update project status based on conditions
                                if ($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])):
                                    if ($prod > 0 || $cprog > 0)
                                        $row['status'] = 2;
                                    else
                                        $row['status'] = 1;
                                elseif ($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])):
                                    $row['status'] = 4;
                                endif;

                                // Check if the project is new (created in the last 24 hours)
                                $is_new = (strtotime($row['date_created']) >= strtotime('-1 day')) ? 'highlight' : '';
                            ?>
                            <tr class="<?php echo $is_new; ?>">
                                <td><?php echo $i++ ?></td>
                                <td>
                                <a style="font-weight: bold;"><?php echo ucwords($row['name']); ?></a>
                                    <br>
                                    <small style="color: red;">Due: <?php echo date("Y-m-d", strtotime($row['end_date'])); ?></small>
                                </td>
                                <td class="project_progress">
                                    <div class="progress progress-sm">
                                        <?php
                                        // Different progress bar colors based on status and progress
                                        $progress_color = 'bg-info'; // default
                                        if ($row['status'] == 5) { // Done
                                            $progress_color = 'bg-success';
                                        } elseif ($row['status'] == 4) { // Over Due
                                            $progress_color = 'bg-danger';
                                        } elseif ($row['status'] == 3) { // On-Hold
                                            $progress_color = 'bg-warning';
                                        } elseif ($prog >= 80) {
                                            $progress_color = 'bg-primary';
                                        } elseif ($prog >= 50) {
                                            $progress_color = 'bg-info';
                                        }
                                        ?>
                                        <div class="progress-bar <?php echo $progress_color; ?>" role="progressbar" 
                                             aria-valuenow="<?php echo $prog; ?>" aria-valuemin="0" aria-valuemax="100" 
                                             style="width: <?php echo $prog ?>%"></div>
                                    </div>
                                    <small>
                                        <?php echo $prog ?>% Complete 
                                        <?php if ($tprog > 0): ?>
                                            (<?php echo $cprog; ?>/<?php echo $tprog; ?> tasks done)
                                        <?php endif; ?>
                                    </small>
                                </td>
                                <td class="project-state">
                                    <?php
                                    if ($stat[$row['status']] == 'Pending') {
                                        echo "<span class='badge badge-secondary'>{$stat[$row['status']]}</span>";
                                    } elseif ($stat[$row['status']] == 'Started') {
                                        echo "<span class='badge badge-primary'>{$stat[$row['status']]}</span>";
                                    } elseif ($stat[$row['status']] == 'On-Progress') {
                                        echo "<span class='badge badge-info'>{$stat[$row['status']]}</span>";
                                    } elseif ($stat[$row['status']] == 'On-Hold') {
                                        echo "<span class='badge badge-warning'>{$stat[$row['status']]}</span>";
                                    } elseif ($stat[$row['status']] == 'Over Due') {
                                        echo "<span class='badge badge-danger'>{$stat[$row['status']]}</span>";
                                    } elseif ($stat[$row['status']] == 'Done') {
                                        echo "<span class='badge badge-success'>{$stat[$row['status']]}</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="./index.php?page=project_details&id=<?php echo $row['id'] ?>">
                                        <i class="fas fa-folder"></i> View
                                    </a>
                                    <?php if ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2): ?>
                                    <a class="btn btn-warning btn-sm" href="./index.php?page=edit_project&id=<?php echo $row['id'] ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body, .wrapper, .content-wrapper {
    background-color:#f4f1ed !important;
}

.highlight {
    background-color: #fffb8f; /* Light yellow background for new projects */
    font-weight: bold;
}
</style>
</body>