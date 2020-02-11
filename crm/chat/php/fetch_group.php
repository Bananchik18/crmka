<?php
	include "../../configs/db.php";

	session_start();


	echo fetch_group($conn,$_SESSION['user_id']);