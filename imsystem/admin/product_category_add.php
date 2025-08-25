<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $inventory_selection = $_POST['inventory_selection'];
    $name = $_POST['name'];
    
    $sql = "SELECT * FROM company_name WHERE company_name = '$name' AND inventory_selection = '$inventory_selection'";
    $query = $conn->query($sql);
    
    if($query->num_rows > 0){
        $_SESSION['error'] = ['Category already exists'];
    }
    else{
        $sql = "INSERT INTO company_name (inventory_selection, company_name) VALUES ('$inventory_selection', '$name')";
        if($conn->query($sql)){
            $_SESSION['success'] = 'Category added successfully';
        }
        else{
            $_SESSION['error'] = ['Error adding category: ' . $conn->error];
        }
    }
}

header('location: product_category.php');
?>