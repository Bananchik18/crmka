<?php

include "../../configs/db.php";
session_start();


if(isset($_POST['userSearch'])){
	
	$sql = " SELECT * FROM chat_message 
	WHERE (from_user_id = '".$_SESSION['user_id']."' 
	AND to_user_id = '".$_POST['touserid']."') 
	OR (from_user_id = '".$_POST['touserid']."' 
	AND to_user_id = '".$_SESSION['user_id']."') 
	ORDER BY `chat_message_id` ASC";
	// var_dump($sql);
	$resultSearch = $conn->query($sql);
	$masForSearch = [];
	$output = '';
				$to_user_id;
	if($resultSearch->num_rows > 0){
		foreach ($resultSearch as $row) {
			$sqlNameUser = "SELECT * FROM `users` WHERE `id`=" . $row['from_user_id'];
			$resultName = $conn->query($sqlNameUser);
			$NameUser = $resultName->fetch_assoc()['username'];
			if(stristr($row['chat_message'],$_POST['value'])){
				if($row['to_user_id'] == $_SESSION['user_id']){
					$to_user_id = $row['from_user_id'];
				}else{
					$to_user_id = $row['to_user_id'];
				}
				$output .= "<div id='foundMessage' id_message='".$row['chat_message_id']."' to_user_id='".$to_user_id."'><p>".$NameUser."</p><b>".$row['chat_message']."</b><p>".$row['timestamp']."</p></div>";
			}else if($row['forward'] != "-1"){
				$sql = "SELECT * FROM `forward_message` WHERE `forward_text` LIKE '%".$_POST['value']."%'";
				// var_dump($sql);
				$resultForward = $conn->query($sql);
				$rowForward = $resultForward->fetch_assoc();
				if($row['to_user_id'] == $_SESSION['user_id']){
					$to_user_id = $row['from_user_id'];
				}else{
					$to_user_id = $row['to_user_id'];
				}
				$output .= "<div id='foundMessage' id_message='".$row['chat_message_id']."' to_user_id='".$to_user_id."'><p>".$NameUser."</p><b>".$row['chat_message']."</b><p>".$row['timestamp']."</p></div>";
			}
		}
		echo $output;

	}else{
		echo "Not Found";
	}
}else if(isset($_POST['valueInput'])){


	$sqlUser = "SELECT * FROM `users` WHERE `users`.`username` LIKE '%".$_POST['valueInput']."%' OR `users`.`lastname` LIKE '%".$_POST['valueInput']."%'";

	$sqlGroup = "SELECT * FROM `group_chats` WHERE  `name_chat` LIKE '%".$_POST['valueInput']."%'";
	$sqlMessage = " SELECT * FROM `chat_message` WHERE `chat_message` LIKE '%".$_POST['valueInput']."%' OR `forward` != -1";

	$output = '';
	$resultUser = $conn->query($sqlUser);
	if($resultUser->num_rows > 0){
		foreach ($resultUser as $row) {
			$sql = "SELECT * FROM `chat_message` WHERE (`to_user_id`='".$row['id']."' AND `from_user_id` = ".$_SESSION['user_id'].") OR (`to_user_id`=".$_SESSION['user_id']." AND `from_user_id`=".$row['id'].") ORDER BY `chat_message`.`chat_message_id` DESC LIMIT 1";
			$result = $conn->query($sql);
			$rowIdMessage = $result->fetch_assoc();
			$output  .= "<div id='foundUser' to_user_id='".$row['id']."' id_message='".$rowIdMessage['chat_message_id']."'><p>".$row['username']." ".$row['lastname']."<br><b>".$rowIdMessage['chat_message']."</b></div>";
		}
	}


	$resultGroup = $conn->query($sqlGroup);
	if($resultGroup->num_rows > 0){
		foreach ($resultGroup as $row) {
			$sql = "SELECT * FROM `chat_message` WHERE `id_chat_group`=".$row['id']." ORDER BY `chat_message`.`timestamp` DESC";
			$result = $conn->query($sql);
			$rowIdMessage = $result->fetch_assoc();

			$output .= "<div id='foundGroup' to_group_id=".$row['id']." id_message='".$rowIdMessage['chat_message_id']."'>".$row['name_chat']."<p>".$rowIdMessage['chat_message']."</p></div>";
		}
	}


	$resultMessage = $conn->query($sqlMessage);
	// var_dump($sqlMessage);
	if($resultMessage->num_rows > 0){

		foreach ($resultMessage as $row) {

			if($row['id_chat_group'] != NULL){
				$sql = "SELECT * FROM `group_chats` WHERE `id`=".$row['id_chat_group'];
				$result = $conn->query($sql);
				$rowIdMessage = $result->fetch_assoc();
				$output .= "<div id='foundGroup' to_group_id=".$rowIdMessage['id']." id_message='".$row['chat_message_id']."''>".$rowIdMessage['name_chat']."<p><b>".$row['chat_message']."</b></p></div>";
				if(stristr($row['chat_message'],$_POST['valueInput'])){
					$sqlNameUser = "SELECT * FROM `group_chats` WHERE `id`=" . $row['id_chat_group'];
					$resultName = $conn->query($sqlNameUser);
					$NameUser = $resultName->fetch_assoc()['name_chat'];
					if($_SESSION['user_id'] == $row['to_user_id']){
						$output .= "<div id='foundMessageGroup' id_message='".$row['chat_message_id']."' to_group_id='".$row['id_chat_group']."'><p>".$NameUser."</p><b>".$row['chat_message']."</b><p>".$row['timestamp']."</p></div>";
					}else{
						$output .= "<div id='foundMessageGroup' id_message='".$row['chat_message_id']."' to_group_id='".$row['id_chat_group']."'><p>".$NameUser."</p><b>".$row['chat_message']."</b><p>".$row['timestamp']."</p></div>";
					}

				}else if($row['forward'] != "-1"){
					$sql = "SELECT * FROM `forward_message` WHERE `forward_text` LIKE '%".$_POST['valueInput']."%'";
					$resultForward = $conn->query($sql);
					$rowForward = $resultForward->fetch_assoc();
					if(stristr($rowForward['forward_text'],$_POST['valueInput'])){
						$output .= "<div id='foundMessageGroup' id_message='".$row['chat_message_id']."' to_group_id='".$row['id_chat_group']."'><p>".$NameUser."</p><b>".$rowForward['forward_text']."</b><p>".$row['timestamp']."</p></div>";
					}
				}

			}else{
				if($_SESSION['user_id'] == $row['to_user_id']){
					$sqlNameUser = "SELECT * FROM `users` WHERE `id`=" . $row['from_user_id'];
				}else{
					$sqlNameUser = "SELECT * FROM `users` WHERE `id`=" . $row['to_user_id'];
				}
				$resultName = $conn->query($sqlNameUser);
				$NameUser = $resultName->fetch_assoc()['username'];
				if(stristr($row['chat_message'],$_POST['valueInput'])){
					
					if($_SESSION['user_id'] == $row['to_user_id']){
						$output .= "<div id='foundMessage' id_message='".$row['chat_message_id']."' to_user_id='".$row['from_user_id']."'><p>".$NameUser."</p><b>".$row['chat_message']."</b><p>".$row['timestamp']."</p></div>";
					}else{
						$output .= "<div id='foundMessage' id_message='".$row['chat_message_id']."' to_user_id='".$row['to_user_id']."'><p>".$NameUser."</p><b>".$row['chat_message']."</b><p>".$row['timestamp']."</p></div>";
					}

				}else if($row['forward'] != "-1"){
					// var_dump($row['forward']);
					$sql = "SELECT * FROM `forward_message` WHERE `forward_text` LIKE '%".$_POST['valueInput']."%'";
					$resultForward = $conn->query($sql);
					if($resultForward->num_rows > 0){				
						$rowForward = $resultForward->fetch_assoc();
						if(stristr($rowForward['forward_text'],$_POST['valueInput'])){
							// var_dump($rowForward['id']);
							// var_dump("----------");
							// var_dump($rowForward['forward_text']);

							$output .= "<div id='foundMessage' id_message='".$row['chat_message_id']."' to_user_id='".$row['from_user_id']."'><p>".$NameUser."</p><b>".$rowForward['forward_text']."</b><p>".$row['timestamp']."</p></div>";
						}
					}
				}

				// if($_SESSION['user_id'] == $row['to_user_id']){
				// 	// var_dump($row['to_user_id']);
				// 	$sql = "SELECT * FROM `users` WHERE `id`=".$row['from_user_id'];
				// 	var_dump($sql);
				// 	$result = $conn->query($sql);
				// 	$rowName = $result->fetch_assoc();
				// 	$output .= "<div id='foundMessage' to_user_id=".$row['from_user_id']." id_message=".$row['chat_message_id']." style='background:orange;'>".$rowName['username']."<br>".$row['chat_message']."".$row['id_chat_group']."</div>";

				// }else{
				// 	$sql = "SELECT * FROM `users` WHERE `id`=".$_SESSION['user_id'];
				// 	$result = $conn->query($sql);
				// 	$rowName = $result->fetch_assoc();
				// 	$output .= "<div id='foundMessage' to_user_id=".$row['to_user_id']." id_message=".$row['chat_message_id']." style='background:orange;'>".$rowName['username']."<br>".$row['chat_message']."".$row['id_chat_group']."</div>";
				// }
			}
		}
	}
	echo $output;

}else if($_POST['groupSearchList']){
	$sql = " SELECT * FROM chat_message WHERE `id_chat_group`=".$_POST['to_group_id'];
	$resultSearch = $conn->query($sql);
	$masForSearch = [];
	$output = '';

	if($resultSearch->num_rows > 0){
		foreach ($resultSearch as $row) {
			$sqlNameUser = "SELECT * FROM `users` WHERE `id`=" . $row['from_user_id'];
			$resultName = $conn->query($sqlNameUser);
			$NameUser = $resultName->fetch_assoc()['username'];
			if(stristr($row['chat_message'],$_POST['value'])){
		
				$output .= "<div id='foundMessageGroup' id_message='".$row['chat_message_id']."' to_group_id='".$_POST['to_group_id']."'><p>".$NameUser."</p><b>".$row['chat_message']."</b><p>".$row['timestamp']."</p></div>";
			}else if($row['forward'] != "-1"){
				$sql = "SELECT * FROM `forward_message` WHERE `forward_text` LIKE '%".$_POST['value']."%'";
				$resultForward = $conn->query($sql);
				if($resultForward->num_rows > 0){
					$rowForward = $resultForward->fetch_assoc();
					// var_dump($row['chat_message_id']);

					$output .= "<div id='foundMessageGroup' id_message='".$row['chat_message_id']."' to_group_id='".$_POST['to_group_id']."'><p>".$NameUser."</p><b>".$row['chat_message']."</b><p>".$row['timestamp']."</p></div>";
				}
			}
		}
		echo $output;

	}else{
		echo "Not Found";
	}	
}

