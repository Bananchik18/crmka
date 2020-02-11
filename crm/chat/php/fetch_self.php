<?php 

	include "../../configs/db.php";

	session_start();

	if(isset($_POST['fetch_self'])){
		$sql = "SELECT * FROM `users` WHERE `id`=".$_SESSION['user_id'];
		$resultName = $conn->query($sql);
		$rowName = $resultName->fetch_assoc();

		$status = '';

		$current_timestamp = strtotime(date("Y-m-d H:i:s") . ' - 10 second');
		$current_timestamp = date('Y-m-d H:i:s', $current_timestamp);

		$user_last_activity = fetch_user_last_activity($rowName['id'], $conn);

		if($user_last_activity > $current_timestamp){
			$status = '<span class="badge badge-success status" style="margin-right:10px;"></span>';
		}else{
			$status = '<span class="badge badge-danger status" style="margin-right:10px;"></span>';
		}

		$output = $status.' <p>'.$rowName['username'].' '.$rowName['lastname'].'<p>';
		echo $output;

	}

?>