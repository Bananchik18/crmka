<?php
	include "../../configs/db.php";
	
	session_start();



	if(empty($_POST['forwad']) && empty($_POST['fieldForForwardOpen']) && empty($_POST['fieldForForwardText'])){
		$data = array(
			":to_user_id" => $_POST['to_user_id'],
			":from_user_id" => $_SESSION['user_id'],
			":chat_message" => $_POST['chat_message'],
			":status" => '1'	
		);

		$sql = "INSERT INTO `chat_message` (`chat_message_id`, `to_user_id`, `from_user_id`, `chat_message`, `timestamp`, `status`) VALUES (NULL, '".$data[':to_user_id']."', '".$data[':from_user_id']."', '".$data[':chat_message']."', now(), '".$data[':status']."')";
		// var_dump($sql);
		$result = $conn->query($sql);
		
		if($result){
			echo fetch_user_chat_history($_SESSION['user_id'], $_POST['to_user_id'], $conn);
		}
	}else if(isset($_POST['forwad'])){
		$sqlGetMessage = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$_POST['id_message'];
		
		$resultGetMessage = $conn->query($sqlGetMessage);
		$rowGetMessage = $resultGetMessage->fetch_assoc();
		
		$sqlToTableForward = "INSERT INTO `forward_message` (`id`,`from_user_id`,`to_user_id`,`forward_text`)
								VALUES (NULL,'".$_SESSION['user_id']."','".$rowGetMessage['from_user_id']."','".$rowGetMessage['chat_message']."')";

		$resultToTableForward = $conn->query($sqlToTableForward);
		$lastIdTableForward = mysqli_insert_id($conn);

		$sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, NULL, '".$_POST['to_user_id']."', '".$_SESSION['user_id']."', '".$_POST['chat_message']."', '".$lastIdTableForward."', now(), '1');";
		$result = $conn->query($sql);
		if($result){
			echo fetch_user_chat_history($_SESSION['user_id'], $_POST['to_user_id'], $conn);
		}
	}else if(isset($_POST['fieldForForwardOpen'])){
		$sqlUser = "SELECT * FROM `users` WHERE `id` != ".$_SESSION['user_id']."; ";
		$sqlGroup = "SELECT * FROM `group_chats` WHERE `members_chat` LIKE '%".$_SESSION['user_id']."%';";
		$resultUser = $conn->query($sqlUser);
		$resultGroup = $conn->query($sqlGroup);

		$output = '';

		foreach ($resultUser as $row) {
			$btnForvard = "<button id='sendForwardMessage' class='btn btn-primary' to_user_id='".$row['id']."'>Отправить</button>";
			$output .= "<li>".$row['username']." ".$row['lastname']." ".$btnForvard."</li>";
		}

		foreach ($resultGroup as $row) {
			$btnForvard = "<button id='sendForwardMessage' class='btn btn-primary' to_group_id='".$row['id']."'>Отправить</button>";
			$output .= "<li>".$row['name_chat']." ".$btnForvard."</li>";
		}

		echo $output;
	}else if(isset($_POST['fieldForForwardText']) && isset($_POST['textComentForward']) && isset($_POST['to_user_id'])){
		$text = $_POST['fieldForForwardText'];
		$comentText = $_POST['textComentForward'];
		$to_user_id = $_POST['to_user_id'];
		var_dump($text);
		// var_dump($comentText);
		$sqlGetMessage = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$_POST['id_message'];
		
		$resultGetMessage = $conn->query($sqlGetMessage);
		$rowGetMessage = $resultGetMessage->fetch_assoc();
		var_dump($sqlGetMessage);
		$sqlToForwardMessage = "INSERT INTO `forward_message` (`id`,`from_user_id`,`to_user_id`,`forward_text`)
								VALUES (NULL,'".$_SESSION['user_id']."','".$rowGetMessage['from_user_id']."','".$text."')";
		$resultToForwardMessage = $conn->query($sqlToForwardMessage);
		$lastIdTableForward = mysqli_insert_id($conn);

		$sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, NULL, '".$_POST['to_user_id']."', '".$_SESSION['user_id']."', '".$comentText."', '".$lastIdTableForward."', now(), '1');";
		$result = $conn->query($sql);
		if($result){

		}
	}