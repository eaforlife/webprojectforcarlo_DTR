<?php
date_default_timezone_set('Asia/Manila');
require("../myCon.php");

// Variables require to convert POST/GET data from Javascript JSON/AJAX to PHP
$json = file_get_contents('php://input'); 
$obj = json_decode($json, true);

if(isset($obj['login-username']) && isset($obj['login-password'])) {
	$errorCtr = 0;
	$usn = $pwd = "";
	
	if(!empty(cleanTxt($obj['login-username']))) {
		$usn = cleanTxt($obj['login-username']);
	} else {
		$errorCtr++;
	}
	
	if(!empty(cleanTxt($obj['login-password']))) {
		$pwd = cleanTxt($obj['login-password']);
		$pwd = md5($pwd);
	} else {
		$errorCtr++;
	}
	
	if($errorCtr == 0) {
		$empID = "";
		$curDt = date('Y-m-d H:i:s');
		$curD = date('Y-m-d');
		if($loginQuery = $myConn->prepare("SELECT acctID, firstName, lastName, userName, passWord, isAdmin, status FROM emp_accounts WHERE userName LIKE ? AND passWord=? AND status=1 LIMIT 1;")) {
			$usn = "%$usn%";
			$loginQuery->bind_param("ss", $usn, $pwd);
			$loginQuery->execute();
			$rslt = $loginQuery->get_result();
			
			if($rslt->num_rows > 0) {
				while($rows = $rslt->fetch_assoc()) {
					$empID = $rows['acctID'];
					$empName = $rows['lastName'] . ", " . $rows['firstName'];
					if($rows['isAdmin'] == "0")
						$empAdmin = "0";
					else
						$empAdmin = "1";
				}
			} else {
				// Username and password not found
				$json_out = array("error" => "1", "message" => "Invalid username or password. Please try again.");
				//$errorCtr++;
			}
			$rslt->free_result();
			
		} else {
			// Mysql error handling
			$json_out = array("error" => "1", "message" => "Something went wrong while trying to login. Error: " . $myConn->error);
		}
		$loginQuery->close();
		
		if(empty($empID)) {
			$json_out = array("error" => "1", "message" => "Invalid username or password. Please try again.");
		} else {
			if($logoutSession = $myConn->prepare("INSERT INTO emp_time VALUES (NULL,?,1,?);")) {
				$logoutSession->bind_param("ss", $empID, $curDt);
				$logoutSession->execute();
			}
			$logoutSession->close();
			sleep(2);
			if($loginUser = $myConn->prepare("INSERT INTO emp_time VALUES (NULL,?,0,?);")) {
				$loginUser->bind_param("ss", $empID, $curDt);
				$loginUser->execute();
				$json_out = array("error" => "0", "message" => "OK.");
				addtoSession($empID, $empName, $empAdmin);
			} else {
				$json_out = array("error" => "1", "message" => "Something went wrong while trying to login. Error: " . $myConn->error);
			}
			$loginUser->close();
		}
		echo json_encode($json_out);
	} else {
		// Username and password from form is empty
		$json_out = array("error" => "1", "message" => "Invalid username or password. Please try again.");
		echo json_encode($json_out);
	}
} else {
	//echo var_dump($obj);
	//$jsonpost = var_dump(json_decode(file_get_contents('php://input')));
	$json_out = array("message" => $obj["login-username"]);
	echo json_encode($json_out);
}

function addtoSession($usnID, $usnName, $usnAdmin) {
	session_start();
	$_SESSION['id'] = $usnID;
	$_SESSION['emp-name'] = $usnName;
	if(isset($_SESSION['error']))
		unset($_SESSION['error']);
	if($usnAdmin=="1")
		$_SESSION['admin'] = "1";
	//$_SESSION['expire'] = time() + 3600; // add session expiry of 1 hour.
}

function cleanTxt($x) {
	$x = trim($x);
	$x = htmlspecialchars($x);
	return $x;
}

?>