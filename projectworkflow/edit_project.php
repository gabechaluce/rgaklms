<?php
include 'db_connect.php';

// Sanitize project ID from URL
$project_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$id = $project_id; // Set $id variable for manage_project.php compatibility

// Fetch project data with all new fields
$qry = $conn->query("SELECT * FROM project_list WHERE id = $project_id");

if ($qry && $qry->num_rows > 0) {
    $project = $qry->fetch_assoc();
    
    // Basic project information
    $name = $project['name'];

    $location = isset($project['location']) ? $project['location'] : '';

    $project_cost = isset($project['project_cost']) ? $project['project_cost'] : '';
    
    // Dates and status
    $start_date = $project['start_date'];
    $end_date = $project['end_date'];
    $status = $project['status'];
    $description = $project['description'];
    
    // Team assignments - handle both old and new field structures
    $manager_id = $project['manager_id'];
    $user_ids = isset($project['user_ids']) ? $project['user_ids'] : '';
    
    // New separated role fields (if they exist in database)
    $coordinator_ids = isset($project['coordinator_ids']) ? $project['coordinator_ids'] : '';
    $designer_ids = isset($project['designer_ids']) ? $project['designer_ids'] : '';
    $estimator_ids = isset($project['estimator_ids']) ? $project['estimator_ids'] : '';
    $inventory_ids = isset($project['inventory_ids']) ? $project['inventory_ids'] : '';
    
    // If new fields are empty but user_ids contains data, we might need to parse it
    // This is for backward compatibility with existing projects
    if (empty($coordinator_ids) && empty($designer_ids) && empty($estimator_ids) && empty($inventory_ids) && !empty($user_ids)) {
        // For backward compatibility, you might want to keep user_ids as is
        // or implement logic to separate roles based on user types
        
        // Example logic to separate roles based on user types (optional):
        if (!empty($user_ids)) {
            $user_id_array = explode(',', $user_ids);
            $coordinators = [];
            $designers = [];
            $estimators = [];
            $inventory = [];
            
            foreach ($user_id_array as $uid) {
                $uid = trim($uid);
                if (!empty($uid)) {
                    $user_type_qry = $conn->query("SELECT type FROM users WHERE id = $uid");
                    if ($user_type_qry && $user_type_qry->num_rows > 0) {
                        $user_type_data = $user_type_qry->fetch_assoc();
                        $user_type = $user_type_data['type'];
                        
                        switch ($user_type) {
                            case 2: // Coordinator
                                $coordinators[] = $uid;
                                break;
                            case 4: // Designer
                                $designers[] = $uid;
                                break;
                            case 6: // Estimator
                                $estimators[] = $uid;
                                break;
                            case 5: // Inventory
                                $inventory[] = $uid;
                                break;
                            case 3: // Team members - keep in user_ids
                            default:
                                // Keep in user_ids for team members
                                break;
                        }
                    }
                }
            }
            
            // Set the separated role arrays
            $coordinator_ids = !empty($coordinators) ? implode(',', $coordinators) : '';
            $designer_ids = !empty($designers) ? implode(',', $designers) : '';
            $estimator_ids = !empty($estimators) ? implode(',', $estimators) : '';
            $inventory_ids = !empty($inventory) ? implode(',', $inventory) : '';
            
            // Filter user_ids to only contain team members (type 3)
            $team_members = [];
            foreach ($user_id_array as $uid) {
                $uid = trim($uid);
                if (!empty($uid)) {
                    $user_type_qry = $conn->query("SELECT type FROM users WHERE id = $uid");
                    if ($user_type_qry && $user_type_qry->num_rows > 0) {
                        $user_type_data = $user_type_qry->fetch_assoc();
                        if ($user_type_data['type'] == 3) {
                            $team_members[] = $uid;
                        }
                    }
                }
            }
            $user_ids = !empty($team_members) ? implode(',', $team_members) : '';
        }
    }
    
} else {
    echo "<div class='alert alert-danger'>Project not found.</div>";
    exit;
}

// Fetch uploaded files for this project
$uploaded_files = [];
$file_qry = $conn->query("SELECT * FROM uploaded_files WHERE project_id = $project_id AND (is_deleted = 0 OR is_deleted IS NULL)");
if ($file_qry && $file_qry->num_rows > 0) {
    while ($file = $file_qry->fetch_assoc()) {
        $uploaded_files[] = $file;
    }
}

// Set page title for breadcrumb/header
$page_title = "Edit Project: " . htmlspecialchars($name);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title; ?></title>
    <style>
        .page-header {
            background: #f8f9fa;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
            border-left: 4px solid #007bff;
        }
        .page-header h4 {
            margin: 0;
            color: #495057;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0.5rem 0 0 0;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: "›";
            color: #6c757d;
        }
        .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Page Header -->
    <div class="page-header">
        <h4>Edit Project</h4>
        
    </div>

    <?php 
    // Include the updated project management form
    include 'manage_project.php'; 
    ?>

    <script>
        $(document).ready(function() {
            // Override the form submission to redirect back to project list after save
            $('#manage-project').off('submit').on('submit', function(e) {
                e.preventDefault();
                start_load();
                let formData = new FormData($(this)[0]);
                $.ajax({
                    url: 'ajax.php?action=save_project',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    success: function(resp) {
                        if (resp == 1) {
                            alert_toast('Project successfully updated', "success");
                            setTimeout(function() {
                                location.href = 'index.php?page=project_list';
                            }, 1500);
                        } else {
                            alert_toast('Error: ' + resp, "danger");
                        }
                        end_load();
                    },
                    error: function(xhr, status, error) {
                        alert_toast('An error occurred: ' + error, "danger");
                        console.error(xhr.responseText);
                        end_load();
                    }
                });
            });

            // Update page title if project name changes
            $('input[name="name"]').on('change', function() {
                var newName = $(this).val();
                if (newName.trim() !== '') {
                    document.title = 'Edit Project: ' + newName;
                    $('.page-header h4').text('Edit Project: ' + newName);
                }
            });

            // Add confirmation before leaving if form has changes
            var formChanged = false;
            $('#manage-project input, #manage-project select, #manage-project textarea').on('change', function() {
                formChanged = true;
            });

            $(window).on('beforeunload', function(e) {
                if (formChanged) {
                    var confirmationMessage = 'You have unsaved changes. Are you sure you want to leave?';
                    e.returnValue = confirmationMessage;
                    return confirmationMessage;
                }
            });

            // Remove the beforeunload event when form is submitted
            $('#manage-project').on('submit', function() {
                $(window).off('beforeunload');
            });

            // Also remove beforeunload when cancel is clicked
            $('button[onclick*="project_list"]').on('click', function() {
                if (formChanged) {
                    var confirm = window.confirm('You have unsaved changes. Are you sure you want to leave?');
                    if (!confirm) {
                        return false;
                    }
                }
                $(window).off('beforeunload');
            });
        });
    </script>
</body>
</html>