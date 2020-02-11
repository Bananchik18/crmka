<?php
include "../configs/db.php";

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['lastname'])){
	session_start();
	$draftUsername = $_POST['username'];
	$draftLastName = $_POST['lastname'];
	$draftPassword = md5($_POST['password']);
	$sql = "SELECT * FROM `users` WHERE username='$draftUsername' AND lastname='$draftLastName' AND password='$draftPassword'";
	$result = $conn->query($sql);

	if($result->num_rows > 0){
		$row = $result->fetch_assoc();
		if($row['confirmation'] == 0){
			echo("Ваш аккаунт ещё не подтвердили");
		}else{
			
			$_SESSION['user_id'] = $row['id'];

			$user_id = $_SESSION['user_id'];
			$sql = "SELECT * FROM `login_details` WHERE `user_id`=" . $row['id'] . " ORDER BY last_activity DESC 
			LIMIT 1";
			$result = $conn->query($sql);
			$result->fetch_assoc();
			if($result->num_rows > 0){
					// UPDATE `login_details` SET `last_activity` = '2019-11-26 09:24:32' WHERE `login_details`.`login_details_id` = 41;
				$sql = "UPDATE `login_details` SET `last_activity` = NOW() WHERE `login_details`.`user_id`=". $row['id'];
			}else{
				$sql = "INSERT INTO `login_details` (`user_id`) VALUES ('$user_id')";	
			}
			$conn->query($sql);	
			$_SESSION['login_details_id'] = $row['id'];
			header("Location:/chat");

		}
	}else{
		echo "Неправильный логин или пароль";
	}
	
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="../style/style.css">
</head>
<body>

	<form action="login.php" method="POST" id="loginForm">
		<input type="text" name="username" placeholder="Имя">
		<input type="text" name="lastname" placeholder="Фамилия">
		<input type="password" name="password" placeholder="Пароль">
		<input type="submit" >
	</form>
</body>
</html>