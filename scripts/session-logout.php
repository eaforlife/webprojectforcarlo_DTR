<?php
	session_start();
	date_default_timezone_set('Asia/Manila');
	require("../myCon.php");
	
	if(isset($_GET['src'])) {
		if(cleanTxt($_GET['src']) == 'idle') {
			$empID = $_SESSION['id'];
			$currD = date('Y-m-d');
			$currDT = date('Y-m-d H:i:s');
			$status = $errCtr = 0;  // status 1 is logged in while 2 is idle
			if($idleCheck = $myConn->prepare("SELECT * FROM emp_time WHERE empID=? AND DATE(timeDateTime)=? ORDER BY timeID DESC LIMIT 1;")) {
				$idleCheck->bind_param("ss", $empID, $currD);
				$idleCheck->execute();
				$idleRslt = $idleCheck->get_result();
				if($idleRslt->num_rows > 0) {
					while($row = $idleRslt->fetch_assoc()) {
						if($row['timeMode'] == "0") {
							$status = 1;
						}
						if($row['timeMode'] == "2") {
							$status = 2;
						}
					}
				}
				$idleCheck->free_result();
				$idleCheck->close();
				
				if($status == 1) {
					$setIdle = $myConn->prepare("INSERT INTO emp_time VALUES (NULL,?,2,?);");
					$output = array("status" => "IDLE OK");
				} else {
					$setIdle = $myConn->prepare("INSERT INTO emp_time VALUES (NULL,?,0,?);");
					$output = array("status" => "NOT IDLE OK");
				}
				$setIdle->bind_param("ss", $empID, $currDT);
				$setIdle->execute();
				
				$setIdle->close();
				$myConn->close();
				session_destroy();
				$_SESSION['error'] = "You've been logged out due to inactivity.";
			} else {
				$errCtr = $errCtr + 1;
				$output = array("status" => "DATABASE ERROR: " . $myConn->error);
			}
			
			echo json_encode($output);
		}
	} elseif(cleanTxt($_GET['src']) == 'error') {
		if(isset($_SESSION['error'])) {
			if(cleanTxt($_SESSION['error'] != "") {
				$output = array("error" => "1", "status" => $_SESSION['error']);
				echo json_encode($output);
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