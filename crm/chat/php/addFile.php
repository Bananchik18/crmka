<?php
header("Access-Control-Allow-Origin: *");
include "../../configs/db.php";

session_start();
	// echo "string";
// var_dump($_POST['to_user_id']);
// var_dump($_POST['my_file_upload']);
if(isset($_POST['my_file_upload'])){
	$uploaddir = '../filemessage/';
	$files = $_FILES;
	$done_files = array();

	foreach ($files as $file) {
		$file_name = $file['name'];
		$last = pathinfo($file_name , PATHINFO_EXTENSION);
		$finish = $uploaddir . generateRandomString(10) .".". $last;
		if( move_uploaded_file( $file['tmp_name'], "$finish"  ) ){
			$finish = "http://".$_SERVER['SERVER_NAME']."/chat/chat/" . $finish;
			// var_dump($finish);
			if(isset($_POST['to_user_id'])){
				$sql = "INSERT INTO `chat_message` (`to_user_id`,`from_user_id`,`chat_message`,`timestamp`,`status`) 
					VALUES ('".$_POST['to_user_id']."','".$_SESSION['user_id']."','$finish',now(),'1')";
			}else if(isset($_POST['to_group_id'])){
				$sqlGroup = "SELECT * FROM `group_chats` WHERE `id`=".$_POST['to_group_id'];
				$resultGroup = $conn->query($sqlGroup);
				$rowGroup = $resultGroup->fetch_assoc();
				$sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, '".$_POST['to_group_id']."', '".$rowGroup['members_chat']."', '".$_SESSION['user_id']."', '".$finish."', '-1', now(), '1');";
			}

			$result = $conn->query($sql);
			// var_dump($sql);
			$lastIdGroup = mysqli_insert_id($conn);
		}
	}
	if(isset($_POST['to_user_id'])){
		$masArray = array("id_messsgae"=>$lastIdGroup,"src"=>$finish,"from_user_id"=>$_SESSION['user_id'],"to_user_id"=>$_POST['to_user_id'],"typeFile"=>$last);
	}else if(isset($_POST['to_group_id'])){
		$masArray = array("id_messsgae"=>$lastIdGroup,"src"=>$finish,"from_user_id"=>$_SESSION['user_id'],"to_group_id"=>$_POST['to_group_id'],"typeFile"=>$last);
	}

	echo json_encode($masArray);

}


function generateRandomString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';

	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}

	return $randomString;
}
