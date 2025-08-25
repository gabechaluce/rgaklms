<?php
ob_start();
date_default_timezone_set("Asia/Manila");

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();

if ($action == 'login') {
    $login = $crud->login();
    if ($login)
        echo $login; 
}

if ($action == 'login2') {
    $login = $crud->login2();
    if ($login)
        echo $login;
}

if ($action == 'logout') {
    $logout = $crud->logout();
    if ($logout)
        echo $logout;
}

if ($action == 'logout2') {
    $logout = $crud->logout2();
    if ($logout)
        echo $logout;
}

if ($action == 'signup') {
    $save = $crud->signup();
    if ($save)
        echo $save;
}

if ($action == 'save_user') {
    $save = $crud->save_user();
    if ($save)
        echo $save;
}

if ($action == 'update_user') {
    $save = $crud->update_user();
    if ($save)
        echo $save;
}

if ($action == 'delete_user') {
    $save = $crud->delete_user();
    if ($save)
        echo $save;
}

if ($action == 'save_inquiry') {
    $save = $crud->save_inquiry();
    if ($save)
        echo $save;
}
if ($action == 'save_track') {
    $save = $crud->save_track();
    if ($save)
        echo $save;
}
if ($action == 'delete_track') {
    $save = $crud->delete_track();
    if ($save)
        echo $save;
}

if ($action == 'delete_inquiry') {
    $save = $crud->delete_inquiry();
    if ($save)
        echo $save;
}

if ($action == 'save_project') {
    $save = $crud->save_project();
    if ($save)
        echo $save;
}

if ($action == 'update_project_status') {
    $save = $crud->update_project_status();  // Call the update_project function
    if ($save)
        echo $save; // Return the result of the update
}

if ($action == 'delete_project') {
    $save = $crud->delete_project();
    if ($save)
        echo $save;
}

if ($action == 'save_task') {
    $save = $crud->save_task();
    if ($save)
        echo $save;
}

if ($action == 'delete_task') {
    $save = $crud->delete_task();
    if ($save)
        echo $save;
}

if ($action == 'save_progress') {
    $save = $crud->save_progress();
    if ($save)
        echo $save;
}

if ($action == 'delete_progress') {
    $save = $crud->delete_progress();
    if ($save)
        echo $save;
}

if ($action == 'get_report') {
    $get = $crud->get_report();
    if ($get)
        echo $get;
}
if (isset($_GET['action']) && $_GET['action'] == 'upload_file') {
    // Define allowed file types
    $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowedDocTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'dwg'];
    
    if (isset($_FILES['file']['name'])) {
        // Get project_id from POST (or default to 0 for new projects)
        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0; // Ensure integer
        
        // Determine file type based on the request
        $fileType = isset($_POST['type']) ? $_POST['type'] : 'image';
        $originalName = $_FILES['file']['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Validate file type
        if ($fileType == 'image' && !in_array($ext, $allowedImageTypes)) {
            echo json_encode([
                'success' => false,
                'message' => 'Only JPG, JPEG, PNG, GIF, and WEBP files are allowed for images.'
            ]);
            exit;
        } elseif ($fileType == 'document' && !in_array($ext, $allowedDocTypes)) {
            echo json_encode([
                'success' => false,
                'message' => 'Only PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, and DWG files are allowed for documents.'
            ]);
            exit;
        }
        
        // Set appropriate upload directory
        $uploadDir = $fileType == 'image' ? 'uploads/images/' : 'uploads/documents/';
        
        // Create directories if they don't exist
        if (!is_dir('uploads/')) {
            mkdir('uploads/', 0755, true);
        }
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename to prevent overwriting
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._]/', '', $originalName);
        $path = $uploadDir . $filename;
        
        // Check file size (limit to 15MB for documents, 5MB for images)
        $maxSize = $fileType == 'image' ? 5242880 : 15728640; // 5MB or 15MB in bytes
        if ($_FILES['file']['size'] > $maxSize) {
            $maxSizeMB = $maxSize / 1048576; // Convert to MB
            echo json_encode([
                'success' => false,
                'message' => "File size exceeds the {$maxSizeMB}MB limit."
            ]);
            exit;
        }
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
            // Construct the URL to the uploaded file
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $scriptPath = dirname($_SERVER['PHP_SELF']);
            $basePath = rtrim($protocol . $host . $scriptPath, '/') . '/';
            $url = $basePath . $path;
            
            // Log the upload for security and tracking purposes
            $logMsg = date('Y-m-d H:i:s') . " - Uploaded {$fileType}: {$originalName} ({$_FILES['file']['size']} bytes) to {$path} by user " . (isset($_SESSION['login_id']) ? $_SESSION['login_id'] : 'unknown') . "\n";
            error_log($logMsg, 3, "uploads/upload_log.txt");
            
            // Add entry to database for document tracking
            if (isset($_SESSION['login_id'])) {
                include 'db_connect.php';
                $user_id = $_SESSION['login_id'];
                $stmt = $conn->prepare("INSERT INTO uploaded_files (filename, original_name, file_type, file_path, url, uploaded_by, project_id, upload_date) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("sssssii", $filename, $originalName, $ext, $path, $url, $user_id, $project_id);
                $stmt->execute();
                $stmt->close();
            }
            
// Inside the upload_file action after inserting into database
$file_id = $conn->insert_id; // Add this line
echo json_encode([
    'success' => true,
    'id' => $file_id, // Add this line
    'url' => $url,
    'filename' => $filename,
    'originalName' => $originalName,
    'fileType' => $fileType,
    'extension' => $ext
]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'File upload failed. Check directory permissions.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No file uploaded.'
        ]);
    }
    exit;
}

