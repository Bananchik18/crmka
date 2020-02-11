<?php

	include "../../configs/db.php";
	session_start();
	$cokie = $_SESSION['user_id'];
	$sql = "SELECT * FROM `users` WHERE `id` != $cokie";

	$result = $conn->query($sql);

	$output = '<ul>';

	foreach($result as $row){
		$output .='<li><h6>' . $row['username'] . 
		'</h6><input type="checkbox" id="box-'.$row['id'].'createGroup"><label class="btn selectUserGroup" data-select-user="'.$row['id'].'" for="box-'.$row['id'].'createGroup"></label></li>';
	}

	$output .= "</ul>";

	echo $output;