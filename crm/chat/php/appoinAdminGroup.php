<?php
	include "../../configs/db.php";

	session_start();

	if(isset($_POST['to_group_id'])){

		$sql = "SELECT `members_chat` FROM `group_chats` WHERE `id`=" . $_POST['to_group_id'];

		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		// var_dump($row['members_chat']);

		$masMembers = [];

		$aloneMembers = explode(",", $row['members_chat']);
		$output = '<ul>';

		for($i=0; $i<count($aloneMembers); $i++){
			// array_push($masMembers, $aloneMembers[$i]);
			$sql = "SELECT * FROM `users` WHERE `id`=" . $aloneMembers[$i];
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			// array_push($masMembers, $row['username']);
			$output .= '<li>' . $row['username'] . '<button class="btn btn-primary newadmingroup" id-user='.$row['id'].' to_group_id="'.$_POST['to_group_id'].'">Выбрать</button></li>';
		}

		$output .= '</ul>';

		echo $output;
	}