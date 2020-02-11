<?php

$servername = "localhost";
$username = "root";
$password = "eximlabcrm2020";
$dbname = "crm";


$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset('utf8');
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 

date_default_timezone_set('Europe/Kiev');
function fetch_user_last_activity($user_id, $connect){
	$sql = "
	SELECT * FROM login_details 
	WHERE user_id = '$user_id' 
	ORDER BY last_activity DESC 
	LIMIT 1
	";
	$result = $connect->query($sql);
	$row = $result->fetch_assoc();
	foreach($result as $row)
	{

		return $row['last_activity'];
	}
}

function fetch_user_chat_history($from_user_id, $to_user_id, $connect){
	$sql = " SELECT * FROM chat_message 
	WHERE (from_user_id = '".$from_user_id."' 
	AND to_user_id = '".$to_user_id."') 
	OR (from_user_id = '".$to_user_id."' 
	AND to_user_id = '".$from_user_id."') 
	ORDER BY `chat_message_id` ASC";
// var_dump($sql);
	$result = $connect->query($sql);

	$sqlCheckFixedMessage = "SELECT * FROM `fixed_message` WHERE `to_user_id`=".$to_user_id." AND `from_user_id`=".$from_user_id." OR `to_user_id`=".$from_user_id." AND `from_user_id`=".$to_user_id;
	$resultFixedMessage = $connect->query($sqlCheckFixedMessage);
	if($resultFixedMessage->num_rows > 0){
		$rowFixedMessage = $resultFixedMessage->fetch_assoc();
		if(strlen($rowFixedMessage['fixed_message']) > 30){
			$rowFixedMessage['fixed_message'] = mb_strimwidth($rowFixedMessage['fixed_message'], 0, 30, "...");
		};
		$fixedMessage = "<div id='fixedMessageLi' id-message=".$rowFixedMessage['message_id'].">Закрепленное сообщения <b>".$rowFixedMessage['fixed_message']."</b><button id='deleteFixed' id-fixed-message=".$rowFixedMessage['fixed_message_id'].">&times</button></div>";
		echo $fixedMessage;
	}



	$output = '<ul class="list-unstyled">';
	foreach($result as $row){
		$user_name = '';
		if($row["from_user_id"] == $from_user_id){
			$user_name = '<b class="text-success">'.get_user_name($_SESSION['user_id'], $connect).'</b>';
			$btnDeleteMessage = '<button class="deleteMessage" data-id-message="'.$row['chat_message_id'].'">&times;</button>';
			if($row['forward'] != "-1"){
			// var_dump($row['id_chat_group']);

				$sqlForvardText = "SELECT * FROM `forward_message` WHERE `id`=".$row['forward'];
				$resultForvardText = $connect->query($sqlForvardText);
				$rowForvardText = $resultForvardText->fetch_assoc();
				$sqlNameForvard = "SELECT * FROM `users` WHERE `id`=".$rowForvardText['from_user_id'];
				$resultNameForward = $connect->query($sqlNameForvard);
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
			$user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $connect).'</b>';
			$btnDeleteMessage = '';
			if($row['forward'] != "-1"){
			// var_dump($row['id_chat_group']);

				$sqlForvardText = "SELECT * FROM `forward_message` WHERE `id`=".$row['forward'];
				$resultForvardText = $connect->query($sqlForvardText);
				$rowForvardText = $resultForvardText->fetch_assoc();
				$sqlNameForvard = "SELECT * FROM `users` WHERE `id`=".$rowForvardText['from_user_id'];
				$resultNameForward = $connect->query($sqlNameForvard);
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

	$sql = "UPDATE chat_message SET status = '0' WHERE from_user_id = '".$to_user_id."' AND to_user_id = '".$from_user_id."' AND status = '1'";
	$result = $connect->query($sql);

	return $output;	
}

function get_user_name($user_id, $connect){
	$sql = "SELECT `username` FROM `users` WHERE `id` = '$user_id'";
	$result = $connect->query($sql);
	 // $result = $result->fetch_assoc();

	foreach($result as $row){
		return $row['username'];
	}
}

function count_unseen_message($from_user_id, $to_user_id, $connect){
	$sql = " SELECT * FROM chat_message WHERE from_user_id = '$from_user_id' AND to_user_id = '$to_user_id' AND status = '1'";

	$result = $connect->query($sql);
	$count = $result->num_rows;
	$output = '';
	if($count > 0){
		$output = '<span class="badge badge-success">'.$count.'</span>';
	}

	return $output;
}

function fetch_group_chat_history($from_user_id, $to_group_id, $connect){
	$sql = "SELECT * FROM `chat_message` WHERE `id_chat_group`=". $to_group_id . " ORDER BY `chat_message`.`timestamp` ASC";

	$result = $connect->query($sql);

	$sqlCheckFixedMessage = "SELECT * FROM `fixed_message` WHERE `id_chat_group`=".$to_group_id;
	$resultFixedMessage = $connect->query($sqlCheckFixedMessage);
	if($resultFixedMessage->num_rows > 0){
		$rowFixedMessage = $resultFixedMessage->fetch_assoc();
		$fixedMessage = "<div id='fixedMessageLi' id-message=".$rowFixedMessage['message_id'].">Закрепленное сообщения <b>".$rowFixedMessage['fixed_message']."</b><button id='deleteFixedGroup' id-fixed-message=".$rowFixedMessage['fixed_message_id'].">&times</button></div>";
		echo $fixedMessage;
	}

	$output = '<ul class="list-unstyled">';
	foreach($result as $row){
		$user_name = '';
		if($row["from_user_id"] == $from_user_id){
			$user_name = '<b>'.get_user_name($_SESSION['user_id'], $connect).'</b>';
			$btnDeleteMessage = '<button class="deleteMessage" data-id-message="'.$row['chat_message_id'].'">&times;</button>';
			if($row['forward'] != "-1"){
				$sqlForvardText = "SELECT * FROM `forward_message` WHERE `id`=".$row['forward'];
				$resultForvardText = $connect->query($sqlForvardText);
				$rowForvardText = $resultForvardText->fetch_assoc();
				$sqlNameForvard = "SELECT * FROM `users` WHERE `id`=".$rowForvardText['from_user_id'];
				$resultNameForward = $connect->query($sqlNameForvard);
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
			$user_name = '<b>'.get_user_name($row['from_user_id'], $connect).'</b>';
			$btnDeleteMessage = '';
			if($row['forward'] != "-1"){
			// var_dump($row['id_chat_group']);

				$sqlForvardText = "SELECT * FROM `forward_message` WHERE `id`=".$row['forward'];
				$resultForvardText = $connect->query($sqlForvardText);
				$rowForvardText = $resultForvardText->fetch_assoc();
				$sqlNameForvard = "SELECT * FROM `users` WHERE `id`=".$rowForvardText['from_user_id'];
				$resultNameForward = $connect->query($sqlNameForvard);
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
	$output .= '</ul>';

	return $output;

}

function fetch_group($connect, $user_id){
	$sql = "SELECT * FROM `group_chats` WHERE `members_chat` LIKE '%".$user_id."%' OR `creator_group` = ".$user_id;
	$result = $connect->query($sql);

	if($result->num_rows > 0){
		// $output = '<table class="table table-bordered table-striped">
		// <tr>
		// <td>Chat Name</td>
		// <td>Info Group</td>
		// <td>Action</td>
		// </tr>
		// ';
		$output = '<ul>';

		foreach ($result as $row) {
			// $output .= '<tr><td>'.$row['name_chat'].'</td><td><button type="button" class="btn btn-primary infoGroup" member-of-group="'.$row['members_chat'].'"data-group-chat="'.$row['id'].'">Info Group</button></td><td><button class="btn start_group_chat" data-group-chat="'.$row['id'].'"member-of-group="'.$row['members_chat'].'">Start Chat</button></td></tr>';
			$output .= '<li class="start_group_chat" data-group-chat="'.$row['id'].'" member-of-group="'.$row['members_chat'].'"><h6>'.$row['name_chat'].'<h6></li>';
		}

		$output .= '</ul>';

		echo $output;
	}
}

function createForChatForUser($touserid,$output){
	$form = '
	<div id="user_dialog_'.$touserid.'" class="user_dialog" >
	<input type="text" id="searchMessageUser" touserid="'.$touserid.'">
	<button id="showFile" to_user_id='.$touserid.'>File</button>
	<button id="showMessage" to_user_id="'.$touserid.'">Показать все сообщения</button>
	<select id="selectName" to_user_id='.$touserid.'></select>

	<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-touserid="'.$touserid.'" id="chat_history_'.$touserid.'">' . $output . '
	</div>
	<div class="blockQuotes"></div>
	<div class="form-group">
	<textarea name="chat_message_'.$touserid.'" id="chat_message_'.$touserid.'" class="form-control"></textarea>
	</div><div class="form-group" align="right">
	<button type="button" name="send_chat" id="'.$touserid.'" class="btn btn-info send_chat">Send</button>
	<label class="fileSend" to_user_id="'.$touserid.'"><input type="file" name="picture" multiple="multiple"/></label></div></div>';

	return $form;
}
function createForChatForGroup($to_group_id, $output){
	$formGroup = '<div id="group_dialog_'.$to_group_id.'" class="group_dialog">
	<input type="text" id="searchMessageGroup" togroupid="'.$to_group_id.'">
	<button id="showFile" to_group_id='.$to_group_id.'>File</button>
	<button id="showMessage" to_group_id='.$to_group_id.'>Показать все сообщения</button>
	<div style="height:400px; border:1px solid #ccc; overflow-y: scroll; margin-bottom:24px; padding:16px;" class="chat_history" data-togroupid-chat="'.$to_group_id.'" id="group_chat_history_'.$to_group_id.'">'.$output.'
	</div>
	<div class="blockQuotes"></div>
	<div class="form-group">
	<textarea name="group_chat_message_'.$to_group_id.'" id="group_chat_message_'.$to_group_id.'" class="form-control"></textarea>
	</div><div class="form-group" align="right">
	<button type="button" name="send_chat_group" id="'.$to_group_id.'" class="btn btn-info send_chat_group">Send</button></div></div>';
	return $formGroup;
}

function checkTypeFile($message){
	if(strpos($message,'../filemessage/')){
		$PathfileOnServer = explode("/", $message);
		$checkPath = "../filemessage/".$PathfileOnServer[7];

		if(file_exists($checkPath)){
			$buffer = file_get_contents($message);
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$typeFile = explode("/", $finfo->buffer($buffer));
	     		// var_dump($typeFile[0]);
			if($typeFile[0] == "image"){
				$message = '<img id="imgUser" src="'.$message.'">';
			}else if($typeFile[0] == "application"){
				$nameDoc =explode("/", $message);
				$message = "<a href='".$message."'>".$nameDoc[7]."</a>";
				$expansion = explode(".", $nameDoc[7]);
				// var_dump($expansion[1]);
				if($expansion[1] == "pdf"){
					// var_dump($nameDoc[7]);
					$message = "<img id='clickPdf' src='material/pdf.png' urlPdf='http://".$_SERVER['SERVER_NAME']."/chat/filemessage/$nameDoc[7]'>";
				}else if($expansion[1] == "docx"){
					$message = "<img id='clickWorld' src='material/doc.png' urlWord='http://".$_SERVER['SERVER_NAME']."/chat/filemessage/$nameDoc[7]'>";
				}else if($expansion[1] == "xlsx"){
					$message = "<img id='clickExel' src='material/xls.png' urlExel='http://".$_SERVER['SERVER_NAME']."/chat/filemessage/$nameDoc[7]'>";
				}
			}else if($typeFile[0] == "video"){
	     			// $message = "<a href='".$message."'>".$message."</a>";
				$message = "<img id='clickVideo' src='material/file.png' urlvideo='$message'>";
			}
	}else if(stristr($message,"https")){
		$message = "<a href='" . $message . "'>".$message."</a>";
	}else{
		$message = $message;
	}
}
	return $message;
}
