<?php include 'db_connect.php'; ?>
<body>
<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <?php if ($_SESSION['login_type'] != 3): // Only admins and managers can add projects ?>
                <div class="card-tools">
                    <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_project">
                        <i class="fa fa-plus"></i> Add New project
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-condensed m-0" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="35%">
                        <col width="15%">
                        <col width="15%">
                        <col width="20%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Project</th>
                            <th>Date Started</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $stat = array("Pending", "Started", "On-Progress", "On-Hold", "Over Due", "Done");
                        $where = "";
                        
    // Corrected project visibility logic
    if ($_SESSION['login_type'] == 1) {
        // Admin sees all projects
        $where = "";
    } elseif ($_SESSION['login_type'] == 2) {
        // Coordinator sees projects where they're in coordinator_ids
        $user_id = $conn->real_escape_string($_SESSION['login_id']);
        $where = " WHERE (
            FIND_IN_SET('$user_id', coordinator_ids) > 0 OR 
            coordinator_ids LIKE '$user_id,%' OR 
            coordinator_ids LIKE '%,$user_id' OR 
            coordinator_ids = '$user_id' OR
            FIND_IN_SET('$user_id', user_ids) > 0 OR 
            user_ids LIKE '$user_id,%' OR 
            user_ids LIKE '%,$user_id' OR 
            user_ids = '$user_id'
        )";
    } elseif ($_SESSION['login_type'] == 7) {
        // Manager sees projects where they're in manager_id or user_ids
        $user_id = $conn->real_escape_string($_SESSION['login_id']);
        $where = " WHERE (
            FIND_IN_SET('$user_id', manager_id) > 0 OR 
            manager_id LIKE '$user_id,%' OR 
            manager_id LIKE '%,$user_id' OR 
            manager_id = '$user_id' OR 
            FIND_IN_SET('$user_id', user_ids) > 0 OR 
            user_ids LIKE '$user_id,%' OR 
            user_ids LIKE '%,$user_id' OR 
            user_ids = '$user_id'
        )";
    } elseif ($_SESSION['login_type'] == 3) {
        // Designer sees projects where they're in designer_ids
        $user_id = $conn->real_escape_string($_SESSION['login_id']);
        $where = " WHERE (
            FIND_IN_SET('$user_id', designer_ids) > 0 OR 
            designer_ids LIKE '$user_id,%' OR 
            designer_ids LIKE '%,$user_id' OR 
            designer_ids = '$user_id' OR
            FIND_IN_SET('$user_id', user_ids) > 0 OR 
            user_ids LIKE '$user_id,%' OR 
            user_ids LIKE '%,$user_id' OR 
            user_ids = '$user_id'
        )";
    } elseif ($_SESSION['login_type'] == 4) {
        // Inventory Coordinator sees projects where they're in inventory_ids
        $user_id = $conn->real_escape_string($_SESSION['login_id']);
        $where = " WHERE (
            FIND_IN_SET('$user_id', inventory_ids) > 0 OR 
            inventory_ids LIKE '$user_id,%' OR 
            inventory_ids LIKE '%,$user_id' OR 
            inventory_ids = '$user_id' OR
            FIND_IN_SET('$user_id', user_ids) > 0 OR 
            user_ids LIKE '$user_id,%' OR 
            user_ids LIKE '%,$user_id' OR 
            user_ids = '$user_id'
        )";
    } elseif ($_SESSION['login_type'] == 5) {
        // Estimator sees projects where they're in estimator_ids
        $user_id = $conn->real_escape_string($_SESSION['login_id']);
        $where = " WHERE (
            FIND_IN_SET('$user_id', estimator_ids) > 0 OR 
            estimator_ids LIKE '$user_id,%' OR 
            estimator_ids LIKE '%,$user_id' OR 
            estimator_ids = '$user_id' OR
            FIND_IN_SET('$user_id', user_ids) > 0 OR 
            user_ids LIKE '$user_id,%' OR 
            user_ids LIKE '%,$user_id' OR 
            user_ids = '$user_id'
        )";
    } elseif ($_SESSION['login_type'] == 9) {
        // Sales sees projects where they're in user_ids
        $user_id = $conn->real_escape_string($_SESSION['login_id']);
        $where = " WHERE (
            FIND_IN_SET('$user_id', user_ids) > 0 OR 
            user_ids LIKE '$user_id,%' OR 
            user_ids LIKE '%,$user_id' OR 
            user_ids = '$user_id'
        )";
    }
                        
                        $qry = $conn->query("SELECT * FROM project_list $where ORDER BY date_created DESC, name ASC");

                        // Add debug output (remove in production)
                        echo "<!-- SQL Query: SELECT * FROM project_list $where ORDER BY date_created DESC, name ASC -->";
                        echo "<!-- User ID: {$_SESSION['login_id']}, User Type: {$_SESSION['login_type']} -->";

                        while ($row = $qry->fetch_assoc()):
                            // Improved description cleaning
                            $desc = html_entity_decode($row['description']);
                            $desc = strip_tags($desc);
                            $desc = str_replace(["&nbsp;", "\r", "\n"], " ", $desc);
                            $desc = preg_replace('/\s+/', ' ', $desc);
                            $desc = trim($desc);
                            
                            // Progress calculation
                            $tprog = $conn->query("SELECT * FROM task_list WHERE project_id = {$row['id']}")->num_rows;
                            $cprog = $conn->query("SELECT * FROM task_list WHERE project_id = {$row['id']} AND status = 3")->num_rows;
                            $prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
                            $prog = $prog > 0 ? number_format($prog, 2) : $prog;
                            $prod = $conn->query("SELECT * FROM user_productivity WHERE project_id = {$row['id']}")->num_rows;

                            // Auto-update project status based on dates and progress
                            if ($row['status'] == 0 && strtotime(date('Y-m-d')) >= strtotime($row['start_date'])):
                                $row['status'] = ($prod > 0 || $cprog > 0) ? 2 : 1;
                            elseif ($row['status'] == 0 && strtotime(date('Y-m-d')) > strtotime($row['end_date'])):
                                $row['status'] = 4;
                            endif;
                        ?>
                            <tr>
                                <th class="text-center"><?php echo $i++ ?></th>
                                <td>
                                    <p><b><?php echo ucwords($row['name']) ?></b></p>
                                    <p class="truncate" title="<?php echo htmlspecialchars($desc) ?>"><?php echo $desc ?></p>
                                </td>
                                <td><b><?php echo date("M d, Y", strtotime($row['start_date'])) ?></b></td>
                                <td><b><?php echo date("M d, Y", strtotime($row['end_date'])) ?></b></td>
                                <td class="text-center">
                                    <?php
                                    // Status badge with improved conditional
                                    $status_class = [
                                        0 => 'secondary', // Pending
                                        1 => 'primary',   // Started
                                        2 => 'info',      // On-Progress
                                        3 => 'warning',   // On-Hold
                                        4 => 'danger',    // Over Due
                                        5 => 'success'    // Done
                                    ];
                                    echo "<span class='badge badge-{$status_class[$row['status']]}'>{$stat[$row['status']]}</span>";
                                    ?>
                                </td>
    <td class="text-center">
        <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
            Action
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item project_details" href="./index.php?page=project_details&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>">View</a>
            <div class="dropdown-divider"></div>
            <?php if (in_array($_SESSION['login_type'], [1, 2, 7, 3])): // Only Admin, Coordinator, and Manager ?>
                <a class="dropdown-item" href="./index.php?page=edit_project&id=<?php echo $row['id'] ?>">Edit</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item delete_project" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
            <?php endif; ?>
        </div>
    </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    table p {
        margin: unset !important;
    }

    table td {
        vertical-align: middle !important;
    }
    
    table p.truncate {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
    }
    
    /* Mobile responsiveness improvements */
    @media (max-width: 768px) {
        table p.truncate {
            max-width: 200px; /* Shorter on mobile */
        }
        
        .table td, .table th {
            padding: 0.5rem 0.25rem; /* Reduced padding on mobile */
            font-size: 0.875rem; /* Slightly smaller text */
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .badge {
            font-size: 0.65em;
        }
    }
    
    /* Improved badge colors */
    .badge-secondary { background-color: #6c757d; }
    .badge-primary { background-color: #007bff; }
    .badge-info { background-color: #17a2b8; }
    .badge-warning { background-color: #ffc107; color: #212529; }
    .badge-danger { background-color: #dc3545; }
    .badge-success { background-color: #28a745; }

    body, .wrapper, .content-wrapper {
        background-color: #f4f1ed !important;
    }
</style>

<script>
    $(document).ready(function () {
        $('#list').dataTable({
            "responsive": true,
            "autoWidth": false,
            "scrollX": true, // Enable horizontal scroll in DataTable
            "language": {
                "emptyTable": "No projects found",
                "info": "Showing _START_ to _END_ of _TOTAL_ projects",
                "infoEmpty": "Showing 0 to 0 of 0 projects",
                "infoFiltered": "(filtered from _MAX_ total projects)",
                "search": "Search projects:"
            }
        });

        $('.delete_project').click(function () {
            _conf("Are you sure to delete this project?", "delete_project", [$(this).attr('data-id')]);
        });
        
        // Add tooltip for truncated text
        $('.truncate').tooltip();
    });

    function delete_project($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_project',
            method: 'POST',
            data: { id: $id },
            success: function (resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                } else {
                    alert_toast("Error: " + resp, 'error');
                    end_load();
                }
            },
            error: function(xhr) {
                alert_toast("Request failed: " + xhr.statusText, 'error');
                end_load();
            }
        });
    }
</script>
</body>