<?php

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
		$empID = $name = $admin = "";
		if($loginQuery = $myConn->prepare("SELECT acctID, firstName, lastName, userName, passWord, isAdmin, status FROM emp_accounts WHERE userName LIKE ? AND passWord=? AND status='1' LIMIT 1;")) {
			$usn = "%$usn%";
			$loginQuery->bind_param("ss", $usn, $pwd);
			$loginQuery->execute();
			$rslt = $loginQuery->get_result();
			
			if($rslt->num_rows > 0) {
				session_start();
				while($rows = $rslt->fetch_assoc()) {
					$_SESSION['id'] = $rows['acctID'];
					$_SESSION['emp-name'] = $rows['lastName'] . ", " . $rows['firstName'];
					$debug = $rows['lastName'] . ", " . $rows['firstName'];
					if($rows['isAdmin'] == "0")
						$_SESSION['admin'] = "0";
					else
						$_SESSION['admin'] = "1";
				}
				$json_out = array("error" => "0", "message" => "No Error.", "test" => $debug);
				
			} else {
				// Username and password not found
				$json_out = array("error" => "1", "message" => "Invalid username or password. Please try again.");
			}
			$loginQuery->free_result();
			$loginQuery->close();
			
		} else {
			// Mysql error handling
			$json_out = array("error" => "1", "message" => "Something went wrong while trying to login. Error: " . $myConn->error);
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

function cleanTxt($x) {
	$x = trim($x);
	$x = htmlspecialchars($x);
	return $x;
}

?>