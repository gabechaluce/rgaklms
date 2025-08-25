<?php
session_start();
include 'includes/conn.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $query = $conn->query($sql);

    if ($query->num_rows < 1) {
        $_SESSION['error'] = 'Cannot find account with the username';
    } else {
        $row = $query->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            // Check if user has allowed login_type
            $login_type = $row['login_type'];

            // Only allow login_type 1, 4, or 6
            if ($login_type == 1 || $login_type == 4 || $login_type == 6) {
                $_SESSION['users'] = $row['id'];
                $_SESSION['login_type'] = $login_type;
                header('Location: dashboard.php'); // Change to your appropriate landing page
                exit();
            } else {
                $_SESSION['error'] = 'Access denied. You do not have permission to access this system.';
                header('Location: ../login.php');
                exit();
            }
        } else {
            $_SESSION['error'] = 'Incorrect password';
        }
    }
} else {
    $_SESSION['error'] = 'Input admin credentials first';
}

header('Location: ../login.php');
exit();
?>
