<?php
	include "../../configs/db.php";
	// include "fetch_group.php";
	session_start();

	$data = array(
		"membersChat" => $_POST['user'],
		"nameGroup" => $_POST['group_name'],
		"from_user_id" => $_SESSION['user_id'],	
	);
	array_push($data['membersChat'], $_SESSION['user_id']);	

	$members = implode(",",$data['membersChat']);
	// var_dump($members);
	
	$sql = "INSERT INTO `group_chats` (`id`, `name_chat`, `members_chat`, `creator_group`) VALUES (NULL, '".$data['nameGroup']."', '".$members."', '".$_SESSION['user_id']."')";
	// var_dump($sql);
	$result = $conn->query($sql);

	if($result){
		echo fetch_group($conn,$_SESSION['user_id']);
	}
	

