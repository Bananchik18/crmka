<?php

	include "../configs/db.php";
	session_start();
	// var_dump($_SESSION['user_id']);
	if(!isset($_SESSION['user_id'])){
		header("Location: ../index.php");
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
	<link rel="stylesheet" href="css/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
</head>
<body>
	<?php
		include "../panel/index.php";
	?>
	<div id="listTask">
		<ul>
			<li id="showTask"><p>Задачи</p></li>
		</ul>
	</div>
	<div id="information_task">
		<button id="createNewTask">Добавить новую задачу</button>
		<div id="formNewTask">
			<select name="" id="selectName"></select>
			<textarea name="" id="" cols="30" rows="10"></textarea>	
			<select name="" id=""></select>
			<input type="date">
			<input type="date">
			<button>Создать задачу</button>
		</div>
		<ul>
			
		</ul>
	</div>
	<div id="calendar"></div>
	<!-- <script src="js/getTask.js"></script> -->
</body>
</html>