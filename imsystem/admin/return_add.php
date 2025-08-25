<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $lastname = trim($_POST['lastname']);
    $project = trim($_POST['project']);
    $store = trim($_POST['store']);
    
    $sql = "SELECT * FROM users WHERE lastname = '$lastname'";
    $query = $conn->query($sql);

    if($query->num_rows < 1){
        if(!isset($_SESSION['error'])){
            $_SESSION['error'] = array();
        }
        $_SESSION['error'][] = 'User not found';
    } else {
        $row = $query->fetch_assoc();
        $user_id = $row['id'];
        $return_count = 0;

        foreach($_POST['title'] as $index => $title){
            if(!empty($title)){
                $quantity = $_POST['quantity'][$index];
                $sql = "SELECT * FROM books WHERE LOWER(TRIM(title)) = LOWER(TRIM('$title'))";
                $query = $conn->query($sql);

                if($query->num_rows > 0){
                    $book = $query->fetch_assoc();
                    $book_id = $book['id'];

                    // Modified SQL to get the latest borrow record that matches project and user
                    $borrow_sql = "SELECT * FROM borrow 
                                   WHERE user_id = '$user_id' 
                                   AND book_id = '$book_id' 
                                   AND status = 0 
                                   AND project = '$project'
                                   ORDER BY date_borrow DESC 
                                   LIMIT 1";
                    $borrow_query = $conn->query($borrow_sql);

                    if($borrow_query->num_rows > 0){
                        $borrow = $borrow_query->fetch_assoc();
                        $borrow_id = $borrow['id'];
                        $borrowed_quantity = $borrow['quantity'];

                        if ($quantity <= $borrowed_quantity) {
                            $insert_sql = "INSERT INTO returns (firstname, lastname, user_id, book_id, quantity, date_return, project, store) 
                                           VALUES ('$row[firstname]', '$row[lastname]', '$user_id', '$book_id', '$quantity', NOW(), '$project', '$store')";

                            if($conn->query($insert_sql)){
                                $return_count++;

                                // Update the book's available quantity
                                $update_book_sql = "UPDATE books SET equip_qty = equip_qty + $quantity WHERE id = '$book_id'";
                                $conn->query($update_book_sql);

                                // Update the borrow record's quantity
                                $remaining_quantity = $borrowed_quantity - $quantity;

                                if ($remaining_quantity == 0) {
                                    // Mark borrow record as returned
                                    $update_borrow_sql = "UPDATE borrow SET status = 1 WHERE id = '$borrow_id'";
                                } else {
                                    // Update borrow record with the remaining quantity
                                    $update_borrow_sql = "UPDATE borrow SET quantity = $remaining_quantity WHERE id = '$borrow_id'";
                                }
                                $conn->query($update_borrow_sql);
                            } else {
                                $_SESSION['error'][] = $conn->error;
                            }
                        } else {
                            $_SESSION['error'][] = "Return quantity for $title exceeds borrowed quantity.";
                        }
                    } else {
                        $_SESSION['error'][] = "Borrow details not found for Equipment: $title in project: $project";
                    }
                } else {
                    $_SESSION['error'][] = "Equipment not found: $title";
                }
            }
        }

        if($return_count > 0){
            $equipment = ($return_count == 1) ? 'Equipment' : 'Equipments';
            $_SESSION['success'] = "$return_count $equipment successfully returned.";
        }
    }
} else {
    $_SESSION['error'] = 'Fill up the form first.';
}

header('location: return.php');
?>