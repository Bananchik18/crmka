<?php
	include "configs/db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<?php
	// isset($_COOKIE['user_id']
	session_start();
		if(isset($_SESSION['user_id'])){
			$user_id = $_SESSION['user_id'];
			$sql = "SELECT * FROM `users` WHERE `id` = '$user_id'";
			// var_dump($sql);
			$result = $conn->query($sql);
			// var_dump($result);
			$row = $result->fetch_assoc();

			echo "Hello " . $row['username'];

			if($row['role'] == "admin"){
				echo "<a href='admin/users/'>Users</a>";	
			}
			echo "<a href='authorization/logout.php'>Logout</a>";
			if($row['confirmation'] == 0){
				echo("Ваш аккаунт ещё не подтвердили");
			}else{
			//	echo "Ваш аккаунт подтверждён";
				header('Location: chat/index.php');
			}
		}else{
			echo "<a href='authorization/login.php'>Login</a>";
			echo "<a href='authorization/register.php'>Register</a>";
		}

	?>
	
		
</body>
</html>




