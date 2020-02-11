<?php

	include "../../../configs/db.php";
  	include "showTaskCalendar.php";
  	include "showTaskCalendarNotice.php";

	session_start();
	
	if(isset($_POST['taskText']) && isset($_POST['timeToNiticeTask'])){
		// var_dump($_POST['taskText']);
		// var_dump($_POST['timeToNiticeTask']);
		// var_dump($_POST['toDate']);
		$toDateCorrect = $_POST['toDate'] . " ".$_POST['timeToNiticeTask'].":00";
		// var_dump($toDateCorrect);
		$sql = "INSERT INTO `calendar_notification` (`id`, `to_user_id`, `from_user_id`, `task`, `timestamp`, `task_time`) VALUES (NULL, '".$_SESSION['user_id']."', NULL, '".$_POST['taskText']."', current_timestamp(), '".$toDateCorrect."');";
		$result = $conn->query($sql);
       	$events = get_events($conn);
       	$events = get_json($events);
       	$events = json_encode($events);
      	echo $events;
  		
	}else if(isset($_POST['noticeMessage'])){
		// var_dump($_POST['idMessage']);
		// var_dump($_POST['timeNotice']);
		$sql = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$_POST['idMessage'];
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		// var_dump($row);
		$toDateCorrect = $_POST['toDate'] . " " . $_POST['timeNotice'].":00";
		// var_dump($toDateCorrect);
		$sql = "INSERT INTO `calendar_notification` (`id`, `to_user_id`, `from_user_id`, `task`, `timestamp`, `task_time`) VALUES (NULL, '".$_SESSION['user_id']."', NULL, '".$row['chat_message']."', current_timestamp(), '".$toDateCorrect."')";
		// var_dump($sql);
		$result = $conn->query($sql);
		$events_notice = get_events_notice($conn);
		$events_notice = get_json_notice($events_notice);
		$events_notice = json_encode($events_notice);
		echo $events_notice;
	}