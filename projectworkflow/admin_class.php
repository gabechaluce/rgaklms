<?php
session_start();
ini_set('display_errors', 1);
Class Action {
	private $db;
	private $conn;
	public function __construct() {
		ob_start();
   	include 'db_connect.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	// Updated login() function with bcrypt
	function login() {
		$email = $_POST['email'] ?? '';
		$password = $_POST['password'] ?? '';

		// Use prepared statement with parameter binding
		$stmt = $this->db->prepare("SELECT *, concat(firstname,' ',lastname) as name 
								   FROM users 
								   WHERE email = ?");
		
		$stmt->bind_param("s", $email);
		$stmt->execute();
		
		$qry = $stmt->get_result();
		
		if($qry->num_rows > 0) {
			$user = $qry->fetch_assoc();
			
			// Verify password with bcrypt
			if(password_verify($password, $user['password'])) {
				foreach ($user as $key => $value) {
					if($key != 'password' && !is_numeric($key))
						$_SESSION['login_'.$key] = $value;
				}
				return 1;
			}
		}
		return 2;
	}

function logout(){
    session_destroy();
    foreach ($_SESSION as $key => $value) {
        unset($_SESSION[$key]);
    }
    header("location:../login.php"); // Add ../ to go to parent directory
}
	// Updated save_user() with bcrypt
	function save_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		
		// Handle password with bcrypt
		if(!empty($password)){
			$hashed_password = password_hash($password, PASSWORD_BCRYPT);
			$data .= ", password='$hashed_password' ";
		}
		
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";
		}
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			return 1;
		}
	}

	// Updated signup() with bcrypt
	function signup(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass')) && !is_numeric($k)){
				if($k =='password'){
					if(empty($v))
						continue;
					// Use bcrypt hashing
					$v = password_hash($v, PASSWORD_BCRYPT);
				}
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}

		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";
		}
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			if(empty($id))
				$id = $this->db->insert_id;
			foreach ($_POST as $key => $value) {
				if(!in_array($key, array('id','cpass','password')) && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			$_SESSION['login_id'] = $id;
			if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
				$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	// Updated update_user() with bcrypt
	function update_user(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id','cpass','table','password')) && !is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		
		$check = $this->db->query("SELECT * FROM users where email ='$email' ".(!empty($id) ? " and id != {$id} " : ''))->num_rows;
		if($check > 0){
			return 2;
			exit;
		}
		
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'assets/uploads/'. $fname);
			$data .= ", avatar = '$fname' ";
		}
		
		// Handle password with bcrypt
		if(!empty($password)) {
			$hashed_password = password_hash($password, PASSWORD_BCRYPT);
			$data .= " ,password='$hashed_password' ";
		}
		
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set $data");
		}else{
			$save = $this->db->query("UPDATE users set $data where id = $id");
		}

		if($save){
			foreach ($_POST as $key => $value) {
				if($key != 'password' && !is_numeric($key))
					$_SESSION['login_'.$key] = $value;
			}
			if(isset($_FILES['img']) && !empty($_FILES['img']['tmp_name']))
					$_SESSION['login_avatar'] = $fname;
			return 1;
		}
	}

	function delete_user(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM users where id = ".$id);
		if($delete)
			return 1;
	}

	function save_system_settings(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!is_numeric($k)){
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if($_FILES['cover']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'../assets/uploads/'. $fname);
			$data .= ", cover_img = '$fname' ";

		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if($chk->num_rows > 0){
			$save = $this->db->query("UPDATE system_settings set $data where id =".$chk->fetch_array()['id']);
		}else{
			$save = $this->db->query("INSERT INTO system_settings set $data");
		}
		if($save){
			foreach($_POST as $k => $v){
				if(!is_numeric($k)){
					$_SESSION['system'][$k] = $v;
				}
			}
			if($_FILES['cover']['tmp_name'] != ''){
				$_SESSION['system']['cover_img'] = $fname;
			}
			return 1;
		}
	}
	function save_image(){
		extract($_FILES['file']);
		if(!empty($tmp_name)){
			$fname = strtotime(date("Y-m-d H:i"))."_".(str_replace(" ","-",$name));
			$move = move_uploaded_file($tmp_name,'assets/uploads/'. $fname);
			$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
			$hostName = $_SERVER['HTTP_HOST'];
			$path =explode('/',$_SERVER['PHP_SELF']);
			$currentPath = '/'.$path[1]; 
			if($move){
				return $protocol.'://'.$hostName.$currentPath.'/assets/uploads/'.$fname;
			}
		}
	}

	function save_inquiry() {
		extract($_POST);
		$id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
		error_log("Received ID: " . $id);
	
		$data = "";
		foreach ($_POST as $k => $v) {
			// Exclude 'id', 'user_ids', and 'manager_id' from general processing
			if (!in_array($k, ['id', 'user_ids', 'manager_id']) && !is_numeric($k)) {
				if ($k == 'description') {
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				}
				$v = $this->db->real_escape_string($v);
	
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
	
		// Handle user_ids array
		if (isset($_POST['user_ids'])) {
			$user_ids = array_map([$this->db, 'real_escape_string'], $_POST['user_ids']);
			$data .= ", user_ids='" . implode(',', $user_ids) . "' ";
		}
	
		// Handle manager_id array
		if (isset($_POST['manager_id'])) {
			$manager_ids = array_map([$this->db, 'real_escape_string'], $_POST['manager_id']);
			$data .= ", manager_id='" . implode(',', $manager_ids) . "' ";
		}
	
		if ($id !== null && $id > 0) {
			$checkQuery = $this->db->query("SELECT id FROM inquiry_list WHERE id = $id");
			if ($checkQuery->num_rows > 0) {
				$query = "UPDATE inquiry_list SET $data WHERE id = $id";
			} else {
				error_log("Inquiry ID $id not found. Inserting new record instead.");
				$query = "INSERT INTO inquiry_list SET $data";
			}
		} else {
			$query = "INSERT INTO inquiry_list SET $data"; // Let MySQL auto-generate the ID
		}
	
		$save = $this->db->query($query);
		if (!$save) {
			error_log("Database Error: " . $this->db->error);
			return 0;
		}
		return 1;
	}

	function delete_inquiry(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM inquiry_list where id = $id");
		if($delete){
			return 1;
		}
	}

	function save_track() {
		extract($_POST);
		$id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
		error_log("Received ID: " . $id);
	
		$data = "";
		foreach ($_POST as $k => $v) {
			// Exclude 'id' from general processing
			if ($k != 'id' && !is_numeric($k)) {
				if ($k == 'purpose') {
					$v = htmlentities(str_replace("'", "&#x2019;", $v));
				}
				$v = $this->db->real_escape_string($v);
	
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}
	
		if ($id !== null && $id > 0) {
			$checkQuery = $this->db->query("SELECT id FROM track WHERE id = $id");
			if ($checkQuery->num_rows > 0) {
				$query = "UPDATE track SET $data WHERE id = $id";
			} else {
				error_log("Track ID $id not found. Inserting new record instead.");
				$query = "INSERT INTO track SET $data";
			}
		} else {
			$query = "INSERT INTO track SET $data"; // Let MySQL auto-generate the ID
		}
	
		$save = $this->db->query($query);
		if (!$save) {
			error_log("Database Error: " . $this->db->error);
			return 0;
		}
		return 1;
	}
	
	function delete_track(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM track WHERE id = $id");
		if($delete){
			return 1;
		}
		return 0;
	}
	// admin_class.php
function save_project() {
    extract($_POST);
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Check if the required columns exist in the database
    $table_info = $this->db->query("DESCRIBE project_list");
    $columns = [];
    while($row = $table_info->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    $data = array();
    
    // Handle regular fields including the new ones
    $regular_fields = ['name', 'description', 'start_date', 'end_date', 'full_name', 'location', 'dimension', 'project_cost'];
    
    foreach ($_POST as $k => $v) {
        // Skip arrays and specific keys that need special handling
        if (!in_array($k, ['id', 'user_ids', 'manager_id', 'estimator_ids', 'designer_ids', 'inventory_ids', 'coordinator_ids', 'status']) 
            && !is_numeric($k) && !is_array($v) && in_array($k, $columns)) {
            
            if ($k == 'description') {
                $v = htmlentities(str_replace("'", "&#x2019;", $v));
            }
            
            // Handle numeric fields properly
            if ($k == 'project_cost') {
                $v = !empty($v) ? floatval($v) : 0;
                $data[$k] = $v;
            } else {
                $v = $this->db->real_escape_string($v);
                $data[$k] = $v;
            }
        }
    }

    // Set status: 1 for new projects, use submitted value for existing
    if ($id == 0) {
        $data['status'] = '1';
    } else {
        if (isset($_POST['status'])) {
            $data['status'] = $this->db->real_escape_string($_POST['status']);
        }
    }

    // Convert data array to string for regular fields
    $data_str = '';
    foreach ($data as $k => $v) {
        if ($k == 'project_cost') {
            $data_str .= ", $k=$v";
        } else {
            $data_str .= ", $k='$v'";
        }
    }
    $data_str = ltrim($data_str, ', ');

    // Handle user_ids (Team Members)
    if (isset($_POST['user_ids']) && in_array('user_ids', $columns)) {
        if (is_array($_POST['user_ids'])) {
            $user_ids = array_map(function($item) {
                return $this->db->real_escape_string($item);
            }, $_POST['user_ids']);
            $data_str .= ", user_ids='" . implode(',', $user_ids) . "'";
        } else {
            $user_ids = $this->db->real_escape_string($_POST['user_ids']);
            $data_str .= ", user_ids='$user_ids'";
        }
    }

    // Handle manager_id
    if (isset($_POST['manager_id']) && in_array('manager_id', $columns)) {
        if (is_array($_POST['manager_id'])) {
            $manager_ids = array_map(function($item) {
                return $this->db->real_escape_string($item);
            }, $_POST['manager_id']);
            $data_str .= ", manager_id='" . implode(',', $manager_ids) . "'";
        } else {
            $manager_id = $this->db->real_escape_string($_POST['manager_id']);
            $data_str .= ", manager_id='$manager_id'";
        }
    }
    
    // Handle coordinator_ids - only if column exists
    if (isset($_POST['coordinator_ids']) && in_array('coordinator_ids', $columns)) {
        if (is_array($_POST['coordinator_ids'])) {
            $coordinator_ids = array_map(function($item) {
                return $this->db->real_escape_string($item);
            }, $_POST['coordinator_ids']);
            $data_str .= ", coordinator_ids='" . implode(',', $coordinator_ids) . "'";
        } else {
            $coordinator_ids = $this->db->real_escape_string($_POST['coordinator_ids']);
            $data_str .= ", coordinator_ids='$coordinator_ids'";
        }
    }
    
    // Handle estimator_ids - only if column exists
    if (isset($_POST['estimator_ids']) && in_array('estimator_ids', $columns)) {
        if (is_array($_POST['estimator_ids'])) {
            $estimator_ids = array_map(function($item) {
                return $this->db->real_escape_string($item);
            }, $_POST['estimator_ids']);
            $data_str .= ", estimator_ids='" . implode(',', $estimator_ids) . "'";
        } else {
            $estimator_ids = $this->db->real_escape_string($_POST['estimator_ids']);
            $data_str .= ", estimator_ids='$estimator_ids'";
        }
    }
    
    // Handle designer_ids - only if column exists
    if (isset($_POST['designer_ids']) && in_array('designer_ids', $columns)) {
        if (is_array($_POST['designer_ids'])) {
            $designer_ids = array_map(function($item) {
                return $this->db->real_escape_string($item);
            }, $_POST['designer_ids']);
            $data_str .= ", designer_ids='" . implode(',', $designer_ids) . "'";
        } else {
            $designer_ids = $this->db->real_escape_string($_POST['designer_ids']);
            $data_str .= ", designer_ids='$designer_ids'";
        }
    }
    
    // Handle inventory_ids - only if column exists
    if (isset($_POST['inventory_ids']) && in_array('inventory_ids', $columns)) {
        if (is_array($_POST['inventory_ids'])) {
            $inventory_ids = array_map(function($item) {
                return $this->db->real_escape_string($item);
            }, $_POST['inventory_ids']);
            $data_str .= ", inventory_ids='" . implode(',', $inventory_ids) . "'";
        } else {
            $inventory_ids = $this->db->real_escape_string($_POST['inventory_ids']);
            $data_str .= ", inventory_ids='$inventory_ids'";
        }
    }

    $data_str .= ", notified=0";

    // Build and execute query
    if ($id > 0) {
        $check = $this->db->query("SELECT id FROM project_list WHERE id = $id");
        if ($check->num_rows > 0) {
            $save = $this->db->query("UPDATE project_list SET $data_str WHERE id = $id");
            if (!$save) {
                error_log("Update Error: " . $this->db->error);
                return 0;
            }
        } else {
            return 0;
        }
    } else {
        $save = $this->db->query("INSERT INTO project_list SET $data_str");
        if (!$save) {
            error_log("Insert Error: " . $this->db->error);
            return 0;
        }
    }

    if ($id == 0) { // New project
        $new_project_id = $this->db->insert_id;
        $user_id = $_SESSION['login_id'];
        $this->db->query("UPDATE uploaded_files SET project_id = $new_project_id WHERE project_id = 0 AND uploaded_by = $user_id");
    }
    
    return 1;
}

function delete_project(){
    extract($_POST);
    
    // Delete related records first to maintain referential integrity
    $this->db->query("DELETE FROM task_list WHERE project_id = $id");
    $this->db->query("DELETE FROM user_productivity WHERE project_id = $id");
    $this->db->query("DELETE FROM uploaded_files WHERE project_id = $id");
    $this->db->query("DELETE FROM progress_files WHERE project_id = $id");
    
    // Finally delete the project itself
    $delete = $this->db->query("DELETE FROM project_list WHERE id = $id");
    
    if($delete){
        return 1;
    }
    return 0;
}

	function save_task(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'description')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		if(empty($id)){
			$save = $this->db->query("INSERT INTO task_list set $data");
		}else{
			$save = $this->db->query("UPDATE task_list set $data where id = $id");
		}
		if($save){
			return 1;
		}
	}
	function delete_task(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM task_list where id = $id");
		if($delete){
			return 1;
		}
	} 
	function save_progress(){


		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id')) && !is_numeric($k)){
				if($k == 'comment')
					$v = htmlentities(str_replace("'","&#x2019;",$v));
				if(empty($data)){
					$data .= " $k='$v' ";
				}else{
					$data .= ", $k='$v' ";
				}
			}
		}
		$dur = abs(strtotime("2020-01-01 ".$end_time)) - abs(strtotime("2020-01-01 ".$start_time));
		$dur = $dur / (60 * 60);
		$data .= ", time_rendered='$dur' ";

		// Handle file upload
		if(isset($_FILES['file']) && $_FILES['file']['tmp_name'] != ''){
			$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['file']['name'];
			$move = move_uploaded_file($_FILES['file']['tmp_name'],'uploads/'. $fname);
			$data .= ", file = '$fname' ";
		}

		if(empty($id)){
			$data .= ", user_id={$_SESSION['login_id']} ";
			
			$save = $this->db->query("INSERT INTO user_productivity set $data");
		}else{
			$save = $this->db->query("UPDATE user_productivity set $data where id = $id");
		}
		if($save){
			return 1;
		}
	
	}
	function delete_progress(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM user_productivity where id = $id");
		if($delete){
			return 1;
		}
	}
	function get_report(){
		extract($_POST);
		$data = array();
		$get = $this->db->query("SELECT t.*,p.name as ticket_for FROM ticket_list t inner join pricing p on p.id = t.pricing_id where date(t.date_created) between '$date_from' and '$date_to' order by unix_timestamp(t.date_created) desc ");
		while($row= $get->fetch_assoc()){
			$row['date_created'] = date("M d, Y",strtotime($row['date_created']));
			$row['name'] = ucwords($row['name']);
			$row['adult_price'] = number_format($row['adult_price'],2);
			$row['child_price'] = number_format($row['child_price'],2);
			$row['amount'] = number_format($row['amount'],2);
			$data[]=$row;
		}
		return json_encode($data);
	}
	public function update_project() {
		if (isset($_POST['id']) && !empty($_POST['name']) && !empty($_POST['start_date']) && !empty($_POST['end_date'])) {
			// Sanitize inputs
			$id = $this->conn->real_escape_string($_POST['id']);
			$name = $this->conn->real_escape_string($_POST['name']);
			$status = isset($_POST['status']) ? $this->conn->real_escape_string($_POST['status']) : '0';
			$start_date = $this->conn->real_escape_string($_POST['start_date']);
			$end_date = $this->conn->real_escape_string($_POST['end_date']);
			$manager_id = isset($_POST['manager_id']) ? $this->conn->real_escape_string($_POST['manager_id']) : NULL;
			$description = isset($_POST['description']) ? $this->conn->real_escape_string($_POST['description']) : '';
	
			// Prepare the SQL query to update the project
			$query = "UPDATE project_list SET 
						name = '$name',
						start_date = '$start_date',
						end_date = '$end_date',
						status = '$status',
						manager_id = '$manager_id',
						description = '$description'
					  WHERE id = '$id'";
	
			// Execute the query
			$result = $this->conn->query($query);
	
			// Check for query errors
			if (!$result) {
				// If query fails, return error message
				return json_encode(['status' => 'error', 'message' => 'Failed to update project. ' . $this->conn->error]);
			}
	
			// Return success if the query was successful
			return json_encode(['status' => 'success', 'message' => 'Project updated successfully.']);
		} else {
			return json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
		}
	}	

	public function update_project_status() {
		extract($_POST);
		$id = intval($id);
		$status = intval($status);
		$query = $this->db->query("UPDATE project_list SET status = $status WHERE id = $id");
		return $query ? 1 : 0;
	}
	// Add these functions to your admin_class.php file

function get_uploaded_files() {
    $where = "";
    if($_SESSION['login_type'] == 2) { // For project managers
        $where = " WHERE f.project_id IN (SELECT id FROM project_list WHERE manager_id = '{$_SESSION['login_id']}') ";
    } elseif($_SESSION['login_type'] == 3) { // For team members
        $where = " WHERE f.project_id IN (SELECT id FROM project_list WHERE FIND_IN_SET('{$_SESSION['login_id']}', user_ids)) ";
    }
    
    $qry = $this->db->query("SELECT f.*, p.name as project_name, CONCAT(u.firstname, ' ', u.lastname) as uploader 
                            FROM uploaded_files f 
                            LEFT JOIN project_list p ON f.project_id = p.id 
                            LEFT JOIN users u ON f.uploaded_by = u.id 
                            $where 
                            ORDER BY f.upload_date DESC");
    $data = array();
    while($row = $qry->fetch_assoc()) {
        $data[] = $row;
    }
    return json_encode($data);
}
function delete_file() {
    extract($_POST);
    $id = intval($id);
    
    // Verify ownership before deletion
    $qry = $this->db->query("SELECT * FROM uploaded_files WHERE id = $id");
    if($qry->num_rows > 0) {
        $file = $qry->fetch_assoc();
        
        // Check if admin or owner
        if($_SESSION['login_type'] == 1 || $_SESSION['login_id'] == $file['uploaded_by']) {
            // Permanently delete the file
            $delete = $this->db->query("DELETE FROM uploaded_files WHERE id = $id");
            if($delete) {
                // Optionally delete the physical file too
                // if(file_exists($file['url'])) unlink($file['url']);
                return 1;
            }
        }
    }
    return 0;
}

function delete_progress_file() {
    extract($_POST);
    $id = intval($id);
    
    // Use prepared statements to prevent SQL injection
    $qry = $this->db->prepare("SELECT * FROM progress_files WHERE id = ?");
    $qry->bind_param("i", $id);
    $qry->execute();
    $result = $qry->get_result();
    
    if($result->num_rows > 0) {
        $file = $result->fetch_assoc();
        
        // Check if admin or owner
        if($_SESSION['login_type'] == 1 || $_SESSION['login_id'] == $file['uploaded_by']) {
            // Permanently delete the file
            $delete_qry = $this->db->prepare("DELETE FROM progress_files WHERE id = ?");
            $delete_qry->bind_param("i", $id);
            if($delete_qry->execute()) {
                // Optionally delete the physical file too
                // if(file_exists($file['url'])) unlink($file['url']);
                return 1;
            } else {
                error_log("Failed to delete progress file ID: " . $id);
                return 0;
            }
        } else {
            error_log("Permission denied: User " . $_SESSION['login_id'] . " attempted to delete file owned by " . $file['uploaded_by']);
            return 0;
        }
    }
    return 0;
}
function get_project_files($project_id) {
    $project_id = $this->db->real_escape_string($project_id);
    $qry = $this->db->query("SELECT f.*, CONCAT(u.firstname, ' ', u.lastname) as uploader 
                           FROM uploaded_files f 
                           LEFT JOIN users u ON f.uploaded_by = u.id 
                           WHERE f.project_id = '$project_id' 
                           ORDER BY f.upload_date DESC");
    $data = array();
    while($row = $qry->fetch_assoc()) {
        $data[] = $row;
    }
    return json_encode($data);
}
function delete_multiple_files() {
    error_log("delete_multiple_files() called");
    if (!isset($_POST['ids'])) {
        error_log("No IDs received");
        return 0;
    }

    $ids = $_POST['ids'];
    error_log("Received IDs: " . print_r($ids, true));
    $deleted_count = 0;

    foreach ($ids as $id) {
        // Sanitize the ID to prevent SQL injection
        $id = (int)$id;

        // Fetch the file information
        $qry = $this->db->query("SELECT * FROM uploaded_files WHERE id = $id");
        if ($qry->num_rows > 0) {
            $file = $qry->fetch_assoc();

            // Check if user has permission (admin or owner)
            if ($_SESSION['login_type'] == 1 || $_SESSION['login_id'] == $file['uploaded_by']) {
                // Delete the file from storage
                if (file_exists($file['file_path']) && is_file($file['file_path'])) {
                    unlink($file['file_path']);
                }

                // Delete the database entry
                $delete = $this->db->query("DELETE FROM uploaded_files WHERE id = $id");
                if ($delete) {
                    $deleted_count++;
                }
            }
        }
    }

	echo json_encode(['status' => $deleted_count > 0 ? 1 : 0]);
	return;
}
}
