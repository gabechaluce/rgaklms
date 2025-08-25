<?php
session_start();
require_once('db_connect.php');
header('Content-Type: application/json');


// Configuration
$allowed_roles = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];

// Authorization check
if (!isset($_SESSION['login_type']) || !in_array($_SESSION['login_type'], $allowed_roles)) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}


// Input validation
if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Valid Event ID required']));
}


$eventId = (int)$_GET['event_id'];
$userId = $_SESSION['login_id'] ?? 0;
$userRole = $_SESSION['login_type'] ?? 0;


try {
    // Verify event exists
    $stmt = $conn->prepare("SELECT id, created_by FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $eventResult = $stmt->get_result();
   
    if ($eventResult->num_rows === 0) {
        die(json_encode(['status' => 'error', 'message' => 'Event not found']));
    }
    $stmt->close();


    // Authorization check
    if ($userRole != 1) { // Non-admin
        $stmt = $conn->prepare("
            SELECT 1 FROM event_visibility
            WHERE event_id = ?
            AND (user_id = ? OR role_id = ?)
            LIMIT 1
        ");
        $stmt->bind_param("iii", $eventId, $userId, $userRole);
        $stmt->execute();
        $visibilityResult = $stmt->get_result();
       
        if ($visibilityResult->num_rows === 0) {
            die(json_encode(['status' => 'error', 'message' => 'Access denied']));
        }
        $stmt->close();
    }


   
    // Modified authorization check for non-admins
if ($userRole != 1) {
    $stmt = $conn->prepare("
        SELECT 1 FROM events
        WHERE id = ? AND created_by = ?
        UNION
        SELECT 1 FROM event_visibility
        WHERE event_id = ? AND (user_id = ? OR role_id = ?)
        LIMIT 1
    ");
    $stmt->bind_param("iiiii", $eventId, $userId, $eventId, $userId, $userRole);
    $stmt->execute();
    $visibilityResult = $stmt->get_result();
   
    if ($visibilityResult->num_rows === 0) {
        die(json_encode(['status' => 'error', 'message' => 'Access denied']));
    }
    $stmt->close();
}


    // Fetch visibility data
    $selectedUsers = [];
    $selectedPositions = [];


    // Get users
    $stmt = $conn->prepare("SELECT user_id FROM event_visibility WHERE event_id = ? AND user_id IS NOT NULL");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $userResult = $stmt->get_result();
    while ($row = $userResult->fetch_assoc()) {
        $selectedUsers[] = (int)$row['user_id'];
    }
    $stmt->close();


    // Get positions
    $stmt = $conn->prepare("SELECT role_id FROM event_visibility WHERE event_id = ? AND role_id IS NOT NULL");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $positionResult = $stmt->get_result();
    while ($row = $positionResult->fetch_assoc()) {
        $selectedPositions[] = (int)$row['role_id'];
    }
    $stmt->close();


    echo json_encode([
        'status' => 'success',
        'data' => [
            'selected_users' => $selectedUsers,
            'selected_positions' => $selectedPositions
        ]
    ]);


} catch (Exception $e) {
    error_log("Visibility Error: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error occurred'
    ]);
}
?>


