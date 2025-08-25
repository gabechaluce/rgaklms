<?php
session_start();
require_once('db_connect.php');


// Get session values safely
$user_id = $_SESSION['login_id'] ?? null;
$user_type = $_SESSION['login_type'] ?? null;


$events = [];


// Base SQL (non-urgent events visible to everyone)
$sql = "
    SELECT id, title, start_datetime AS start, end_datetime AS end, color
    FROM events
    WHERE event_type != 'urgent_meeting'
";


// Extend SQL if user is logged-in to also fetch urgent_meeting that they are allowed to see
if ($user_id && $user_type) {
    $sql .= "
    UNION
    SELECT e.id, e.title, e.start_datetime AS start, e.end_datetime AS end, e.color
    FROM events e
    LEFT JOIN event_visibility ev ON e.id = ev.event_id
    WHERE e.event_type = 'urgent_meeting'
      AND (ev.user_id = ? OR ev.role_id = ? OR e.created_by = ?)
    ";
}


$stmt = $conn->prepare($sql);


// Bind only if user is logged-in
if ($user_id && $user_type) {
    $stmt->bind_param("iii", $user_id, $user_type, $user_id);
}


$stmt->execute();
$result = $stmt->get_result();


// Fetch the events
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start'],
        'end' => $row['end'],
        'color' => $row['color'],
        'allDay' => false
    ];
}


// Return events as JSON
header('Content-Type: application/json');
echo json_encode($events);
?>
