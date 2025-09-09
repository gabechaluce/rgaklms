<?php
session_start();
require_once 'db_connect.php';
header('Content-Type: application/json');


// Authorization check
$allowed_roles = [1, 2, 3, 5, 6, 7, 10];
if (!isset($_SESSION['login_type']) || !in_array($_SESSION['login_type'], $allowed_roles)) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}


// Validate event ID
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid event ID']));
}


$eventId = (int)$_POST['id'];
$userId = $_SESSION['login_id'];
$isAdmin = ($_SESSION['login_type'] == 1);


try {
    // Fetch event details including creator
    $stmt = $conn->prepare("SELECT created_by, event_type FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
   
    if ($result->num_rows === 0) {
        die(json_encode(['status' => 'error', 'message' => 'Event not found']));
    }
   
    $event = $result->fetch_assoc();
    $stmt->close();


    // Permission check: Admin or creator only
    if (!$isAdmin && $event['created_by'] != $userId) {
        die(json_encode(['status' => 'error', 'message' => 'You can only delete your own events']));
    }


    // Delete visibility first
    $stmt = $conn->prepare("DELETE FROM event_visibility WHERE event_id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $stmt->close();


    // Delete event
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();


    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Event deleted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Deletion failed']);
    }
} catch (Exception $e) {
    error_log("Delete Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}
?>
