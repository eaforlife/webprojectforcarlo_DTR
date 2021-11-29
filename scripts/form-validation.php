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
					if(in_array($input, $pass_section)) {
						
					} else {
						if(strlen($_POST[$input]) <= 2) {
							$err[$input] = $input;
						} else {
							$update[$input] = $_POST[$input];
						}
					}
				}
			}
			if(!empty($update['emp-email']) && !filter_var($update['emp-email'], FILTER_VALIDATE_EMAIL)) {
				$err['emp-email'] = 'emp-email';
			}
			if(count($err) > 0) {
				$output = $err;
				$output['error'] = "1";
				$output['msg'] = "validation";
				echo json_encode($output);
			} else {
				if(empty($update['emp-old-pass'])) {
					if(!empty($update['emp-new-pass']) && !empty($update['emp-new-pass-confirm'])) {
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
					if(!empty($update['emp-new-pass']) && !empty($update['emp-new-pass-confirm'])) {
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
	
	if(isset($_GET['src']) && $_GET['src'] == 'add') {
		$fields = ['frm-emp-fname','frm-emp-lname','frm-emp-email'];
		$filedir = "../style/avatar/";
		if(!file_exists($filedir)) {
			mkdir($filedir, 0777, true);
		}
		
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			foreach($fields as $input) {
				if(empty($_POST[$input])) {
					$err[$input] = $input;
				} else {
					if(strlen($_POST[$input]) <= 2) {
						$err[$input] = $input;
					} else {
						$update[$input] = $_POST[$input];
					}
				}
			}
			if(isset($_POST['frm-emp-admin'])) {
				$update['frm-emp-admin'] = $_POST['frm-emp-admin'];
			}
			if(!empty($update['frm-emp-email']) && !filter_var($update['frm-emp-email'], FILTER_VALIDATE_EMAIL)) {
				$err['frm-emp-email'] = 'frm-emp-email';
			}
			/* Start of file validation */
			$fileTemp = $filedir . basename($_FILES['frm-emp-pic']['name']);
			$fileExt = strtolower(pathinfo($fileTemp,PATHINFO_EXTENSION));
			if(!is_uploaded_file($_FILES['frm-emp-pic']['tmp_name'])) {
				$err['frm-emp-pic'] = 'frm-emp-pic';
			} else {
				if(!getimagesize($_FILES['frm-emp-pic']['tmp_name'])) {
					$err['frm-emp-pic'] = 'frm-emp-pic';
				}
				if($_FILES['frm-emp-pic']['size'] > 5242880) {
					$err['frm-emp-pic'] = 'frm-emp-pic';
				}
				if($fileExt != "jpeg" && $fileExt != "jpg" && $fileExt != "png" && $fileExt != "gif") {
					$err['frm-emp-pic'] = 'frm-emp-pic';
				}
				if(!array_key_exists("frm-emp-pic",$err)) {
					$dt = date("Ymdgis");
					$imgname = $dt . $fileTemp;
					$imgname = md5($imgname);
					$imgname = substr($imgname, 0, 10);
					$imgname = $imgname . "." . $fileExt;
					$update["frm-emp-pic"] = $imgname;
				}
			}
			/* End of file validation */
			
			if(count($err) > 0) {
				$output = $err;
				$output['error'] = "1";
				$output['error-message'] = "validation";
			}  else {
				// Create auto generated username
				$username = substr($update['frm-emp-fname'],0,1) . $update['frm-emp-lname'];
				$dt = date("Ymdgis");
				$tmp_username = $dt;
				$tmp_username = md5($tmp_username);
				$tmp_username = substr($tmp_username, 0, 3);
				$username = $username . $tmp_username;
				// Create auto generated password
				$tmp_pass = $dt . "+" . $username;
				$tmp_pass = md5($tmp_pass);
				$tmp_pass = substr($tmp_pass,0,8);
				$password = md5($tmp_pass);
				// Set admin role
				if(isset($update['frm-emp-admin'])) {
					$admin = "1";
				} else {
					$admin = "0";
				}
				$fileDest = $filedir . $update['frm-emp-pic'];
				if(move_uploaded_file($_FILES['frm-emp-pic']['tmp_name'],$fileDest)) {
					if($adduser = $myConn->prepare("INSERT INTO emp_accounts (firstName,lastName,userName,passWord,email,photo,status,isAdmin) VALUES(?,?,?,?,?,?,?,?);")) {
						$adduser->bind_param("ssssssss",$update['frm-emp-fname'],$update['frm-emp-lname'],$username,$password,$update['frm-emp-email'],$update['frm-emp-pic'],$status,$admin);
						$status = "1";
						if($adduser->execute()) {
							$newempid = $adduser->insert_id;
							$output['error'] = "0";
							$output['error-message'] = "<img src='" . $filedir . $update['frm-emp-pic'] . "' class='img-fluid img-thumbnail mb-4' alt='pic-" . $newempid . "' style='height:200px;'>
								<h4>Successfully added employee to database</h4><p><small>Employee ID:</small> <strong>" . $newempid . "</strong></p>
								<p><small>Employee Name:</small> <strong>" . ucfirst($update['frm-emp-fname']) . " " . ucfirst($update['frm-emp-lname']) . "</strong></p>
								<p><small>Username:</small> <strong>" . strtoupper($username) . "</strong></p>
								<p><small>Password:</small> <strong>" . $tmp_pass . "</strong></p>";
						} else {
							$output['error'] = "1";
							$output['error-message'] = $adduser->error;
						}
					} else {
						$output['error'] = "1";
						$output['error-message'] = "Error in adding employee. Database error: " . $myConn->error;
					}
					$adduser->close();
				} else {
					$output['error'] = "1";
					$output['error-message'] = "Error in uploading image.";
				}	
			}
			echo json_encode($output);
		}
	}
	
	if(isset($_GET['src']) && $_GET['src'] == 'delete') {
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			if(isset($_POST['frm-emp-id']) && !empty($_POST['frm-emp-id'])) {
				$update['frm-emp-id'] = $_POST['frm-emp-id'];
			} else {
				$err['frm-emp-id'] = 'frm-emp-id';
			}
			
			if(count($err) > 0) {
				$output['error'] = "1";
				$output['error-message'] = "Employee not found!";
			} else {
				// no error so delete account and profile picture from server.
				$userImage = "../style/avatar/";
				$delImg = $myConn->prepare("SELECT photo FROM emp_accounts WHERE acctID=?;");
				$delImg->bind_param("s",$update['frm-emp-id']);
				$delImg->execute();
				$fetchImg = $delImg->get_result();
				if($fetchImg->num_rows > 0) {
					while($row = $fetchImg->fetch_assoc()) {
						$userImage = $userImage . $row['photo'];
					}
				}
				$fetchImg->free_result();
				$delImg->close();
				
				if(file_exists($userImage)) {
					unlink($userImage);
				}
				
				if($delemp = $myConn->prepare("DELETE emp_accounts, emp_time FROM emp_accounts LEFT JOIN emp_time ON emp_accounts.acctID=emp_time.empID WHERE emp_accounts.acctID=?;")) {
					$delemp->bind_param("s",$update['frm-emp-id']);
					if($delemp->execute()) {
						$output['error'] = "0";
						$output['error-message'] = "Successfully deleted employee with ID " . $update['frm-emp-id'] . "!";
					} else {
						$output['error'] = "1";
						$output['error-message'] = "Unable to delete employee with the following error:<br>" . $delemp->error;
					}
				} else {
					$output['error'] = "1";
					$output['error-message'] = "Unable to delete employee with the following database error:<br>" . $myConn->error;
				}
				$delemp->close();
			}
			echo json_encode($output);
		}
	}
	
	if(isset($_GET['src']) && $_GET['src'] == 'admin-edit') {
		$fields = ['frm-emp-id','frm-emp-fname','frm-emp-lname','frm-emp-email'];
		
		if($_SERVER["REQUEST_METHOD"] == "POST") {
			foreach($fields as $input) {
				if(empty($_POST[$input])) {
					$err[$input] = $input;
				} else {
					if(strlen($_POST[$input]) <= 2) {
						$err[$input] = $input;
					} else {
						$update[$input] = $_POST[$input];
					}
				}
			}
			
			if(!empty($update['frm-emp-email']) && !filter_var($update['frm-emp-email'], FILTER_VALIDATE_EMAIL)) {
				$err['frm-emp-email'] = 'frm-emp-email';
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
					//$output['error-message'] = "Some error";
					//$output[] = $update;
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
		global $output;
		
		$toChange = [];
		if($checkUser = $myConn->prepare("SELECT firstName, lastName, email FROM emp_accounts WHERE acctID=?;")) {
			$checkUser->bind_param("s",$update['frm-emp-id']);
			$checkUser->execute();
			$resultcheckUser = $checkUser->get_result();
			if($resultcheckUser->num_rows > 0) {
				while($row = $resultcheckUser->fetch_assoc()) {
					if($row['firstName'] != $update['frm-emp-fname']) {
						$toChange['frm-emp-fname'] = $update['frm-emp-fname'];
					}
					if($row['lastName'] != $update['frm-emp-lname']) {
						$toChange['frm-emp-lname'] = $update['frm-emp-lname'];
					}
					if($row['email'] != $update['frm-emp-email']) {
						$toChange['frm-emp-email'] = $update['frm-emp-email'];
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
				$updateStmt->bind_param("ss",$toChange['frm-emp-fname'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's first name updated successfully.";
			} elseif(!array_key_exists("frm-emp-fname", $toChange) && array_key_exists("frm-emp-lname", $toChange) && !array_key_exists("frm-emp-email", $toChange)) {
				// lname
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET lastName=? WHERE acctID=?;");
				$updateStmt->bind_param("ss",$toChange['frm-emp-lname'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's last name updated successfully.";
			} elseif(!array_key_exists("frm-emp-fname", $toChange) && !array_key_exists("frm-emp-lname", $toChange) && array_key_exists("frm-emp-email", $toChange)) {
				// email
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET email=? WHERE acctID=?;");
				$updateStmt->bind_param("ss",$toChange['frm-emp-email'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's email updated successfully.";
			} elseif(array_key_exists("frm-emp-fname", $toChange) && array_key_exists("frm-emp-lname", $toChange) && !array_key_exists("frm-emp-email", $toChange)) {
				// fname lname
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=? WHERE acctID=?;");
				$updateStmt->bind_param("sss",$toChange['frm-emp-fname'],$toChange['frm-emp-lname'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's first name and last name updated successfully.";
			} elseif(array_key_exists("frm-emp-fname", $toChange) && !array_key_exists("frm-emp-lname", $toChange) && array_key_exists("frm-emp-email", $toChange)) {
				// fname email
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, email=? WHERE acctID=?;");
				$updateStmt->bind_param("sss",$toChange['frm-emp-fname'],$toChange['frm-emp-email'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's first name and email updated successfully.";
			} elseif(!array_key_exists("frm-emp-fname", $toChange) && array_key_exists("frm-emp-lname", $toChange) && array_key_exists("frm-emp-email", $toChange)) {
				// lname email
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET lastName=?, email=? WHERE acctID=?;");
				$updateStmt->bind_param("sss",$toChange['frm-emp-lname'],$toChange['frm-emp-email'],$update['frm-emp-id']);
				$output['error-message'] = "Employee's last name and email updated successfully.";
			} else {
				$updateStmt = $myConn->prepare("UPDATE emp_accounts SET firstName=?, lastName=?, email=? WHERE acctID=?;");
				$updateStmt->bind_param("ssss",$toChange['frm-emp-fname'],$toChange['frm-emp-lname'],$toChange['frm-emp-email'],$update['frm-emp-id']);
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
				$output['msg'] = "none";
				return false;
			}
			
		}
	}
	
	
	function cleanTxt($x) {
		$x = trim($x);
		$x = htmlspecialchars($x);
		return $x;
	}
?>