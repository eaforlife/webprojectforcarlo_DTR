<?php
	session_start();
	date_default_timezone_set('Asia/Manila');
	require("../myCon.php");
	
	if(isset($_GET['src']) && cleanTxt($_GET['src']) != "") {
		if(cleanTxt($_GET['src']) == 'idle') {
			$empID = $_SESSION['id'];
			$currD = date('Y-m-d');
			$currDT = date('Y-m-d H:i:s');
			if($setIdle = $myConn->prepare("INSERT INTO emp_time VALUES (NULL,?,2,?);")) {
				$setIdle->bind_param("ss", $empID, $currDT);
				$setIdle->execute();
				session_unset();
				session_destroy();
				$_SESSION['error'] = "You've been logged out due to inactivity.";
				$output = array("status" => "IDLE OK");
			} else {
				$output = array("status" => "DATABASE ERROR: " . $myConn->error);
			}
			$setIdle->close();
			echo json_encode($output);
		}
		if(cleanTxt($_GET['src']) == 'error') {
			if(isset($_SESSION['error'])) {
				if(cleanTxt($_SESSION['error'] != "")) {
					$output = array("error" => "1", "status" => $_SESSION['error']);
					echo json_encode($output);
				}
			}
		}
	} else {
		if(isset($_SESSION['id'])) {
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
	}
	
	function cleanTxt($x) {
		$x = trim($x);
		$x = htmlspecialchars($x);
		return $x;
	}
?>