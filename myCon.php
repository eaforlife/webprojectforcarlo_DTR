<?php
// MySQL connection
$serverDB = "127.0.0.1";
$usernameDB = "root";
$passwordDB = "1234";
$dbName = "datetimerecorddb";

$myConn = new mysqli($serverDB, $usernameDB, $passwordDB, $dbName);
if ($myConn->connect_error)
	die("Unable to connect to the database server: " . $myConn->connection_error);
?>