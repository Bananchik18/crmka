<?php

	include "../../configs/db.php";
	
	session_start();

	$sql = "SELECT * FROM `group_chats` WHERE id=" . $_POST['to_group_id'];

	$result = $conn->query($sql);

	$id_group = $result->fetch_assoc()['members_chat'];

	$sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `timestamp`, `status`) VALUES (NULL, '".$_POST['to_group_id']."', '".$id_group."', '".$_SESSION['user_id']."', '".$_POST['chat_message_group']."', current_timestamp(), '1')";

	$result = $conn->query($sql);

	if($result){
		echo fetch_group_chat_history($_SESSION['user_id'],$_POST['to_group_id'],$conn);
	}
