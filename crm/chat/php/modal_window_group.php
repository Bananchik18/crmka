<?php

include "../../configs/db.php";
session_start();

$sql = "SELECT * FROM `group_chats` WHERE `creator_group`=".$_SESSION['user_id'] . " AND `id`=" . $_POST['to_group_id'];
$result = $conn->query($sql);
if($result->num_rows > 0){
	$isAdmin = 1;
}else{
	$btnDeleteMember = '';
	$isAdmin = 0;
}

$rowGroup = $result->fetch_assoc();


$members = explode(",",$rowGroup['members_chat']);
	
$nameMembers = [];
for ($i=0; $i < count($members); $i++) { 
	$sql = "SELECT * FROM `users` WHERE `id`=" . $members[$i];
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	array_push($nameMembers, $row);
	
}

$sql = "SELECT * FROM `group_chats` WHERE `id`=" . $_POST['to_group_id'];
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$output = '<button class="leaveFromGroup" data-group="'.$_POST['to_group_id'].'" isAdmin='.$isAdmin.'>Выйти</button>';
$output .= '<button  id="btnAddMemberGroup" members-of-group="'.$row['members_chat'].'" id-group="'.$_POST['to_group_id'].'">Добавить</button>';
$output .= '<ul>';

foreach ($nameMembers as $row) {
	if($isAdmin == 1){
		$output .= '<li><h6>' . $row['username'] . '</h6><button id="delete_user_group" id_user='.$row['id'].' id_group='.$_POST['to_group_id'].'>&times;</button></li>';
	}else{
		$output .= '<li>' . $row['username'] . '</li>';
	}
}

$output .= '</ul>';

echo $output;

