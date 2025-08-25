<?php
include 'db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$project_list = [];

$qry = $conn->query("SELECT * FROM project_list");

if (!$qry) {
    die("Database Query Failed: " . $conn->error);
}

while ($row = $qry->fetch_assoc()) {
    $project_list[] = [
        'title' => $row['name'],
        'start' => date('c', strtotime($row['start_date'])), // ISO 8601 format
        'end' => date('c', strtotime($row['end_date'])), // ISO 8601 format
        'color' => '#0071c5'
    ];
}

header('Content-Type: application/json');
echo json_encode($project_list);
?>
