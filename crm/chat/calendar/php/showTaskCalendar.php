<?php

	function get_events($conn){

		$sql = "SELECT * FROM `calendar_notification` WHERE `to_user_id`=".$_SESSION['user_id'];
		$result = $conn->query($sql);
		return mysqli_fetch_all($result,MYSQLI_ASSOC);
	}

	function get_json($arr){
		$data = array();
		foreach ($arr as $row) {
			$data[] = array(
				"id"=>$row['id'],
				"to_user_id"=>$row['to_user_id'],
				"from_user_id"=>$row['from_user_id'],
				"title"=>$row['task'],
				"timestamp"=>$row['timestamp'],
				'start'=>$row['task_time']
			);
		}
		return $data;
	}
	

	function print_arr($arr){
		echo "<pre>".print_r($arr,true) . "</pre>";
	}

	function get_events_message($conn){

		$sql = "SELECT * FROM `calendar_notification` WHERE `to_user_id`=".$_SESSION['user_id'];
		$result = $conn->query($sql);
		return mysqli_fetch_all($result,MYSQLI_ASSOC);
	}
?>