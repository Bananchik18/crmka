<?php

include "../../configs/db.php";
session_start();
$cokie = $_SESSION['user_id'];
//1. SELECT * FROM `chat_message` WHERE `to_user_id` = 1 AND `status` = 1 AND `id_chat_group` IS NULL  ORDER BY `timestamp` DESC 
//2  SELECT * FROM `chat_message` WHERE `from_user_id` = 1 ORDER BY `timestamp` DESC
//3. SELECT * FROM `users` WHERE `id` != $cokie


$masIdUser = [];

$sqlFirst = "SELECT * FROM `chat_message` WHERE `to_user_id` = ".$_SESSION['user_id']." AND `status` = 1 AND `id_chat_group` IS NULL  ORDER BY `timestamp` DESC";
$resultFirst = $conn->query($sqlFirst);
foreach ($resultFirst as $row) {
	array_push($masIdUser,$row['from_user_id']);
}

// $sqlSecond = "SELECT * FROM `chat_message` WHERE `from_user_id` = ".$_SESSION['user_id']." AND `id_chat_group` IS NULL ORDER BY `timestamp` DESC";
// $sqlSecond = "SELECT * FROM `chat_message` WHERE `from_user_id` = ".$_SESSION['user_id']." AND `to_user_id` != ".$_SESSION['user_id']." AND `id_chat_group` IS NULL ORDER BY `timestamp` DESC";
$sqlSecond = "SELECT * FROM `chat_message` WHERE `from_user_id` = ".$_SESSION['user_id']." AND `to_user_id` != ".$_SESSION['user_id']." AND `id_chat_group` IS NULL OR `from_user_id` != ".$_SESSION['user_id']." AND `to_user_id` = ".$_SESSION['user_id']." AND `id_chat_group` IS NULL ORDER BY `timestamp` DESC";
$resultSecond = $conn->query($sqlSecond);
foreach ($resultSecond as $row) {
	if($row['from_user_id'] == $_SESSION['user_id']){
		array_push($masIdUser,$row['to_user_id']);
	}else if($row['to_user_id'] == $_SESSION['user_id']){
		array_push($masIdUser,$row['from_user_id']);
	}
}

$sqlThird = "SELECT * FROM `users` WHERE `id` != $cokie";
$resultThird = $conn->query($sqlThird);
foreach ($resultThird as $row) {
	array_push($masIdUser,$row['id']);
}

// var_dump(array_unique($masIdUser));

$newarr = array_unique($masIdUser);

// $sql = "SELECT * FROM `users` WHERE `id` != $cokie";

// $result = $conn->query($sql);

$output = '
	<ul>
';

foreach($newarr as $row){
	// var_dump($row);
	// echo "<br>";
	$sql = "SELECT * FROM `users` WHERE `id`=" . $row;
	$result = $conn->query($sql);
	// var_dump($result->fetch_assoc()['username']);
	$rowName = $result->fetch_assoc();
	 $status = '';
	 
	 $current_timestamp = strtotime(date("Y-m-d H:i:s") . ' - 10 second');
	 $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);

	 $user_last_activity = fetch_user_last_activity($row, $conn);
	 
	 if($user_last_activity > $current_timestamp){
	  	$status = '<span class="badge badge-success status" style="margin-right:10px;"></span><p></p>';
	 }else{
  		$status = '<span class="badge badge-danger status" style="margin-right:10px;"></span><p></p>';
	 }


	$output .= '<li data-touserid="'.$rowName['id'].'" data-tousername="'.$rowName['username'].'" class="start_chat">'.$status.'<h6>'.$rowName['username'].' '.$rowName['lastname'].''.count_unseen_message($row, $_SESSION['user_id'], $conn).'</h6></li>';

}
$output .= '</ul>';
echo $output;