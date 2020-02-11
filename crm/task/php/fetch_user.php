<?php
	include "../../configs/db.php";

	if($_POST['fetch_user']){
		getUser($conn);
	}

	function getUser($conn){
		$sql = "SELECT * FROM `users`";
		$result = $conn->query($sql);
		$output = '';
		foreach ($result as $row) {
			$output .= '<option id_user='.$row['id'].'>'.$row['username'].' '.$row['lastname'].'</option>';
		}
		echo $output;
	}