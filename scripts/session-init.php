<?php
//header("Content-Type: application/json", true);
date_default_timezone_set('Asia/Manila');
require("../myCon.php");
session_start();

/* $json = file_get_contents('php://input'); 
$post = json_decode($json, true);

var_dump($json);
echo "\n";
var_dump($post);
echo "\n";
var_dump($_POST);
echo "\n";
var_dump($_SESSION);
echo "\n";
echo "test: " . $_POST['src']; 
echo "\n"; */

if(isset($_POST['src'])) {
	$src = cleanTxt($_POST['src']);
	
	if($src == "index") {
		// json for index page
		$usnID = $usnName = $usnAdmin = $usnSession = "";
		if(isset($_SESSION['id'])) { // check if session actually exists.
			$userID = $_SESSION['id'];
			$userName = $_SESSION['emp-name'];
			if(isset($_SESSION['admin']))
				$isAdmin = "true";
			else
				$isAdmin = "false";
			unset($_SESSION['error']); // clear any error message
			
			$userlogged = 0;
			if($checkUserLog = $myConn->prepare("SELECT emp_accounts.acctID, emp_accounts.firstName, emp_accounts.lastName, emp_accounts.isAdmin, emp_time.empID, emp_time.timeDateTime, emp_time.timeMode FROM emp_time INNER JOIN emp_accounts ON emp_accounts.acctID = emp_time.empID WHERE DATE(emp_time.timeDateTime) = ? AND emp_time.empID = ? AND emp_time.timeMode = '0' ORDER BY emp_time.timeDateTime DESC LIMIT 1;")) {
				$checkUserLog->bind_param("ss", $curD, $empID);
				$checkUserLog->execute();
				$rsltLog = $checkUserLog->get_result();
				if($rsltLog->num_rows > 0) {
					// if user is logged in for the day then do not add log in entry to database.
					$userlogged = 1;
					while($rows = $rsltLog->fetch_assoc()) {
						$usnID = $rows['empID'];
						$usnName = $rows['lastName'] . ", " . $rows['firstName'];
						$usnAdmin = $rows['isAdmin'];
						$usnSession = $rows['timeDateTime'];
					}
				} 
				$checkUserLog->free_result();
				$checkUserLog->close();
			} else {
				$output = array("error" => "1", "message" => "sql error. session not found: " . $myConn->error);
				
			}
			
			if($userlogged == 1) {
				$_SESSION['expire'] = time() + 3600; // add session expiry of 1 hour.
				
				$output = array("error" => "2", "user-id" => $userID, "user-name" => $userName, "user-admin" => $isAdmin, "message" => "null", "user-session" => $usnSession);
			} else {
				$output = array("error" => "1", "message" => "session not found");
			}
			
			
			$output = array("error" => "0", "user-id" => $userID, "user-name" => $userName, "user-admin" => $isAdmin, "message" => "null");
		} else {
			$_SESSION['error'] = "Unknown login token. Please try to login again later.";
			$output = array("error" => "1", "message" => "session not found");
			
		}
		//var_dump($output);
		echo json_encode($output);
	}
	
	if($src == "login") {
		// json for login page
		if(isset($_SESSION['error'])) {
			unset($_SESSION['error']);
			$output = array("error" => "0", "message" => "Error cleared.");
		} else {
			$output = array("error" => "1", "message" => "No changes made.");
		}
		echo json_encode($output);
	}
}

function cleanTxt($x) {
	$x = trim($x);
	$x = htmlspecialchars($x);
	return $x;
}

?>