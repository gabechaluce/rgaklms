<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$isbn = $_POST['isbn'];
		$title = $_POST['title'];
		$category = $_POST['category'];
		$author = $_POST['author'];
		$equip_qty = $_POST['equip_qty'];
		$publisher = $_POST['publisher'];
		$pub_date = $_POST['pub_date'];

		$sql = "INSERT INTO books (isbn, category_id, title, author,equip_qty, publisher, publish_date) VALUES ('$isbn', '$category', '$title', '$author','$equip_qty', '$publisher', '$pub_date')";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Equipment added successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}	
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: book.php');

?>