<?php
session_start();
session_destroy();
header("Location: ../login.php"); // Add ../ to go to parent directory
exit();
?>