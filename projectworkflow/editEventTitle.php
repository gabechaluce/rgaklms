
<?php
session_start();
require_once('db_connect.php');


// Logging function
function logError($message) {
    error_log("Event Update Error: " . $message);
}


$allowed_roles = [1, 2, 3, 4, 5, 6, 7, 8, 13]; // Admin, PM, Inventory Coord, Project Coord, Production Sup
if (!isset($_SESSION['login_type']) || !in_array($_SESSION['login_type'], $allowed_roles)) {
    logError("Unauthorized access by user type: " . $_SESSION['login_type']);
    echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logError("Invalid request method");
    die(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
}


$conn->begin_transaction();


try {
    if (isset($_POST['delete']) && $_POST['delete'] == 'on') {
        $eventId = (int)$_POST['id'];
        $conn->query("DELETE FROM event_visibility WHERE event_id = $eventId");
        $conn->query("DELETE FROM events WHERE id = $eventId");
        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Event deleted']);
        exit;
    }


    $required = ['id', 'title', 'color', 'start', 'end'];
    foreach ($required as $field) {
        if (!isset($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }


    $eventId = (int)$_POST['id'];
    $title = $conn->real_escape_string($_POST['title']);
    $color = $conn->real_escape_string($_POST['color']);
    $start = $conn->real_escape_string($_POST['start']);
    $end = $conn->real_escape_string($_POST['end']);


    // ✅ Automatically detect event_type based on color
    $event_type = ($color === '#FF0000') ? 'urgent_meeting': null;


    // Update event details safely
    $updateQuery = "UPDATE events SET
        title = '$title',
        color = '$color',
        start_datetime = '$start',
        end_datetime = '$end',
        event_type = " . ($event_type ? "'$event_type'" : "NULL") . "
        WHERE id = $eventId";


    $updateResult = $conn->query($updateQuery);
    if (!$updateResult) {
        throw new Exception("Failed to update event: " . $conn->error);
    }


    // ✅ Handle visibility for urgent meetings only
    if ($event_type === 'urgent_meeting') {
        $conn->query("DELETE FROM event_visibility WHERE event_id = $eventId");


        $userStmt = $conn->prepare("INSERT INTO event_visibility (event_id, user_id) VALUES (?, ?)");
        $roleStmt = $conn->prepare("INSERT INTO event_visibility (event_id, role_id) VALUES (?, ?)");


        $currentUserId = (int)$_SESSION['login_id'];
        $currentUserRole = (int)$_SESSION['login_type'];


        if (empty($_POST['selected_users'])) {
            $userStmt->bind_param("ii", $eventId, $currentUserId);
            $userStmt->execute();
        } else {
            foreach ($_POST['selected_users'] as $userId) {
                $userId = (int)$userId;
                $userStmt->bind_param("ii", $eventId, $userId);
                $userStmt->execute();
            }
        }


        if (empty($_POST['selected_positions'])) {
            $roleStmt->bind_param("ii", $eventId, $currentUserRole);
            $roleStmt->execute();
        } else {
            foreach ($_POST['selected_positions'] as $roleId) {
                $roleId = (int)$roleId;
                $roleStmt->bind_param("ii", $eventId, $roleId);
                $roleStmt->execute();
            }
        }
    }


    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Event updated']);


} catch (Exception $e) {
    $conn->rollback();
    logError($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
$conn->close();
?>
