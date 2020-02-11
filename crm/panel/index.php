
	<link rel="stylesheet" href="../panel/css/panelcss.css">
<div id="menuPanelLeft">
	<a href="../chat/index.php"><div id="messenger"></div></a>
	<!-- <a href="../task/index.php"><div id="taskIcon"></div></a> -->
	<?php
		// include "../configs/db.php";

		$sql = "SELECT * FROM `users` WHERE `id`='".$_SESSION['user_id']."'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		if($row['role'] == "admin"){
			echo "<a href='../admin/users/index.php'><div>Admin</div></a>";
		}

	 ?>
</div>