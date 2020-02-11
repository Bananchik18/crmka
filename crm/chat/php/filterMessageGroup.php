<?php

session_start();
include "../../configs/db.php";


if(isset($_POST['fetchName'])){
	// $sql = "SELECT * FROM `users` WHERE `id`=".$_POST['to_user']." || `id`=".$_SESSION['user_id'];
	// $result = $conn->query($sql);
	$sql  = "SELECT * FROM `group_chats` WHERE `id`=".$_POST['to_group_id'];
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();

	$nameid = explode(",",$row['members_chat']);


	$output = '';
	for ($i=0; $i < count($nameid); $i++) { 
		$sql = "SELECT * FROM `users` WHERE `id`=".$nameid[$i];
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$output .= "<option value='".$row['id']."'>".$row['username']."</option>";

	}
	echo  $output;
}
else if(isset($_POST['selectName'])){
	if($_POST['val'] != $_SESSION['user_id']){
		$sql = "SELECT * FROM `chat_message` WHERE `from_user_id`=".$_POST['val']." AND id_chat_group=".$_POST['to_group_id'];
	}else{
		$sql = "SELECT * FROM `chat_message` WHERE `from_user_id`=".$_SESSION['user_id']." AND id_chat_group=".$_POST['to_group_id'];;
	}
		// var_dump($sql);
	$result = $conn->query($sql);

	if($result->num_rows > 0){
		// var_dump($sql);
	$output = '<ul class="list-unstyled">';
	foreach($result as $row){
		$user_name = '';
		if($row["from_user_id"] == $_SESSION['user_id']){
			$user_name = '<b>'.get_user_name($_SESSION['user_id'], $conn).'</b>';
			$btnDeleteMessage = '<button class="deleteMessage" data-id-message="'.$row['chat_message_id'].'">&times;</button>';
			if($row['forward'] != "-1"){
				$sqlForvardText = "SELECT * FROM `forward_message` WHERE `id`=".$row['forward'];
				$resultForvardText = $conn->query($sqlForvardText);
				$rowForvardText = $resultForvardText->fetch_assoc();
				$sqlNameForvard = "SELECT * FROM `users` WHERE `id`=".$rowForvardText['from_user_id'];
				$resultNameForward = $conn->query($sqlNameForvard);
				if($resultNameForward->num_rows > 0){
					$rowNameForvar = $resultNameForward->fetch_assoc();
				}
				$output .= '
				<li>
				<p id-message="'.$row['chat_message_id'].'" id="messageUserForward">'.$user_name.'<br><span>'.checkTypeFile($row['chat_message']).'</span>
				<br>Переслано от '.$rowNameForvar['username'].'<br>'.checkTypeFile($rowForvardText['forward_text']).'
				<div align="right">
				<small><em>'.$row['timestamp'].'</em></small>
				</div>
				</p>
				</li>';
			}else{
				$output .= '
				<li>
				<p id-message="'.$row['chat_message_id'].'" id="messageUser">'.$user_name.'<br><span>'.checkTypeFile($row['chat_message']).'</span>
				<div align="right">
				<small><em>'.$row['timestamp'].'</em></small>
				</div>
				</p>
				</li>';
			}
		}else{
			$user_name = '<b>'.get_user_name($row['from_user_id'], $conn).'</b>';
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
				<p id-message="'.$row['chat_message_id'].'">'.$user_name.'<br><span>'.checkTypeFile($row['chat_message']).'</span>
				<br>Переслано от '.$rowNameForvar['username'].'<br>'.checkTypeFile($rowForvardText['forward_text']).'
				<div>
				<small><em>'.$row['timestamp'].'</em></small>
				</div>
				</p>
				</li>';

			}else{
				$output .= '
				<li>
				<p id-message="'.$row['chat_message_id'].'">'.$user_name.'<br><span>'.checkTypeFile($row['chat_message']).'</span>
				<div>
				<small><em>'.$row['timestamp'].'</em></small>
				</div>
				</p>
				</li>';

			}
		}
	}
		$output .= "</ul>";
		echo $output;
	}
}
