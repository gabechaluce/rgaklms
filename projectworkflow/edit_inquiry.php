<?php
include 'db_connect.php';

// Sanitize the id from the URL to ensure it's a valid integer
$inquiry_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fetch inquiry data based on sanitized id
$qry = $conn->query("SELECT * FROM inquiry_list WHERE id = $inquiry_id");

if ($qry && $qry->num_rows > 0) {
    // Fetch the inquiry data as an associative array
    $inquiry = $qry->fetch_assoc();
    
    // You can now use the array keys directly for easier access
    $name = $inquiry['name'];
    $contact = $inquiry['contact'];
    $business_name = $inquiry['business_name'];
    $inquiry_status = $inquiry['inquiry_status'];
    $quotation_status = $inquiry['quotation_status'];
    $description = $inquiry['description'];
} else {
    echo "Inquiry not found.";
    exit;  // Exit if inquiry is not found
}

include 'manage_inquiry.php'; // Include the form or view for editing the inquiry
?>
