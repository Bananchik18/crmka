<?php

	include "../../configs/db.php";

	session_start();
	


	$sql = "SELECT * FROM `chat_message` WHERE `chat_message_id` = " . $_POST['id_message'];
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$to_user = $row['to_user_id'];
	if(isset($_POST['id_message'])){
		$sql2 = "SELECT * FROM `chat_message` WHERE `chat_message`.`chat_message_id` = " . $_POST['id_message'];
		$result2 = $conn->query($sql2);
		$row = $result2->fetch_assoc();
		// var_dump($row['chat_message']);
		$x = explode("/",$row['chat_message']);
		var_dump($x[7]);
		unlink("../filemessage/".$x[7]);

		$sql = "DELETE FROM `chat_message` WHERE `chat_message`.`chat_message_id` = " . $_POST['id_message'];
		$result = $conn->query($sql);

		if($result){
			echo fetch_user_chat_history($_SESSION['user_id'], $to_user, $conn);
		}

	}