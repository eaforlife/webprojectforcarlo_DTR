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
			$isloggedout = 0;
			$currD = date('Y-m-d');
			$currDT = date('Y-m-d H:i:s');
			/* if($checkStatus = $myConn->prepare("SELECT timeMode FROM emp_time WHERE empID=? AND DATE(timeDateTime)=? AND (timeMode=1 OR timeMode=2) ORDER BY timeID DESC LIMIT 1;")) {
				$checkStatus->bind_param("ss", $empID, $currD);
				$checkStatus->execute();
				$result = $checkStatus->get_result();
				if($result->num_rows > 0) {
					$isloggedout = 1;
				} else {
					$isloggedout = 0;
				}
			}
			$checkStatus->close(); */
			
			//if($isloggedout == 0) {
				if($logoutQuery  = $myConn->prepare("INSERT INTO emp_time VALUES (NULL,?,1,?);")) {
					$logoutQuery->bind_param("ss", $empID, $currDT);
					$logoutQuery->execute();
					session_unset();
					session_destroy();
					$output = array("status" => "OK - logout set");
				} else {
					session_unset();
					session_destroy();
					$output = array("status" => "Database Error: " . $myConn->error);
				}
				$logoutQuery->close();
			/* } else {
				session_unset();
				session_destroy();
				$output = array("status" => "OK - logout not set");
			} */
			
			echo json_encode($output);
		}
	}
	
	function cleanTxt($x) {
		$x = trim($x);
		$x = htmlspecialchars($x);
		return $x;
	}
?>