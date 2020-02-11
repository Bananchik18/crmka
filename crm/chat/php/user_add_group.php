<?php

include "../../configs/db.php";
session_start();


if(isset($_POST['members'])){
	$masOfName = [];

	$sql = "SELECT * FROM `users` WHERE `id` NOT IN (".$_POST['members'].")";
	
	$result = $conn->query($sql);

	$output = '<button id="closeWindowAddUserGroup"  to_group_id="'.$_POST['to_group_id'].'">Назад</button><ul>';

	foreach ($result as $row) {
		$output .= "<li id-user=".$row['id']."><h6>" . $row['username'] . "</h6><input type='checkbox' id='box-".$row['id']."addGroup'><label id='selectBtnNewUserGroup' id-user=".$row['id']." for='box-".$row['id']."addGroup'></label></li>";
		// <input type='checkbox' id-user=".$row['id']." id='selectBtnNewUserGroup'>
	}

	$output .= "</ul><button id='btnAddNewUserToGroup' to-group-id='".$_POST['to_group_id']."'>Добавить</button>";

	echo $output;
}

if(isset($_POST['to_group']) && isset($_POST['masNewUser'])){
	var_dump($_POST['masNewUser']);
	$sql = "SELECT `members_chat` FROM `group_chats` WHERE `id`=".$_POST['to_group'];
	$result = $conn->query($sql);
	$members = $result->fetch_assoc()['members_chat']; 
	$members .= "," . implode(",", $_POST['masNewUser']);
	$sql = "UPDATE `group_chats` SET `members_chat`='".$members."' WHERE `id`=" . $_POST['to_group'];
	$result = $conn->query($sql);
	
		// $output
}	
// 