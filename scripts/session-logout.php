<?php
	session_start();
	date_default_timezone_set('Asia/Manila');
	require("../myCon.php");
	
	if(isset($_SESSION)) {
		// todo: add to database
		$empID = $_SESSION['id'];
		$currDT = date('Y-m-d H:i:s');
		if($logoutQuery  = $myConn->prepare("INSERT INTO emp_time VALUES (NULL,?,1,?);")) {
			$logoutQuery->bind_param("ss", $empID, $currDT);
			$logoutQuery->execute();
			
			session_unset();
			session_destroy();
			$output = array("status" => "OK");
		} else {
			$output = array("status" => "Database Error: " . $myConn->error);
		}
		$logoutQuery->close();
		echo json_encode($output);
	}
?>