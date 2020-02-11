<?php

$servername = "localhost";
$username = "root";
$password = "eximlabcrm2020";
$dbname = "crm";


$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset('utf8');
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
} 

date_default_timezone_set('Europe/Kiev');
