<?php

	include "../../configs/db.php";
	
	$sql = "UPDATE `users` SET `confirmation` = '0' WHERE `users`.`id` = " . $_GET['id'];

	$conn->query($sql);

	header("Location: index.php");