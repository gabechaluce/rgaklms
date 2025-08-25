<?php
include 'includes/session.php';

if(isset($_POST['key']) && isset($_POST['qty']) && isset($_POST['price'])) {
    $key = $_POST['key'];
    $new_qty = intval($_POST['qty']);
    $price = floatval($_POST['price']);

    if(isset($_SESSION['cart'][$key])) {
        // Update quantity and total
        $_SESSION['cart'][$key]['qty'] = $new_qty;
        $_SESSION['cart'][$key]['total'] = $new_qty * $price;
        echo 'success';
    } else {
        echo 'Item not found in cart';
    }
} else {
    echo 'Missing required parameters';
}
?>