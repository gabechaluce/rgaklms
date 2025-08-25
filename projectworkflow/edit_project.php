<?php
include 'db_connect.php';

// Sanitize project ID from URL
$project_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch project data
$qry = $conn->query("SELECT * FROM project_list WHERE id = $project_id");

if ($qry && $qry->num_rows > 0) {
    $project = $qry->fetch_assoc();
    $name = $project['name'];
    $start_date = $project['start_date'];
    $end_date = $project['end_date'];
    $manager_id = $project['manager_id'];
    $user_ids = $project['user_ids'];
    $status = $project['status'];
    $description = $project['description'];
} else {
    echo "Project not found.";
    exit;
}

// Fetch uploaded files for this project
$uploaded_files = [];
$file_qry = $conn->query("SELECT * FROM uploaded_files WHERE project_id = $project_id AND is_deleted = 0");
if ($file_qry && $file_qry->num_rows > 0) {
    while ($file = $file_qry->fetch_assoc()) {
        $uploaded_files[] = $file;
    }
}
?>

<!-- Include your project management form -->
<?php include 'manage_project.php'; ?>
