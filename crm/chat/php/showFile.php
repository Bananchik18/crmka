<?php

include "../../configs/db.php";
session_start();

if(isset($_POST['touserFile'])){
		// var_dump($_POST['to_user_id']);
			// $sql = "SELECT * FROM chat_message WHERE ((from_user_id = '".$_SESSION['user_id']."' AND to_user_id = '".$_POST['to_user_id']."') OR (from_user_id = '".$_POST['to_user_id']."' AND to_user_id = '".$_SESSION['user_id']."')) AND `chat_message` LIKE '%".$_SERVER['SERVER_NAME']."/%' ORDER BY timestamp ASC";
	$sql = "SELECT * FROM chat_message WHERE ((from_user_id = '".$_SESSION['user_id']."' AND to_user_id = '".$_POST['to_user_id']."') OR (from_user_id = '".$_POST['to_user_id']."' AND to_user_id = '".$_SESSION['user_id']."')) ORDER BY timestamp ASC";
			// var_dump($sql);
	$result = $conn->query($sql);
	$output = '<ul class="list-unstyled">';
	foreach ($result as $row) {
		$user_name = '';
		if($row["from_user_id"] == $_SESSION['user_id']){
			$user_name = '<b class="text-success">You</b>';
					// $btnDeleteMessage = '<button class="deleteMessage" data-id-message="'.$row['chat_message_id'].'">&times;</button>';
		}else{
			$user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $conn).'</b>';
					// $btnDeleteMessage = '';
		}

		if(stristr($row['chat_message'],$_SERVER['SERVER_NAME'])){
			$output .= '	
			<li style="border-bottom:1px dotted #ccc">
			<p id-message="'.$row['chat_message_id'].'">'.$user_name.' - '.checkTypeFile($row['chat_message']).'
			<div align="right">
			- <small><em>'.$row['timestamp'].'</em></small>
			</div>
			</p>
			</li>';
		}else if($row['forward'] != "-1"){
			$sqlForward = "SELECT * FROM `forward_message` WHERE `id`=".$row['forward'];
			$resultForward = $conn->query($sqlForward);
			$rowForward = $resultForward->fetch_assoc();
			// var_dump($rowForward);
			if(stristr($rowForward['forward_text'],$_SERVER['SERVER_NAME'])){
				$sqlNameForvard = "SELECT * FROM `users` WHERE `id`=".$rowForward['from_user_id'];
				$resultNameForward = $conn->query($sqlNameForvard);
				// if($resultForward->num_rows > 0){}
				$rowNameForvar = $resultNameForward->fetch_assoc();
				$output .= '
					<li>
					<p id-message="'.$row['chat_message_id'].'">'.$user_name.' - <span>'.checkTypeFile($row['chat_message']).'</span>
					<br>Пользователь - '.$rowNameForvar['username'].' написал - '.checkTypeFile($rowForward['forward_text']).'
					<div align="right">
					- <small><em>'.$row['timestamp'].'</em></small>
					</div>
					</p>
					</li>';
			}
		}
	}
	$output .= "</ul>";

	echo $output;
			// echo createForChatForUser($_POST['to_user_id'],$output);
}