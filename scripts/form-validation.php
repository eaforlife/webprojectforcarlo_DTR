<?php
	date_default_timezone_set('Asia/Manila');
	require("../myCon.php");
	session_start();
	
	$err = [];
	$update = [];
	$output = [];
	
	if(isset($_GET['src']) && $_GET['src'] == 'edit') {
		$fields = ['emp-id','emp-fname','emp-lname','emp-email','emp-old-pass','emp-new-pass','emp-new-pass-confirm'];
		$pass_section = ['emp-old-pass','emp-new-pass','emp-new-pass-confirm'];
		
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			foreach($fields as $input) {
				if(empty($_POST[$input]) && !in_array($input, $pass_section)) {
					$err[$input] = $input;
				} else {
					$update[$input] = $_POST[$input];
				}
			}
			if(count($err) > 0) {
				$output = $err;
				$output['error'] = "1";
				$output['msg'] = "validation";
				echo json_encode($output);
			} else {
				if(empty($update['emp-old-pass'])) {
					if(!empty($update['emp-new-pass']) || !empty($update['emp-new-pass-confirm'])) {
						$output['error'] = "1";
						$output['msg'] = "validation";
						$output['emp-new-pass'] = "emp-new-pass";
						$output['emp-new-pass-confirm'] = "emp-new-pass-confirm";
						//var_dump($output);
					} else {
						// proceed to change fields without changing password
						if(updateUser(false, $update)) {
							$output['error'] = "0";
							//var_dump($update);
						} else {
							$output['error'] = "1";
						}
						//var_dump($output);
					}
				} else {
					if(!empty($update['emp-new-pass']) || !empty($update['emp-new-pass-confirm'])) {
						// ok to change pass
						if(updateUser(true, $update)) {
							$output['error'] = "0";
						} else {
							$output['error'] = "1";
						}
						//var_dump($output);
					} else {
						// "Do nothing on password";
						if(updateUser(false, $update)) {
							$output['error'] = "0";
							//var_dump($update);
						} else {
							$output['error'] = "1";
						}
						var_dump($output);
					}
				}
				echo json_encode($output);
			}
			//var_dump($output);
			//var_dump($err);
			//var_dump($update);
		}
	}
	
	if(isset($_GET['src']) && $_GET['src'] == 'admin-edit') {
		$fields = ['frm-emp-id','frm-emp-fname','frm-emp-lname','frm-emp-email'];
		
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			foreach($fields as $input) {
				if(empty($_POST[$input])) {
					$err[$input] = $input;
				} else {
					$update[$input] = $_POST[$input];
				}
			}
			
			if(count($err) > 0) {
				$output = $err;
				$output['error'] = "1";
				$output['error-message'] = "validation";
			} else {
				if(updateUserAdmin($update)) {
					$output['error'] = "0";
				} else {
					$output['error'] = "1";
					$output = $update;
				}
			}
			echo json_encode($output);
		}
	}
	
	if(isset($_GET['src']) && $_GET['src'] == 'resetpass') {
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			if(!empty($_POST['frm-emp-id'])) {
				$empid = cleanTxt($_POST['frm-emp-id']);
				$dt = date("Ymdgis");
				$newpass = $dt . $empid;
				$newpass = md5($newpass);
				$newpass = substr($newpass, 0, 8);
				$dbpass = md5($newpass);
				
				if($resetPass = $myConn->prepare("UPDATE emp_accounts SET passWord=? WHERE acctID=?;")) {
					$resetPass->bind_param("ss", $dbpass, $empid);
					$resetPass->execute();
					$output['error'] = "0";
					$output['error-message'] = "Successfully updated password for Employee " . $empid . ". New password: <pre>" . $newpass . "</pre>";
				} else {
					$output['error'] = "1";
					$output['error-message'] = "Unable to update with error: " . $myConn->error;
				}
				$resetPass->close();
			} else {
				$output['error'] = "1";
				$output['error-message'] = "Unknown employee";
			}
			echo json_encode($output);
		}
	}
	
	function updateUserAdmin($update) {
		global $myConn;
		
		$toChange = [];
		if($checkUser = $myConn->prepare("SELECT firstName, lastName, email FROM emp_accounts WHERE acctID=?;")) {
			$checkUser->bind_param("s",$update['frm-emp-id']);
			$checkUser->execute();
			$resultcheckUser = $checkUser->get_result();
			if($resultcheckUser->num_rows > 0) {
				while($row = $resultcheckUser->fetch_assoc()) {
					if($row['firstName'] != $update['frm-emp-fname']) {
						$toChange['emp-fname'] = $update['frm-emp-fname'];
					}
					if($row['lastName'] != $update['frm-emp-lname']) {
						$toChange['emp-lname'] = $update['frm-emp-lname'];
					}
					if($row['email'] != $update['frm-emp-email']) {
						$toChange['emp-email'] = $update['frm-emp-email'];
					}
				}
			}
			$resultcheckUser->free_result();
		}
		$checkUser->close();
		
		if(count($toChange) > 0) {
			if(array_key_exists("frm-emp-fname", $toChange) && !array_key_exists("frm-emp-lname", $toChange) && !array_key_exists("frm-emp-email", $toChange)) {
				// fname
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=? WHERE acctID=?;");
				$updateStmt->bind_param("ss",$toChange['emp-fname'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's first name updated successfully.";
			} elseif(!array_key_exists("frm-emp-fname", $toChange) && array_key_exists("frm-emp-lname", $toChange) && !array_key_exists("frm-emp-email", $toChange)) {
				// lname
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET lastName=? WHERE acctID=?;");
				$updateStmt->bind_param("ss",$toChange['emp-lname'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's last name updated successfully.";
			} elseif(!array_key_exists("frm-emp-fname", $toChange) && !array_key_exists("frm-emp-lname", $toChange) && array_key_exists("frm-emp-email", $toChange)) {
				// email
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET email=? WHERE acctID=?;");
				$updateStmt->bind_param("ss",$toChange['emp-email'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's email updated successfully.";
			} elseif(array_key_exists("frm-emp-fname", $toChange) && array_key_exists("frm-emp-lname", $toChange) && !array_key_exists("frm-emp-email", $toChange)) {
				// fname lname
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=? WHERE acctID=?;");
				$updateStmt->bind_param("sss",$toChange['emp-fname'],$toChange['emp-lname'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's first name and last name updated successfully.";
			} elseif(array_key_exists("frm-emp-fname", $toChange) && !array_key_exists("frm-emp-lname", $toChange) && array_key_exists("frm-emp-email", $toChange)) {
				// fname email
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, email=? WHERE acctID=?;");
				$updateStmt->bind_param("sss",$toChange['emp-fname'],$toChange['emp-email'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's first name and email updated successfully.";
			} elseif(!array_key_exists("frm-emp-fname", $toChange) && array_key_exists("frm-emp-lname", $toChange) && array_key_exists("frm-emp-email", $toChange)) {
				// lname email
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET lastName=?, email=? WHERE acctID=?;");
				$updateStmt->bind_param("sss",$toChange['emp-lname'],$toChange['emp-email'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's last name and email updated successfully.";
			} else {
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=?, email=? WHERE acctID=?;");
				$updateStmt->bind_param("sss",$toChange['emp-fname'],$toChange['emp-lname'],$toChange['emp-email'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's first name, last name and email updated successfully.";
			}
			
			if($updateStmt) {
				$updateStmt->execute();
				return true;
			} else {
				$output['error-message'] = "Database error: " . $myConn->error;
				return false;
			}
			$updateStmt->close();
		} else {
			$output['error-message'] = "No changes were made.";
			return false;
		}
	}

	function updateUser($withPass, $update) {
		global $err;
		global $myConn;
		global $output;
		
		$toChange = [];
		if($checkUser = $myConn->prepare("SELECT firstName, lastName, email FROM emp_accounts WHERE acctID=?;")) {
			$checkUser->bind_param("s",$update['emp-id']);
			$checkUser->execute();
			$resultcheckUser = $checkUser->get_result();
			if($resultcheckUser->num_rows > 0) {
				while($row = $resultcheckUser->fetch_assoc()) {
					if($row['firstName'] != $update['emp-fname']) {
						$toChange['emp-fname'] = $update['emp-fname'];
					}
					if($row['lastName'] != $update['emp-lname']) {
						$toChange['emp-lname'] = $update['emp-lname'];
					}
					if($row['email'] != $update['emp-email']) {
						$toChange['emp-email'] = $update['emp-email'];
					}
				}
			}
			$resultcheckUser->free_result();
		}
		$checkUser->close();
		
		if($withPass == true) {
			$passCheck = md5($update['emp-old-pass']);
			if($checkPass = $myConn->prepare("SELECT * FROM emp_accounts WHERE acctID=? AND passWord=?;")) {
				$checkPass->bind_param("ss",$update['emp-id'],$update['emp-old-pass']);
				$checkPass->execute();
				$resultcheckPass = $checkPass->get_result();
				if($resultcheckPass->num_rows > 0) {
					$output['msg'] = "password ok";
					// good
				} else {
					$output['msg'] = "Password not found";
					$err['emp-old-pass'] = 'emp-old-pass';
				}
				$resultcheckPass->free_result();
			} else {
				// mysql error
				$err['emp-old-pass'] = 'emp-old-pass';
				$err['emp-new-pass-confirm'] = 'emp-new-pass-confirm';
				$output['msg'] = "Error: " . $myConn->error;
				
			}
			$checkPass->close();
			
			if(strlen($update['emp-new-pass']) >= 8 && strlen($update['emp-new-pass']) <= 12) {
				// good
			} else {
				$err['emp-new-pass'] = 'emp-new-pass';
			}
			if(strlen($update['emp-new-pass-confirm']) >= 8 && strlen($update['emp-new-pass-confirm']) <= 12) {
				// good
			} else {
				$err['emp-new-pass-confirm'] = 'emp-new-pass-confirm';
			}
			if($update['emp-new-pass'] == $update['emp-new-pass-confirm']) {
				// good
			} else {
				$err['emp-new-pass-confirm'] = 'emp-new-pass-confirm';
			}
			
			if(count($err) > 0) {
				$output = $err;
				$output['msg'] = "validation";
				return false;
			} else {
				$newPass = md5($update['emp-new-pass-confirm']);
				if(array_key_exists("emp-fname",$toChange) && !array_key_exists("emp-lname",$toChange) && !array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, passWord=? WHERE acctID=?;");
					$updateStmt->bind_param("sss",$update['emp-fname'],$newPass,$update['emp-id']);
					$output['msg'] = "Updated pass and firstname.";
				} elseif(array_key_exists("emp-fname",$toChange) && array_key_exists("emp-lname",$toChange) && !array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=?, passWord=? WHERE acctID=?;");
					$updateStmt->bind_param("ssss",$update['emp-fname'],$update['emp-lname'],$newPass,$update['emp-id']);
					$output['msg'] = "Updated pass, lastname and firstname.";
				} elseif(array_key_exists("emp-fname",$toChange) && array_key_exists("emp-lname",$toChange) && array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=?, email=?, passWord=? WHERE acctID=?;");
					$updateStmt->bind_param("sssss",$update['emp-fname'],$update['emp-lname'],$update['emp-email'],$newPass,$update['emp-id']);
					$output['msg'] = "Updated pass, email, lastname and firstname.";
				} elseif(!array_key_exists("emp-fname",$toChange) && array_key_exists("emp-lname",$toChange) && !array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET lastName=?, passWord=? WHERE acctID=?;");
					$updateStmt->bind_param("ssss",$update['emp-lname'],$newPass,$update['emp-id']);
					$output['msg'] = "Updated pass, and lastname";
				} elseif(array_key_exists("emp-fname",$toChange) && !array_key_exists("emp-lname",$toChange) && array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, email=?, passWord=? WHERE acctID=?;");
					$updateStmt->bind_param("ssss",$update['emp-fname'],$update['emp-email'],$newPass,$update['emp-id']);
					$output['msg'] = "Updated pass, email and firstname.";
				} elseif(!array_key_exists("emp-fname",$toChange) && array_key_exists("emp-lname",$toChange) && array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET email=?, lastName=?, passWord=? WHERE acctID=?;");
					$updateStmt->bind_param("ssss",$update['emp-email'],$update['emp-lname'],$newPass,$update['emp-id']);
					$output['msg'] = "Updated pass, lastname and email.";
				} elseif(!array_key_exists("emp-fname",$toChange) && !array_key_exists("emp-lname",$toChange) && array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET email=?, passWord=? WHERE acctID=?;");
					$updateStmt->bind_param("ssss",$update['emp-email'],$newPass,$update['emp-id']);
					$output['msg'] = "Updated email and pass.";
				} else {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET passWord=? WHERE acctID=?;");
					$updateStmt->bind_param("ss",$newPass,$update['emp-id']);
					$output['msg'] = "Updated pass only.";
				}
				if($updateStmt) {
					$updateStmt->execute();
					return true;
				} else {
					$output['msg'] = "Database error: " . $myConn->error;
					return false;
				}
				$updateStmt->close();
			}
		} else {
			// todo check if fields exist already
			if(count($toChange) > 0) {
				if(array_key_exists("emp-fname",$toChange) && !array_key_exists("emp-lname",$toChange) && !array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=? WHERE acctID=?;");
					$updateStmt->bind_param("ss",$update['emp-fname'],$update['emp-id']);
					$output['msg'] = "Updated firstname.";
				} elseif(!array_key_exists("emp-fname",$toChange) && array_key_exists("emp-lname",$toChange) && !array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET lastName=? WHERE acctID=?;");
					$updateStmt->bind_param("ss",$update['emp-lname'],$update['emp-id']);
					$output['msg'] = "Updated lastname.";
				} elseif(!array_key_exists("emp-fname",$toChange) && !array_key_exists("emp-lname",$toChange) && array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET email=? WHERE acctID=?;");
					$updateStmt->bind_param("ss",$update['emp-email'],$update['emp-id']);
					$output['msg'] = "Updated email.";
				} elseif(array_key_exists("emp-fname",$toChange) && array_key_exists("emp-lname",$toChange) && !array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=? WHERE acctID=?;");
					$updateStmt->bind_param("sss",$update['emp-fname'],$update['emp-lname'],$update['emp-id']);
					$output['msg'] = "Updated lastname and firstname.";
				} elseif(!array_key_exists("emp-fname",$toChange) && array_key_exists("emp-lname",$toChange) && array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET lastName=?, email=? WHERE acctID=?;");
					$updateStmt->bind_param("sss",$update['emp-lname'],$update['emp-email'],$update['emp-id']);
					$output['msg'] = "Updated lastname and email.";
				} elseif(array_key_exists("emp-fname",$toChange) && !array_key_exists("emp-lname",$toChange) && array_key_exists("emp-email",$toChange)) {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, email=? WHERE acctID=?;");
					$updateStmt->bind_param("sss",$update['emp-fname'],$update['emp-email'],$update['emp-id']);
					$output['msg'] = "Updated firstname and email.";
				} else {
					$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=?, email=? WHERE acctID=?;");
					$updateStmt->bind_param("ssss",$update['emp-fname'],$update['emp-lname'],$update['emp-email'],$update['emp-id']);
					$output['msg'] = "Updated email, lastname and firstname.";
				}
				if($updateStmt) {
					$updateStmt->execute();
					return true;
				} else {
					$output['msg'] = "Database error: " . $myConn->error;
					return false;
				}
				$updateStmt->close();
			} else {
				$output['msg'] = "No changes made.";
				return true;
			}
			
		}
	}
	
	
	function cleanTxt($x) {
		$x = trim($x);
		$x = htmlspecialchars($x);
		return $x;
	}
?>