// Legacy image upload handler for backward compatibility
if (isset($_GET['action']) && $_GET['action'] == 'upload_image') {
    if (isset($_FILES['file']['name'])) {
        $uploadDir = 'uploads/images/';
        // Create uploads directory if it doesn't exist
        if (!is_dir('uploads/')) {
            mkdir('uploads/', 0755, true);
        }
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Sanitize filename and generate unique name
        $filename = time() . '_' . basename($_FILES['file']['name']);
        $path = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
            // Construct the full URL to the uploaded image
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $scriptPath = dirname($_SERVER['PHP_SELF']);
            // Adjust path to avoid double slashes
            $basePath = rtrim($protocol . $host . $scriptPath, '/') . '/';
            $url = $basePath . $path;
            echo $url;
        } else {
            http_response_code(500);
            echo "Image upload failed. Check directory permissions.";
        }
    } else {
        http_response_code(400);
        echo "No file uploaded.";
    }
    exit;
}

// For uploaded_files (project files)
function delete_file() {
    global $conn;
    $id = $_POST['id'];
    $user_id = $_SESSION['login_id'];
    $is_admin = ($_SESSION['login_type'] == 1);    
    // Get file info
    $file = $conn->query("SELECT uploaded_by FROM uploaded_files WHERE id = $id")->fetch_assoc();
    
    // Check permissions
    if(!$is_admin && $file['uploaded_by'] != $user_id) {
        return 0; // Not authorized
    }
    
    // Soft delete
    return $conn->query("UPDATE uploaded_files SET is_deleted = 1 WHERE id = $id") ? 1 : 0;
}

// For progress_files
function delete_progress_file() {
    global $conn;
    $id = $_POST['id'];
    $user_id = $_SESSION['login_id'];
    $is_admin = ($_SESSION['login_type'] == 1);    
    // Get file info
    $file = $conn->query("SELECT uploaded_by FROM progress_files WHERE id = $id")->fetch_assoc();
    
    // Check permissions
    if(!$is_admin && $file['uploaded_by'] != $user_id) {
        return 0; // Not authorized
    }
    
    // Soft delete
    return $conn->query("UPDATE progress_files SET is_deleted = 1 WHERE id = $id") ? 1 : 0;
}
if ($action == 'delete_progress_file') {
    $save = $crud->delete_progress_file();
    if ($save)
        echo $save;
} 

if ($action == 'get_project_files') {
    $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : 0;
    $files = $crud->get_project_files($project_id);
    if ($files)
        echo $files;
}

