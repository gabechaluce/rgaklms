<?php
include 'includes/session.php';

if(isset($_POST['item_id']) && isset($_SESSION['cart'][$_POST['item_id']])) {
    // Remove item from cart
    unset($_SESSION['cart'][$_POST['item_id']]);
    echo "success";
} else {
    echo "error";
}
?>