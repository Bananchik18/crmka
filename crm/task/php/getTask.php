<?php
	include "../../configs/db.php";

	if(isset($_POST['getTask'])){
		getTask($conn,$_POST['id_user']);
	}



	function getTask($conn,$executor){
		$sql = "SELECT * FROM `task`,`users` WHERE `executor`= $executor AND `task`.`appointed` = `users`.`id`";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
			$response = [];
			while($row = $result->fetch_assoc()){
				// var_dump($row);
				$item = [
					"id_task" => $row['id_task'],
					"appointed" => $row['appointed'],
					"executor" => $row['executor'],
					"info_task" => $row['info_task'],
					"importance" => $row['importance'],
					"status" => $row['status'],
					"start_date" => $row['start_date'],
					"end_date" => $row['end_date'],
					"username" => $row['username'],
					"lastname" => $row['lastname']
				];
				$response[] = $item;
			}
			echo json_encode(["task" => $response]);
		}else{
			echo json_encode(["status" => "not found"]);
		}

	}
?>