<?php
	date_default_timezone_set('Asia/Manila');
	require("../myCon.php");
	session_start();

	$err = array();
	$update = array();
	
	
	if(!isset($_POST['emp-fname']) || cleanTxt($_POST['emp-fname']) == "") {
		$err['emp-fname'] = "";
	} else {
		$update['first'] = cleanTxt($_POST['emp-fname']);
	}
	if(!isset($_POST['emp-lname']) || cleanTxt($_POST['emp-lname']) == "") {
		$err['emp-lname'] = "";
	} else {
		$update['last'] = cleanTxt($_POST['emp-lname']);
	}
	if(!isset($_POST['emp-email']) || cleanTxt($_POST['emp-email']) == "") {
		$err['emp-email'] = "";
	} else {
		$update['email'] = cleanTxt($_POST['emp-email']);
	}
	if(isset($_POST['emp-old-pass']) && cleanTxt($_POST['emp-old-pass']) == "") {
		if(isset($_POST['emp-new-pass']) && cleanTxt($_POST['emp-new-pass']) != "") {
			$err['emp-old-pass'] = "";
		}
		if(isset($_POST['emp-new-pass-confirm']) && cleanTxt($_POST['emp-new-pass-confirm']) != "") {
			$err['emp-old-pass'] = "";
			$err['emp-new-pass'] = ""; 
		}
	} else {
		if(isset($_POST['emp-new-pass']) && cleanTxt($_POST['emp-new-pass']) != "") {
			$update['emp-new-pass'] = cleanTxt($_POST['emp-new-pass']);
		}
		if(isset($_POST['emp-new-pass-confirm']) && cleanTxt($_POST['emp-new-pass-confirm']) != "") {
			$update['emp-new-pass-confirm'] = cleanTxt($_POST['emp-new-pass-confirm']);
		}
		$update['old-pass'] = cleanTxt($_POST['emp-old-pass']);
	}
	
	if(count($err) > 0) {
		$err['error'] = "yes";
		$err['type'] = "validation";
		echo json_encode($err);
	} else {
		$empid = $_SESSION['id'];
		if(!empty($update['emp-old-pass']) && !empty($update['emp-new-pass']) && !empty($update['emp-new-pass-confirm'])) {
			$oldpass = md5($update['emp-old-pass']);
			if($checkPass = $myConn->prepare("SELECT * FROM emp_accounts WHERE acctID=? AND passWord=?;")) {
				$checkPass->bind_param("ss",$empid,$oldpass);
				$checkPass->execute();
				$passRslt = $checkPass->get_result();
				if($passRslt->num_rows > 0) {
					// do nothing
				} else {
					$err['emp-old-pass'] = "";
				}
			} else {
				$err['error'] = "Database error: " . $myConn->error;
			}
			$checkPass->free_result();
			
			if(strlen($update['emp-new-pass']) < 8 && strlen($update['emp-new-pass']) > 12) {
				$err['emp-new-pass'] = "";
			}
			if(strlen($update['emp-new-pass-confirm']) < 8 && strlen($update['emp-new-pass-confirm']) > 12) {
				$err['emp-new-pass-confirm'] = "";
			}
			if($update['emp-new-pass'] != $update['emp-new-pass-confirm']) {
				$err['emp-new-pass'] = "";
				$err['emp-new-pass-confirm'] = "";
			}
			
			if(count($err) <= 0) {
				$update['emp-set-pass'] = md5($update['emp-new-pass']);
			}
		}
		
		if(count($err) > 0) {
			$err['error'] = "yes";
			$err['type'] = "validation";
			echo json_encode($err);
		} else {
			// update database
			if(isset($update['emp-set-pass']) && $update['emp-set-pass'] != "") {
				if($updateAccount = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=?, email=?, passWord=? WHERE acctID=?;")) {
					$updateAccount->bind_param("sssss",$update['first'],$update['last'],$update['email'],$update['emp-set-pass'],$empid);
					$updateAccount->execute();
					$output['error'] = "0";
					$output['message'] = "Update database with password is OK.";
				} else {
					$output['error'] = "1";
					$output['message'] = "Update database with password error: " . $myConn->error;
				}
				$updateAccount->close();
			} else {
				if($updateAccount = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=?, email=? WHERE acctID=?;")) {
					$updateAccount->bind_param("ssss",$update['first'],$update['last'],$update['email'],$empid);
					$updateAccount->execute();
					$output['error'] = "0";
					$output['message'] = "Update database without password is OK.";
				} else {
					$output['error'] = "1";
					$output['message'] = "Update database error: " . $myConn->error;
				}
				$updateAccount->close();
			}
			$output['type'] = "update";
			echo json_encode($output);
		}
		
	}
	
	function cleanTxt($x) {
		$x = trim($x);
		$x = htmlspecialchars($x);
		return $x;
	}
?>