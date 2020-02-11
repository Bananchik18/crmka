<?php

session_start();
include "../../configs/db.php";


if(isset($_POST['fetchName'])){
	$sql = "SELECT * FROM `users` WHERE `id`=".$_POST['to_user']." || `id`=".$_SESSION['user_id'];
	$result = $conn->query($sql);

	$output = '<option value="all">Все</option>';
	foreach($result as $row){
		$output .= "<option value='".$row['id']."'>".$row['username']."</option>";
	}
	echo  $output;
}else if(isset($_POST['selectName'])){
		// var_dump($_POST['to_user']);

	if($_POST['val'] == "all"){
				$sql = " SELECT * FROM chat_message 
				WHERE (from_user_id = '".$_SESSION['user_id']."' 
				AND to_user_id = '".$_POST['to_user']."') 
				OR (from_user_id = '".$_POST['to_user']."' 
				AND to_user_id = '".$_SESSION['user_id']."') 
				ORDER BY timestamp ASC";
	}else if($_POST['val'] != $_SESSION['user_id']){
		$sql = "SELECT * FROM `chat_message` WHERE `from_user_id`=".$_POST['val']." AND `to_user_id`=".$_SESSION['user_id'];
	}else{
		$sql = "SELECT * FROM `chat_message` WHERE `from_user_id`=".$_SESSION['user_id']." AND `to_user_id`='".$_POST['to_user']." '";
	}
		// var_dump($sql);
	$result = $conn->query($sql);

	if($result->num_rows > 0){
		// var_dump($sql);
$output = '<ul class="list-unstyled">';
	foreach($result as $row){
		$user_name = '';
		if($row["from_user_id"] == $_SESSION['user_id']){
			$user_name = '<b class="text-success">'.get_user_name($_SESSION['user_id'], $conn).'</b>';
			$btnDeleteMessage = '<button class="deleteMessage" data-id-message="'.$row['chat_message_id'].'">&times;</button>';
			if($row['forward'] != "-1"){
			// var_dump($row['id_chat_group']);

				$sqlForvardText = "SELECT * FROM `forward_message` WHERE `id`=".$row['forward'];
				$resultForvardText = $conn->query($sqlForvardText);
				$rowForvardText = $resultForvardText->fetch_assoc();
				$sqlNameForvard = "SELECT * FROM `users` WHERE `id`=".$rowForvardText['from_user_id'];
				$resultNameForward = $conn->query($sqlNameForvard);
				if($resultNameForward->num_rows > 0){

					$rowNameForvar = $resultNameForward->fetch_assoc();

				}
			// var_dump($rowNameForvar);
				$output .= '
				<li>
				<p id-message="'.$row['chat_message_id'].'" id="messageUserForward"><span>'.checkTypeFile($row['chat_message']).'</span>
				<br>Переслано от '.$rowNameForvar['username'].'<br>'.checkTypeFile($rowForvardText['forward_text']).'
				<div align="right">
				<small><em>'.$row['timestamp'].'</em></small>
				</div>
				</p>
				</li>';

			}else{
				$output .= '
				<li >
				<p id-message="'.$row['chat_message_id'].'" id="messageUser"><span>'.checkTypeFile($row['chat_message']).'</span>
				<div align="right">
				<small><em>'.$row['timestamp'].'</em></small>
				</div>
				</p>
				</li>';

			}
		}else{
			$user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $conn).'</b>';
			$btnDeleteMessage = '';
			if($row['forward'] != "-1"){
			// var_dump($row['id_chat_group']);

				$sqlForvardText = "SELECT * FROM `forward_message` WHERE `id`=".$row['forward'];
				$resultForvardText = $conn->query($sqlForvardText);
				$rowForvardText = $resultForvardText->fetch_assoc();
				$sqlNameForvard = "SELECT * FROM `users` WHERE `id`=".$rowForvardText['from_user_id'];
				$resultNameForward = $conn->query($sqlNameForvard);
				if($resultNameForward->num_rows > 0){
					$rowNameForvar = $resultNameForward->fetch_assoc();

				}
			// var_dump($rowNameForvar);
				$output .= '
				<li>
				<p id-message="'.$row['chat_message_id'].'"><span>'.checkTypeFile($row['chat_message']).'</span>
				<br>Переслано от '.$rowNameForvar['username'].'<br>'.checkTypeFile($rowForvardText['forward_text']).'
				<div>
				<small><em>'.$row['timestamp'].'</em></small>
				</div>
				</p>
				</li>';

			}else{
				$output .= '
				<li>
				<p id-message="'.$row['chat_message_id'].'"><span>'.checkTypeFile($row['chat_message']).'</span>
				<div>
				<small><em>'.$row['timestamp'].'</em></small>
				</div>
				</p>
				</li>';

			}
		}

		$optionBtnMessage = $btnDeleteMessage;

	}
	$output .= '</ul>';
		echo $output;
	}
}

