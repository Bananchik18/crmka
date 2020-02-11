<?php

	include "../configs/db.php";
	session_start();
	if(isset($_POST['username']) && isset($_POST['lastname']) && isset($_POST['patronymic']) && isset($_POST['department']) && isset($_POST['position'])  && isset($_POST['password']) && isset($_POST['private_phone']) && isset($_POST['working_phone']) && isset($_POST['email'])){

		$draftUsername = $_POST['username'];
		$draftLastname = $_POST['lastname'];
		$draftPatronymic = $_POST['patronymic'];
		$draftDepartment = $_POST['department'];
		$draftPosition = $_POST['position'];
		$draftPassword = md5($_POST['password']);
		$draftPrivatePhone = $_POST['private_phone'];
		$draftWorkingPhone = $_POST['working_phone'];
		$draftEmail = $_POST['email'];

		$sql = "SELECT * FROM users WHERE username='$draftUsername'";
		$result = $conn->query($sql);
		
		if($result->num_rows > 0){
			echo "Выберити другой логин";
		}else{
			$sql = "INSERT INTO `users` (`id`, `username`, `lastname`, `patronymic`, `department`, `position`, `password`, `private_phone`,`working_phone`, `email`, `role`) VALUES (NULL, '$draftUsername', '$draftLastname', '$draftPatronymic', '$draftDepartment', '$draftPosition', '$draftPassword', '$draftPrivatePhone','$draftWorkingPhone', '$draftEmail', '$draftPosition');";
			echo $sql;
			$conn->query($sql);
			$_SESSION['user_id'] = $row['user_id'];
			header("Location:/");
		}
	}


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link rel="stylesheet" href="../style/style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body>
	
	<form action="register.php" method="POST" id="registerForm">
		<input type="text" name="lastname" placeholder="Фамилия" required>
		<input type="text" name="username" placeholder="Имя" required>
		<input type="text" name="patronymic" placeholder="Отчество" required>
		<input type="text" name="department" placeholder="Отдел" required>
		<input type="text" name="position" placeholder="Должность" required>
		<input type="password" name="password" placeholder="Пароль" required>
		<input type="text" name="private_phone" placeholder="Личный телефон" required>
		<input type="text" name="working_phone" placeholder="Рабочий телефон" required>
		<input type="text" name="email" placeholder="Email" required>
		<input type="submit">
	</form>
</body>

<script type="text/javascript" src="../filecdn/jquery.inputmask.js"></script>
<script type="text/javascript">
	 $(":input").inputmask();

	$("input[name='private_phone']").inputmask({"mask": "(999) 999-99-99"});
</script>
</html>