<?php
include 'includes/session.php';

if (isset($_POST['add'])) {
    $lastname = $_POST['lastname'];
    $project = $_POST['project'];
    $store = $_POST['store'];

    // Look up user by lastname
    $sql = "SELECT * FROM users WHERE lastname = '$lastname'";
    $query = $conn->query($sql);

    if ($query->num_rows < 1) {
        $_SESSION['error'][] = 'Employee not found';
    } else {
        $row = $query->fetch_assoc();
        $user_id = $row['id'];
        $firstname = $row['firstname'];

        $added = 0;

        // Process each equipment from title and quantity arrays
        foreach ($_POST['title'] as $index => $title) {
            if (!empty($title)) {
                // Get the quantity for the equipment - ALLOW ANY QUANTITY
                $total_quantity = intval($_POST['quantity'][$index]); // Convert to integer to handle large numbers

                // Use case-insensitive search and check for sufficient quantity (no upper limit)
                $sql = "SELECT * FROM books WHERE LOWER(TRIM(title)) = LOWER(TRIM('$title')) AND equip_qty >= '$total_quantity'";
                $query = $conn->query($sql);

                if ($query->num_rows > 0) {
                    $brow = $query->fetch_assoc();
                    $bid = $brow['id'];

                    // Insert borrow record
                    $sql = "INSERT INTO borrow (firstname, lastname, user_id, book_id, quantity, date_borrow, status, project, store) 
                            VALUES ('$firstname', '$lastname', '$user_id', '$bid', '$total_quantity', NOW(), 0, '$project', '$store')";
                    
                    if ($conn->query($sql)) {
                        $added++;
                        // Update the equipment quantity after borrowing
                        $sql = "UPDATE books SET equip_qty = equip_qty - $total_quantity WHERE id = '$bid'";
                        $conn->query($sql);
                    } else {
                        $_SESSION['error'][] = $conn->error;
                    }
                } else {
                    // Check what equipment exists for better error message
                    $check_sql = "SELECT title, equip_qty FROM books WHERE LOWER(TRIM(title)) LIKE LOWER(TRIM('%$title%'))";
                    $check_query = $conn->query($check_sql);
                    
                    if ($check_query->num_rows > 0) {
                        $check_row = $check_query->fetch_assoc();
                        $_SESSION['error'][] = "Equipment '$title' found but insufficient quantity. Available: " . $check_row['equip_qty'] . ", Requested: $total_quantity";
                    } else {
                        $_SESSION['error'][] = "Equipment '$title' not found in inventory";
                    }
                }
            }
        }

        // Success message after processing
        if ($added > 0) {
            $equipment = ($added == 1) ? 'Equipment' : 'Equipments';
            $_SESSION['success'] = $added . ' ' . $equipment . ' successfully borrowed';
        }
    }
} else {
    $_SESSION['error'][] = 'Fill up add form first';
}

header('location: borrow.php');
?>