if ($action == 'get_uploaded_files') {
    $files = $crud->get_uploaded_files();
    if ($files)
        echo $files;
}
if (isset($_GET['action']) && $_GET['action'] == 'upload_progress_file') {
    // First, include the database connection at the top
    include 'db_connect.php';
    
    // Define allowed file types
    $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowedDocTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'dwg'];
    
    if (isset($_FILES['file']['name'])) {
        // Get project_id and task_id from POST
        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        $task_id = isset($_POST['progress_id']) ? intval($_POST['progress_id']) : 0;
        
        // Determine file type based on the request
        $fileType = isset($_POST['type']) ? $_POST['type'] : 'image';
        $originalName = $_FILES['file']['name'];
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        // Validate file type
        if ($fileType == 'image' && !in_array($ext, $allowedImageTypes)) {
            echo json_encode([
                'success' => false,
                'message' => 'Only JPG, JPEG, PNG, GIF, and WEBP files are allowed for images.'
            ]);
            exit;
        } elseif ($fileType == 'document' && !in_array($ext, $allowedDocTypes)) {
            echo json_encode([
                'success' => false,
                'message' => 'Only PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, and DWG files are allowed for documents.'
            ]);
            exit;
        }
        
        // Set appropriate upload directory
        $uploadDir = $fileType == 'image' ? 'uploads/progress_images/' : 'uploads/progress_documents/';
        
        // Create directories if they don't exist with more permissive rights
        if (!is_dir('uploads/')) {
            mkdir('uploads/', 0777, true);
        }
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename to prevent overwriting
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._]/', '', $originalName);
        $path = $uploadDir . $filename;
        
        // Check file size (limit to 15MB for documents, 5MB for images)
        $maxSize = $fileType == 'image' ? 5242880 : 15728640; // 5MB or 15MB in bytes
        if ($_FILES['file']['size'] > $maxSize) {
            $maxSizeMB = $maxSize / 1048576; // Convert to MB
            echo json_encode([
                'success' => false,
                'message' => "File size exceeds the {$maxSizeMB}MB limit."
            ]);
            exit;
        }
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
            // Construct the URL to the uploaded file
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
            $host = $_SERVER['HTTP_HOST'];
            $scriptPath = dirname($_SERVER['PHP_SELF']);
            $basePath = rtrim($protocol . $host . $scriptPath, '/') . '/';
            $url = $basePath . $path;
            
            // Log the upload for security and tracking purposes
            $logMsg = date('Y-m-d H:i:s') . " - Uploaded progress {$fileType}: {$originalName} ({$_FILES['file']['size']} bytes) to {$path} by user " . (isset($_SESSION['login_id']) ? $_SESSION['login_id'] : 'unknown') . "\n";
            error_log($logMsg, 3, "uploads/upload_log.txt");
            
            // Inside the upload_progress_file action
            if (isset($_SESSION['login_id'])) {
                $user_id = $_SESSION['login_id']; 
                
                try {
                    // Insert into progress_files only
                    $stmt = $conn->prepare("INSERT INTO progress_files (filename, original_name, file_type, file_path, url, uploaded_by, project_id, task_id, upload_date) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
                    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                    $stmt->bind_param("sssssiii", $filename, $originalName, $ext, $path, $url, $user_id, $project_id, $task_id);
                    if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
                    $file_id = $conn->insert_id;
                    $stmt->close();
                    
                    echo json_encode([
                        'success' => true,
                        'id' => $file_id,
                        'url' => $url,
                        'filename' => $filename,
                        'originalName' => $originalName,
                        'fileType' => $fileType,
                        'extension' => $ext
                    ]);
                } catch (Exception $e) {
                    error_log("Upload Error: " . $e->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Database error: ' . $e->getMessage()
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not logged in.'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'File upload failed. Check directory permissions.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No file uploaded.'
        ]);
    }
    exit;
}
// Add these CRUD operations for progress files


function get_progress_files($progress_id = 0) {
    include 'db_connect.php';
    $progress_id = $conn->real_escape_string($progress_id);
    
    $qry = $conn->query("SELECT * FROM progress_files WHERE progress_id = {$progress_id} AND is_deleted = 0 ORDER BY upload_date DESC");
    $data = array();
    
    while ($row = $qry->fetch_assoc()) {
        $data[] = $row;
    }
    
    return json_encode($data);
}

