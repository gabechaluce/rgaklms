
<?php
session_start();
require_once('db_connect.php');
date_default_timezone_set("Asia/Manila");


// Configuration
$allowed_roles = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
$valid_positions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
// Authorization check
if (!isset($_SESSION['login_type']) || !in_array($_SESSION['login_type'], $allowed_roles)) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}


// Input validation
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid event ID']));
}


$eventId = (int)$_POST['id'];
$isAdmin = ($_SESSION['login_type'] == 1);


// Fetch existing event details
try {
    $stmt = $conn->prepare("SELECT event_type, color, created_by FROM events WHERE id = ?");
    $stmt->bind_param("i", $eventId);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingEvent = $result->fetch_assoc();
    $stmt->close();


    if (!$existingEvent) {
        die(json_encode(['status' => 'error', 'message' => 'Event not found']));
    }


    // Permission check: Non-admins can only edit their own events
    if (!$isAdmin && $existingEvent['created_by'] != $_SESSION['login_id']) {
        die(json_encode(['status' => 'error', 'message' => 'You can only edit your own events']));
    }


    $existingColor = $existingEvent['color'];
    $existingEventType = $existingEvent['event_type'];
} catch (Exception $e) {
    die(json_encode(['status' => 'error', 'message' => 'Failed to fetch event details: ' . $e->getMessage()]));
}


// Handle event type and color based on user role
if (!$isAdmin) {
    $color = $existingColor;
    $event_type = $existingEventType;


    // Force non-admins to only work with urgent meetings
    if ($existingEventType !== 'urgent_meeting') {
        die(json_encode(['status' => 'error', 'message' => 'Unauthorized event type modification']));
    }
} else {
    // Admin can change type/color
    $color = $_POST['color'] ?? $existingColor;
    switch ($color) {
        case '#FF0000': $event_type = 'urgent_meeting'; break;
        case '#FF8C00': $event_type = 'events_holidays'; break;
        case '#0071c5': $event_type = 'project_schedule'; break;
        default: $event_type = $existingEventType;
    }
}


// Process selected users and positions
$selected_users = [];
if (isset($_POST['selected_users']) && is_array($_POST['selected_users'])) {
    $selected_users = array_filter($_POST['selected_users'], 'is_numeric');
}
$selected_positions = [];
if (isset($_POST['selected_positions']) && is_array($_POST['selected_positions'])) {
    $selected_positions = array_filter($_POST['selected_positions'], function($p) use ($valid_positions) {
        return is_numeric($p) && in_array($p, $valid_positions);
    });
}


try {
    $conn->begin_transaction();


    // Update main event details
    $stmt = $conn->prepare("UPDATE events SET
        title = ?,
        start_datetime = ?,
        end_datetime = ?,
        color = ?,
        event_type = ?
        WHERE id = ?");
    $stmt->bind_param("sssssi",
        $_POST['title'],
        $_POST['start'],
        $_POST['end'],
        $color,
        $event_type,
        $eventId
    );
    $stmt->execute();
    $stmt->close();


    // Handle visibility only for urgent meetings
    if ($event_type === 'urgent_meeting') {
        // Clear existing visibility only if roles are selected
        $stmt = $conn->prepare("DELETE FROM event_visibility WHERE event_id = ?");
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $stmt->close();


        // Insert users if selected
        if (!empty($selected_users)) {
            $stmt = $conn->prepare("INSERT INTO event_visibility (event_id, user_id) VALUES (?, ?)");
            foreach ($selected_users as $userId) {
                $stmt->bind_param("ii", $eventId, $userId);
                $stmt->execute();
            }
            $stmt->close();
        }


        // Insert positions only if selected (no default roles)
        if (!empty($selected_positions)) {
            $stmt = $conn->prepare("INSERT INTO event_visibility (event_id, role_id) VALUES (?, ?)");
            foreach ($selected_positions as $positionId) {
                $stmt->bind_param("ii", $eventId, $positionId);
                $stmt->execute();
            }
            $stmt->close();
        }
    }


    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Event updated successfully']);
} catch (Exception $e) {
    $conn->rollback();
    error_log("Event Update Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $e->getMessage()]);
}
?>
