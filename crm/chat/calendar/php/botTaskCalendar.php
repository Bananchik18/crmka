<?php

	session_start();
	include "../../../configs/db.php";
  	// include "showTaskCalendar.php";


	$sql = "SELECT * FROM `calendar_notification` WHERE `task_time` < now()";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();

	if($result->num_rows > 0){
		foreach ($result as $row) {

			$sqlCheckRepeat = "SELECT * FROM `message_bot_notification` WHERE `id_task`=".$row['id'];
			$resultCheckRepeat = $conn->query($sqlCheckRepeat);
			// var_dump($resultCheckRepeat->num_rows);
			if($resultCheckRepeat->num_rows <= 0){
			$sqlInsert = "INSERT INTO `message_bot_notification` (`id`,`id_task`, `to_user_id`, `from_user_id`, `text`) VALUES (NULL,'".$row['id']."', '".$_SESSION['user_id']."', NULL, '".$row['task']."');";
			$conn->query($sqlInsert);
			}
		}
	}

	$sqlSelectForMessageBot = "SELECT * FROM `message_bot_notification` WHERE `to_user_id`=".$_SESSION['user_id'];
	$resultForMessageBot = $conn->query($sqlSelectForMessageBot);

	$output = "";

	foreach ($resultForMessageBot as $row) {
		$output .= "<li>".$row['text']."</li>";
	}

	echo $output;