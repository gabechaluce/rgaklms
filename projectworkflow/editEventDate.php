<?php
session_start();
require_once('db_connect.php');


if (isset($_POST['id'], $_POST['start'], $_POST['end'])) {
    $id = intval($_POST['id']);
    $start = $_POST['start'];
    $end = $_POST['end'];


    // Get current event details
    $stmt = $conn->prepare("SELECT event_type, color FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Event not found."]);
        exit;
    }


    $event = $result->fetch_assoc();
    $event_type = $event['event_type'];
    $color = $event['color'];


    // Update the event date
    $updateStmt = $conn->prepare("UPDATE events SET start_datetime = ?, end_datetime = ? WHERE id = ?");
    $updateStmt->bind_param("ssi", $start, $end, $id);


    if (!$updateStmt->execute()) {
        echo json_encode(["status" => "error", "message" => "Error updating event date."]);
        exit;
    }


    // Check if it's an urgent meeting and visibility is missing for the user
    if ($event_type == 'urgent_meeting') {
        $user_id = $_SESSION['login_id'];
        $role_id = $_SESSION['login_type'];


        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM event_visibility WHERE event_id = ? AND (user_id = ? OR role_id = ?)");
        $checkStmt->bind_param("iii", $id, $user_id, $role_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $count = $checkResult->fetch_assoc()['count'];


        if ($count == 0) {
            // Automatically add visibility if missing
            $insertStmt = $conn->prepare("INSERT INTO event_visibility (event_id, user_id) VALUES (?, ?)");
            $insertStmt->bind_param("ii", $id, $user_id);
            $insertStmt->execute();
            $insertStmt->close();
        }


        $checkStmt->close();
    }


    echo json_encode(["status" => "success", "message" => "Event date updated."]);


    $updateStmt->close();
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "You can only edit Project Schedule in the Project List"]);
}
?>
