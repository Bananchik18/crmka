<?php

	session_start();
	include "../../configs/db.php";

	if(isset($_POST['createFixedMessage'])){
		// var_dump($_POST['id_message']);
		// var_dump($_POST['touserid']);

		$sql = "SELECT * FROM `fixed_message` WHERE `from_user_id`=".$_SESSION['user_id']." AND `to_user_id`=".$_POST['touserid']."
				OR `from_user_id`=".$_POST['touserid']." AND `to_user_id`=".$_SESSION['user_id'];

		$resultIssetRecord = $conn->query($sql);
		if($resultIssetRecord->num_rows > 0){
			$rowIssetRecord = $resultIssetRecord->fetch_assoc();
			$sqlGetMessage = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$_POST['id_message'];
			$resultGetMessage = $conn->query($sqlGetMessage);
			$rowGetMessage = $resultGetMessage->fetch_assoc();

			$sql = "UPDATE fixed_message SET to_user_id = '".$_POST['touserid']."', from_user_id = '".$_SESSION['user_id']."', fixed_message = '".$rowGetMessage['chat_message']."', timestamp = now(),message_id = ".$rowGetMessage['chat_message_id']." 
					WHERE fixed_message_id = ".$rowIssetRecord['fixed_message_id'].";";

			$result = $conn->query($sql);
		}else{
			$sql = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$_POST['id_message'];
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$sql = "INSERT INTO `fixed_message` (`fixed_message_id`,`message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `fixed_message`, `timestamp`) VALUES (NULL,".$row['chat_message_id'].", NULL, '".$_POST['touserid']."', '".$_SESSION['user_id']."', '".$row['chat_message']."', now())";
			$result = $conn->query($sql);
		}
		
		if($result){
			echo fetch_user_chat_history($_SESSION['user_id'],$_POST['touserid'],$conn);
		}
	}
	//удаление сообщения
	else if(isset($_POST['deleteFixed'])){
		// var_dump($_POST['idFixedMessage']);
		$sql = "SELECT * FROM `fixed_message` WHERE `fixed_message_id`=".$_POST['idFixedMessage'];
		$result = $conn->query($sql);
		$rowToUser = $result->fetch_assoc();
		$sql = "DELETE FROM `fixed_message` WHERE `fixed_message_id`=".$_POST['idFixedMessage'];
		$result = $conn->query($sql);
		if($result){
			echo fetch_user_chat_history($_SESSION['user_id'],$rowToUser['to_user_id'],$conn);
		}	
	}
