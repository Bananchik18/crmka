<?php
	
	include "../../configs/db.php";
	
	$sqlTime = "SELECT DATE_SUB(NOW(),Interval 6 MONTH)";
		$resultTime = $conn->query($sqlTime);
		$rowTime = $resultTime->fetch_assoc();
		$sqlDeleteMessage = "DELETE FROM `chat_message` WHERE `timestamp` < '" . $rowTime['DATE_SUB(NOW(),Interval 6 MONTH)'] . "'";
		// var_dump($sqlDeleteMessage);
		$resultaDeleteMessage = $conn->query($sqlDeleteMessage); 