function get_all_progress_files($project_id = 0) {
    include 'db_connect.php';
    $project_id = $conn->real_escape_string($project_id);
    
    $qry = $conn->query("SELECT pf.*, p.title as progress_title 
                       FROM progress_files pf 
                       LEFT JOIN project_progress p ON pf.progress_id = p.id 
                       WHERE pf.project_id = {$project_id} AND pf.is_deleted = 0 
                       ORDER BY pf.upload_date DESC");
    $data = array();
    
    while ($row = $qry->fetch_assoc()) {
        $data[] = $row;
    }
    
    return json_encode($data);
}
// Save team members
if($action == 'save_team_members'){
    $project_id = $_POST['project_id'];
    $user_ids = isset($_POST['user_ids']) ? $_POST['user_ids'] : array();
    
    // Get current project data
    $qry = $conn->query("SELECT * FROM project_list WHERE id = {$project_id}");
    if($qry->num_rows > 0){
        $project = $qry->fetch_assoc();
        
        // Get current user IDs and filter out designers (type=4) and estimators (type=5)
        $current_user_ids = !empty($project['user_ids']) ? explode(',', $project['user_ids']) : array();
        $designers_and_estimators = array();
        
        if(!empty($current_user_ids)){
            $special_users = $conn->query("SELECT id FROM users WHERE id IN (" . implode(',', $current_user_ids) . ") AND (type = 4 OR type = 5)");
            while($row = $special_users->fetch_assoc()){
                $designers_and_estimators[] = $row['id'];
            }
        }
        
        // Combine new team members with existing designers and estimators
        $final_user_ids = array_merge($user_ids, $designers_and_estimators);
        $final_user_ids = array_unique($final_user_ids);
        
        // Update the project
        if(!empty($final_user_ids)){
            $user_ids_str = implode(',', $final_user_ids);
            $update = $conn->query("UPDATE project_list SET user_ids = '{$user_ids_str}' WHERE id = {$project_id}");
        } else {
            $update = $conn->query("UPDATE project_list SET user_ids = NULL WHERE id = {$project_id}");
        }
        
        if($update){
            echo 1;
        } else {
            echo 2;
        }
    }
}

// Save designers
if($action == 'save_designers'){
    $project_id = $_POST['project_id'];
    $designer_ids = isset($_POST['designer_ids']) ? $_POST['designer_ids'] : array();
    
    // Format designer IDs as comma-separated string
    $designer_ids_str = !empty($designer_ids) ? implode(',', $designer_ids) : "";
    
    // Update the project with new designer IDs
    $update = $conn->query("UPDATE project_list SET designer_ids = '{$designer_ids_str}' WHERE id = {$project_id}");
    
    if($update){
        echo 1;
    } else {
        echo 2;
    }
}

// Save estimator
if($action == 'save_estimator'){
    $project_id = $_POST['project_id'];
    $estimator_id = !empty($_POST['estimator_id']) ? $_POST['estimator_id'] : NULL;
    
    // Update the project
    if(!empty($estimator_id)){
        $update = $conn->query("UPDATE project_list SET estimator_id = '{$estimator_id}' WHERE id = {$project_id}");
        
        // Also add estimator to user_ids if not already there
        $qry = $conn->query("SELECT user_ids FROM project_list WHERE id = {$project_id}");
        $row = $qry->fetch_assoc();
        $current_user_ids = !empty($row['user_ids']) ? explode(',', $row['user_ids']) : array();
        
        if(!in_array($estimator_id, $current_user_ids)){
            $current_user_ids[] = $estimator_id;
            $user_ids_str = implode(',', $current_user_ids);
            $conn->query("UPDATE project_list SET user_ids = '{$user_ids_str}' WHERE id = {$project_id}");
        }
    } else {
        $update = $conn->query("UPDATE project_list SET estimator_id = NULL WHERE id = {$project_id}");
    }
    
    if($update){
        echo 1;
    } else {
        echo 2;
    }
}
ob_end_flush();
?>