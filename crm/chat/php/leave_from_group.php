<?php

	include "../../configs/db.php";
	session_start();
	if(isset($_POST['to_group_id']) && empty($_POST['newadmin'])){
		$sql = "SELECT * FROM `group_chats` WHERE `creator_group`=" . $_SESSION['user_id'] . " AND `id`=" . $_POST['to_group_id'];

		$result = $conn->query($sql);

		if($result->num_rows > 0){
			$isAdmin = true;
		}else{
			$isAdmin = false;
			$sql = "SELECT * FROM `group_chats` WHERE `id`=" . $_POST['to_group_id'];
			$result = $conn->query($sql);
		}

		$row = $result->fetch_assoc();
		$memberGroup = $row['members_chat'];
		$members = explode(",",$memberGroup);
		$key = array_search($_SESSION['user_id'], $members);
		unset($members[$key]);
		$updateMembers = implode(",", $members);
		if($isAdmin){
			$sql = "DELETE FROM `group_chats` WHERE `id`=".$_POST['to_group_id']."; ";
			$sql2 = "DELETE FROM `chat_message` WHERE `id_chat_group`=" . $_POST['to_group_id'];
			$result2 = $conn->query($sql2);
		}else{
			$sql = "UPDATE `group_chats` SET `members_chat`='".$updateMembers."' WHERE `id`=".$_POST['to_group_id'];
		}

		$result = $conn->query($sql);
		
		if($result){
			echo fetch_group($conn,$_SESSION['user_id']);
		}
	}else if(isset($_POST['to_group_id']) && isset($_POST['newadmin'])){
		$sql = "UPDATE `group_chats` SET `creator_group`=".$_POST['newadmin']." WHERE `id`=".$_POST['to_group_id'];
		$result = $conn->query($sql);
		// var_dump($sql);
		$sql = "SELECT * FROM `group_chats` WHERE `id`=".$_POST['to_group_id'];
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();

		$members = explode(",",$row['members_chat']);
		$key  = array_search($_SESSION['user_id'], $members);
		unset($members[$key]);
		$updateMembers = implode(",", $members);

		$sql = "UPDATE `group_chats` SET `members_chat`='".$updateMembers."' WHERE `id`=".$_POST['to_group_id'];
		
		$result = $conn->query($sql);
		if($result){
			echo fetch_group($conn,$_SESSION['user_id']);
		}
	}