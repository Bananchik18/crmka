<?php

	include "../../configs/db.php";
	
	$sql = "UPDATE `users` SET `confirmation` = '1' WHERE `users`.`id` = " . $_GET['id'];

	$conn->query($sql);

	header("Location: index.php");