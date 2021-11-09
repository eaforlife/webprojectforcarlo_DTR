<?php
require("../myCon.php");
	
if(isset($_POST['email']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['password1']) && isset($_POST['password2'])) {
	$email = cleanTxt($_POST['email']);
	$fname = cleanTxt($_POST['firstName']);
	$lname = cleanTxt($_POST['lastName']);
	$pass = cleanTxt($_POST['password1']);
	$confirm_pass = cleanTxt($_POST['password2']);
	$isadmin = 0;
	
	print_r($_POST);
	
	if(isset($_POST['admin'])) {
		$isadmin = 1;
	} else {
		$isadmin = 0;
	}
	
	$errorMsg = "";
	
	if($email == "" && $fname == "" && $lname == "" && $pass == "" && $confirm_pass == "" && $isadmin == "") {
		$errorMsg .= "<p><small><span class='txt-danger'>One or more fields are incorrect.</span></small></p><br>";
	}
	
	if($pass !== $confirm_pass) {
		$errorMsg .= "<p><small><span class='txt-danger'>Password does not match.</span></small></p><br>";
	}
	
	if($pass < 6) {
		$errorMsg .= "<p><small><span class='txt-danger'>Password must be atleast 6 characters or more.</span></small></p><br>";
	}
	
	$errMsg = trim($errMsg);
	$pass = md5($pass);
	
	if($errorMsg == "") {
		$newID = 404;
		$createID = "SELECT acctID FROM emp_accounts DESC LIMIT 1";
		$idSQL = $myConn->query($createID);
		if($idSQL->num_rows > 0) {
			while($row = $idSQL->fetchassoc()) {
				$newID += $row['acctID'];
			}
		} 
		$username = $lastName . substr($firstName, 0) . acctID;
		$createEmp = "INSERT INTO emp_accounts VALUES ($newID, $fname, $lname, $userName, $pass, $email, 'ACTIVE', $isadmin);";
		if($myConn->query($createEmp) === TRUE) {
			echo "<h2 class='txt-success'>Successfully added employee!</h2>";
			echo "<p class='txt-success'><small>Your employee ID: $newID</small></p>";
			echo "<p class='txt-success'><small>Your username: $username</small></p>";
			echo "<p class='txt-success'><small>Your password: $pass</small></p>";
			echo "Error MSG $errMsg";
		} else {
			echo "<p class='txt-danger'><small>Something went wrong while adding the account</small></p>";
		}
		
		
	} else {
		echo $errMsg . "sad";
	}
	echo "sad";
} else {
	echo "test = " . $_POST['firstName'];
}

function cleanTxt($x) {
	$x = trim($x);
	$x = htmlspecialchars($x);
	return $x;
}
?>