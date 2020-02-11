<?php

include "../configs/db.php";

session_start();
// var_dump($_SESSION['user_id']);
if(!isset($_SESSION['user_id'])){
	header("Location: ../index.php");
}
// 		$user_id = $_COOKIE['user_id'];
// 		$sql = "SELECT * FROM `users` WHERE `id` = '$user_id'";
// 		// var_dump($sql);
// 		$result = $conn->query($sql);
// 		// var_dump($result);
// 		$row = $result->fetch_assoc();
// 		// var_dump($row);

// 		echo "Hello " . $row['username'];
// 		echo "<a href='../authorization/logout.php'>Logout</a>";
// }

?>

<html>  
<head>  
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/chat.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
</head>  
<body>  

	<!-- <div class="col-lg-12"> -->

<!-- 		<div id="menuPanelLeft">
			<a href="index.php"><div id="messenger"></div></a>
			<a href=""><div id="task"></div></a>
		</div> -->
		<?php
			include "../panel/index.php";
		?>
		</div>

		<div id="list">
			<div id="nameUser"></div>
			<div class="globalSearch">
				<input type="text" id="globalSearchInput" placeholder="люди, группи, сообщения">
				<button type="button" name="group_chat" id="group_chat" class="btn btn-warning btn-xs">Создать чат</button>
			</div>
			<div id="listUserGroup">
				<div id="user_details" style="width: 100%;float: left;"></div>
				<div id="user_group"><ul></ul></div>	 	
			</div>
			<div id="resultSearch" style="display: none;float: left;"></div>
		</div>

		<div id="group_chat_dialog" title="Выберите пользователей" style="display: none;">
			<input type="text" id="createGroupInput">
			<div id="listOfUsers"></div>
			<button class="btn" id="btnCreateGroup">Создать</button>
		</div>


		<div id="fieldForForward" style="display: none;">
			<!-- <p id="fieldForForwardText"></p> -->
			<input type="text" id="fieldForForwardInput">
			<div id="userGroupForwardSelect"></div>
		</div>
		<!-- </div> -->
		<!-- <div class="col-lg-12">		 -->
			<div id="user_chat" ></div>
			<div class="openModal">
				<div id="infoAboutGroup"></div>
			</div>

			<div id="openImg" style="display: none;">
				<img src="" alt="" id="openImg1">
				<button id="closeOpenImg">&times</button>
			</div>

			<div style="display: none;" id="openVideo">
				<video src="" height="400" width="auto" controls="controls"></video>
			</div>

			<div id="openApplication" style="display: none;">
				<iframe src="" frameborder="0"></iframe>
				<a href="">Download</a>
			</div>
			<?php 
			include "calendar/indexcalendar.php";
			?>
			<div id="fieldForNiticeMessage" style="display: none;">
			<?php
				include "calendar/calendarNotice.php";
			?>
			<script src="js/user.js"></script>
			<script src="js/group.js"></script>
			<script > 
				$(document).ready(function(){

					fetch_user();
					fetch_group();

				}); 
			</script>  

		</body>
		</html> 