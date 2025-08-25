<?php
include 'includes/session.php';
include 'includes/db_connect.php'; // Adjust based on your DB connection file

if(isset($_POST['id']) && isset($_POST['price'])) {
    $id = $_POST['id'];
    $product_selling_price = $_POST['product_selling_price'];
    
    // Update the selling price in the database
    $sql = "UPDATE stock_master SET product_selling_price = ? WHERE id = ?"; // Adjust table/column names if needed
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $price, $id);
    
    if($stmt->execute()) {
        $_SESSION['success'] = 'Selling price updated successfully';
    } else {
        $_SESSION['error'] = 'Error updating price: ' . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    $_SESSION['error'] = 'Invalid request';
}

header('Location: view_stock.php');
exit();
?>