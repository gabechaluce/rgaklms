<?php
include_once("db_connect.php"); // Database connection

if (empty($_FILES['file'])) {
    echo json_encode(["error" => "No file uploaded"]);
    exit();
}

$temp = explode(".", $_FILES["file"]["name"]);
$newfilename = round(microtime(true)) . "." . end($temp);
$destinationFilePath = 'assets/uploads/' . $newfilename;

if (move_uploaded_file($_FILES['file']['tmp_name'], $destinationFilePath)) {
    // Store the file path in the database
    $stmt = $conn->prepare("INSERT INTO `project_list`(`files`) VALUES (?)");
    $stmt->bind_param("s", $destinationFilePath);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO `user_productivity`(`files`) VALUES (?)");
    $stmt->bind_param("s", $destinationFilePath);
    $stmt->execute();
    $stmt->close();
    // Return success response
    echo json_encode(["url" => $destinationFilePath]);
} else {
    echo json_encode(["error" => "File upload failed"]);
}
?>
