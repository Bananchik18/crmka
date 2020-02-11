<?php

	include "../../configs/db.php";
	
	session_start();

	echo fetch_group_chat_history($_SESSION['user_id'], $_POST['to_group_id'], $conn);