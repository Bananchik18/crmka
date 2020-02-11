<?php
	include "../../configs/db.php";

	session_start();


	$sql = "UPDATE `login_details` SET `last_activity` = now() WHERE `login_details`.`user_id` = " . $_SESSION['login_details_id'];

	$conn->query($sql);

	// echo $sql;