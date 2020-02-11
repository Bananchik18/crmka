<?php

	include "../../configs/db.php";
	if(isset($_POST['open_chat'])){
		session_start();

		$sql = "SELECT * FROM `chat_message` WHERE `from_user_id` = ".$_SESSION['user_id']." ORDER BY `chat_message`.`timestamp` DESC LIMIT 1";
		// $sql = "SELECT * FROM `chat_message` WHERE `from_user_id` = ".$_SESSION['user_id']." OR `to_user_id` = ".$_SESSION['user_id']." AND `status` = 1 ORDER BY `chat_message`.`timestamp` DESC LIMIT 1";
		// var_dump($sql);
		// $sql = "SELECT * FROM `chat_message` WHERE `from_user_id` = ".$_SESSION['user_id']." AND `status` = 0 OR `to_user_id` = ".$_SESSION['user_id']." AND `status` = 0 ORDER BY `chat_message`.`timestamp` DESC LIMIT 1";
		// var_dump($sql);
		$result = $conn->query($sql);
		if($result->num_rows > 0){
			$row = $result->fetch_assoc();
			if($row['id_chat_group'] != NULL){
				$arr = array('id_group' => $row['id_chat_group'], 'chat_group'=>true);
				echo json_encode($arr);
			}else{
				if($row['to_user_id'] == $_SESSION['user_id']){
					$sql = "SELECT * FROM `users` WHERE `id`=".$row['from_user_id'];
				}else{
					$sql = "SELECT * FROM `users` WHERE `id`=".$row['to_user_id'];
				}

				$resultName = $conn->query($sql);
				$rowName = $resultName->fetch_assoc();
				$arr = array('id_user' => $rowName['id'], 'name_user' => $rowName['username'], 'chat_user' => true);
				echo json_encode($arr);
			}
		}else{
			echo json_encode(array('nothig'=>true));
		}
	}
?>