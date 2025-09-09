<?php
session_start();
require_once('db_connect.php');
date_default_timezone_set("Asia/Manila");
header('Content-Type: application/json');


// Allowed user types
$allowed_types = [1, 2, 3, 4, 5, 6, 7, 10];

// Authorization check
if (!isset($_SESSION['login_type']) || !in_array($_SESSION['login_type'], $allowed_types)) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required fields
    $required = ['title', 'start', 'end', 'event_type'];
    foreach ($required as $field) {
        // Check if field exists
        if (!isset($_POST[$field])) {
            echo json_encode(["status" => "error", "message" => "Missing required field: $field"]);
            exit;
        }


        // Check for empty values after trimming
        $value = trim($_POST[$field] ?? '');
        if (empty($value)) {
            echo json_encode(["status" => "error", "message" => "Field cannot be empty: $field"]);
            exit;
        }
    }


    // Sanitize and validate inputs
    $title = trim($_POST['title']);
    $start = date('Y-m-d H:i:s', strtotime($_POST['start']));
    $end = !empty($_POST['end']) ? date('Y-m-d H:i:s', strtotime($_POST['end'])) : null;
    $event_type = $_POST['event_type'];
    $created_by = $_SESSION['login_id'];
    $color = ($event_type == 'urgent_meeting') ? '#FF0000' : '#FF8C00';


    // Date validation
    if (!strtotime($start) || ($end && !strtotime($end))) {
        echo json_encode(["status" => "error", "message" => "Invalid date format"]);
        exit;
    }


    // Event type validation
    if (!in_array($event_type, ['events_holidays', 'urgent_meeting'])) {
        echo json_encode(["status" => "error", "message" => "Invalid event type"]);
        exit;
    }


    $conn->begin_transaction();


    try {
        // Insert main event
        $stmt = $conn->prepare("INSERT INTO events
            (title, start_datetime, end_datetime, color, event_type, created_by)
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $title, $start, $end, $color, $event_type, $created_by);
       
        if (!$stmt->execute()) {
            throw new Exception("Event insertion failed: " . $stmt->error);
        }
       
        $event_id = $conn->insert_id;
        $stmt->close();


        // Handle urgent meeting visibility
        if ($event_type === 'urgent_meeting') {
            $valid_positions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];


            // Process selected positions
            $selected_positions = [];
            if (!empty($_POST['selected_positions'])) {
                $selected_positions = array_filter(
                    $_POST['selected_positions'],
                    function($p) use ($valid_positions) {
                        return is_numeric($p) && in_array((int)$p, $valid_positions);
                    }
                );
            }


            // Insert valid positions
            if (!empty($selected_positions)) {
                $stmt = $conn->prepare("INSERT INTO event_visibility (event_id, role_id) VALUES (?, ?)");
                foreach ($selected_positions as $position_id) {
                    $stmt->bind_param("ii", $event_id, $position_id);
                    if (!$stmt->execute()) {
                        throw new Exception("Position visibility failed: " . $stmt->error);
                    }
                }
                $stmt->close();
            }


            // Process individual users
            $selected_users = [];
            if (!empty($_POST['selected_users'])) {
                $selected_users = array_filter($_POST['selected_users'], 'is_numeric');
            }


            // Insert valid users
            if (!empty($selected_users)) {
                $stmt = $conn->prepare("INSERT INTO event_visibility (event_id, user_id) VALUES (?, ?)");
                foreach ($selected_users as $user_id) {
                    $stmt->bind_param("ii", $event_id, $user_id);
                    if (!$stmt->execute()) {
                        throw new Exception("User visibility failed: " . $stmt->error);
                    }
                }
                $stmt->close();
            }
        }


        $conn->commit();


        // Return success response
        echo json_encode([
            "status" => "success",
            "message" => "Event added successfully",
            "event" => [
                'id' => $event_id,
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'color' => $color,
                'event_type' => $event_type,
                'created_by' => $created_by
            ]
        ]);


    } catch (Exception $e) {
        $conn->rollback();
        error_log("Database Error: " . $e->getMessage());
        echo json_encode([
            "status" => "error",
            "message" => "Failed to add event: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>


