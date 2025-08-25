<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$isbn = $_POST['isbn'];
		$title = $_POST['title'];
		$category = $_POST['category'];
		$author = $_POST['author'];
		$equip_qty = $_POST['equip_qty'];
		$publisher = $_POST['publisher'];
		$pub_date = $_POST['pub_date'];

		$sql = "UPDATE books SET isbn = '$isbn', title = '$title', category_id = '$category', author = '$author', equip_qty = '$equip_qty', publisher = '$publisher', publish_date = '$pub_date' WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Equipment updated successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	header('location:book.php');

?>