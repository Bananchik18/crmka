<?php
	include "../../configs/db.php";
?>


<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<table>
		<tr>
			<th>id</th>
			<th>username</th>
			<th>lastname</th>
			<th>patronymic</th>
			<th>department</th>
			<th>position</th>
			<th>password</th>
			<th>private_phone</th>
			<th>working_phone</th>
			<th>email</th>
			<th>role</th>
			<th>confirmation</th>
		</tr>
		<?php
			$sql = "SELECT * FROM `users`";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				while ($row = $result->fetch_assoc()) {
					?>
						<tr>
							<td><?php echo $row['id']; ?></td>
							<td><?php echo $row['username']; ?></td>
							<td><?php echo $row['lastname']; ?></td>
							<td><?php echo $row['patronymic']; ?></td>
							<td><?php echo $row['department']; ?></td>
							<td><?php echo $row['position']; ?></td>
							<td><?php echo $row['password']; ?></td>
							<td><?php echo $row['private_phone']; ?></td>
							<td><?php echo $row['working_phone']; ?></td>
							<td><?php echo $row['email']; ?></td>
							<td><?php echo $row['role']; ?></td>
							<td><?php echo $row['confirmation']; ?></td>
							
							<?php 
								if($row['confirmation'] == 0){
									?>
									<td><a href="confirm.php?id=<?php echo $row['id'];?>">Подтвердить пользователя</a></td>
									<?php 
								}else if($row['confirmation'] == 1){
									?>
									<td><a href="disconect.php?id=<?php echo $row['id']; ?>">Отключить пользователя</a></td>
									<?php 
								}

							 ?>
						</tr>
					<?php
				}
			}
		?>
	</table>

</body>
</html>
