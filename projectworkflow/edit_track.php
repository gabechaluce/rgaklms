<?php
include 'db_connect.php';

// Sanitize the id from the URL to ensure it's a valid integer
$track_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch track data based on sanitized id
$qry = $conn->query("SELECT * FROM track WHERE id = $track_id");

if ($qry && $qry->num_rows > 0) {
    // Fetch the track data as an associative array
    $track = $qry->fetch_assoc();
    
    // Correct field mapping
    $date = $track['date'];
    $time = $track['time'];
    $destination = $track['destination'];
    $address = $track['address'];
    $purpose = $track['purpose'];
    $vehicle = $track['vehicle'];
    $driver = $track['driver'];
    $dept = $track['dept'];
} else {
    echo "Track not found.";
    exit;  // Exit if track is not found
}

include 'manage_track.php'; // Include the form or view for editing the track
?>