<!DOCTYPE html>
<!-- Use of CSS Grid: https://www.w3schools.com/css/css_grid.asp -->
<html>
<head>
<title>Date Time Record - Home</title>
<meta name='viewport' content='width=device-width, initial-scale=1.0'> <!-- Call viewport for responsive html css: https://www.w3schools.com/html/html_responsive.asp -->
<link rel="stylesheet" type="text/css" href="./style/style.css">
<link rel="stylesheet" type="text/css" href="./style/style-1.css">
</head>
<body>

<!-- header -->
<?php require("./require_header.php"); ?>	

<div class="bottom-space">
<!-- content here -->
<?php if(isset($_SESSION['login-name']) && isset($_SESSION['login-id'])) { ?>
<div class="row">
	<div class="container">
		<div class="col-md-1">&nbsp;</div>
		<div class="col-md-10">
		<h2>Welcome! <?php echo $loginName; ?></h2>
		</div>
		<div class="col-md-1">&nbsp;</div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-1"></div>
		<div class="col-md-4">
			<p><strong>Quick Links</strong></p>
			<br>
				<button class="btn btn-info" id="time-daily">Time In - Daily</button>
				<button class="btn btn-info" id="time-weekly">Time In - Weekly</button>
				<button class="btn btn-info" id="edit-profile">Edit Profile</button>
		</div>
		<div class="col-md-1"></div>
		<div class="col-md-5">
			<p><strong>Your account info: </strong></p>
			<fieldset>
				<p><strong>Account ID: </strong><?php echo $loginID; ?></p>
				<p><strong>Account Name: </strong><?php echo $loginName; ?></p>
			</fieldset>
		</div>
		<div class="col-md-1"></div>
	</div>
</div>

<br><br>
<div class="clean"></div>

<?php if($loginLevel === '1'): // Only show when admin ?>
<div class="container">
	<div class="row">
		<div class="col-md-1">&nbsp;</div>
		<div class="col-md-5">
			<h2>Add Account</h2>
			<?php
			function cleanTxt($x) {
				$x = trim($x);
				$x = stripslashes($x);
				$x = htmlspecialchars($x);
				return $x;
			}
			function checkEmail($x) {
				if(!filter_var($x, FILTER_VALIDATE_EMAIL)) {
					return false;
				} else {
					return true;
				}
			}
			function checkLetters($x) {
				if(ctype_alpha($x)) {
					return true;
				} else {
					return false;
				}
			}
			
			if(isset($_POST['email']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['password1']) && isset($_POST['password2'])) {
				$email = $_POST['email'];
				$fname = $_POST['firstName'];
				$lname = $_POST['lastName'];
				$pass1 = $_POST['password1'];
				$pass2 = $_POST['password2'];
				$pass = "";
				$admin = 0;
				
				$errArr = array();
				
				
				if(isset($_POST['admin'])) {
					$admin = 1;
				}
				
				$email = cleanTxt($email);
				$fname = cleanTxt($fname);
				$lname = cleanTxt($lname);
				$pass1 = cleanTxt($pass1);
				$pass2 = cleanTxt($pass2);
				
				if(empty($email) || checkEmail($email) === false) {
					$errArr[0] = "<small><span class='txt-danger'>Invalid or empty email address!</span></small>";
				}
				if(empty($fname) || checkLetters($fname) === false || strlen($fname) <= 3) {
					$errArr[1] = "<small><span class='txt-danger'>Invalid or empty first name!</span></small>";
				}
				if(empty($lname) || checkLetters($lname) === false || strlen($lname) <= 3) {
					$errArr[2] = "<small><span class='txt-danger'>Invalid or empty last name!</span></small>";
				}
				if(empty($pass1)) {
					$errArr[3] = "<small><span class='txt-danger'>Invalid or empty password field!</span></small>";
				} else {
					if(strlen($pass1) < 8) {
						$errArr[5] = "<small><span class='txt-danger'>Password must be more than or equal to 8 characters!</span></small>";
					}
				}
				if(empty($pass2)) {
					$errArr[4] = "<small><span class='txt-danger'>Invalid or empty password field!</span></small>";
				} else {
					if(strlen($pass2) < 8) {
						$errArr[6] = "<small><span class='txt-danger'>Password must be more than or equal to 8 characters!</span></small>";
					} else {
						if($pass1 != $pass2) {
							$errArr[7] = "<small><span class='txt-danger'>Password must match!</span></small>";
						}
					}
				}
				
				if(empty($errArr)) {
					
					
					$newID = 404;
					$createID = "SELECT * FROM emp_accounts ORDER BY acctID DESC LIMIT 1";
					$idSQL = $myConn->query($createID);
					if($idSQL->num_rows > 0) {
						while($row = $idSQL->fetch_assoc()) {
							$newID = $row['acctID'];
							$newID = $newID + 1;
						}
					}
					
					$checkEmail = "SELECT * FROM emp_accounts WHERE email='$email';";
					$checkSQL = $myConn->query($checkEmail);
					if($checkSQL->num_rows > 0) {
						$errArr[0] = "<small><span class='txt-danger'>E-Mail already exists in our system!</span></small>";
					}
					
					if(empty($errArr)) {
						$pass = md5($pass1);
						$username = $fname[0] . $lname[0] . $newID;
						
						$createEmp = "INSERT INTO emp_accounts VALUES ('$newID', '$fname', '$lname', '$username', '$pass', '$email', '1', '$admin');";
						if($myConn->query($createEmp) === TRUE) {
							echo "<div id='success-msg'>";
							echo "<h2 class='txt-success'>Successfully added employee!</h2>";
							echo "<p class='txt-success'><small>Your employee ID: $newID</small></p>";
							echo "<p class='txt-success'><small>Your username: $username</small></p>";
							echo "<p class='txt-success'><small>Your password: $pass1</small></p>";
							echo "</div>";
							header( "refresh:10;url=./index.php" );
						} else {
							echo "<p class='txt-danger'><small>Something went wrong while adding the account</small></p>";
						}
					}
				}
			}
			?>
			<form method="post" id="frmAdd" class="frmAdd">
				<label for="frmEmail">E-Mail: </label>
				<input type="text" class="form-flex" name="email" id="frmEmail" placeholder="Valid E-Mail Address" autocomplete="username" required<?php if(!empty($errArr[0]) && !empty($errArr[0])) echo " autofocus";?>>&nbsp;
				<?php if(!empty($errArr[0])){ echo $errArr[0]; } ?><br>
				<label for="frmFirstName">First Name: </label>
				<input type="text" class="form-flex" name="firstName" id="frmFirstName" placeholder="First Name" required<?php if(!empty($errArr[1]) && !empty($errArr[1])) echo " autofocus";?>>&nbsp;
				<?php if(!empty($errArr[1])){ echo $errArr[1]; } ?><br>
				<label for="frmLastName">Last Name: </label>
				<input type="text" class="form-flex" name="lastName" id="frmLastName" placeholder="Last Name" required<?php if(!empty($errArr[2]) && !empty($errArr[2])) echo " autofocus";?>>
				<?php if(!empty($errArr[2])){ echo $errArr[2]; } ?><br>
				<label for="frmPassword1">Password: </label>
				<input type="password" class="form-flex" name="password1" id="frmPassword1" autocomplete="new-password" required<?php if(!empty($errArr[3]) || !empty($errArr[4]) || !empty($errArr[5]) || !empty($errArr[6])) echo " autofocus";?>>&nbsp;
				<?php if(!empty($errArr[5])){ echo $errArr[5]; } ?><br>
				<label for="frmPassword2">Confirm Password: </label>
				<input type="password" class="form-flex" name="password2" id="frmPassword2" autocomplete="new-password" required>&nbsp;
				<?php if(!empty($errArr[6])){ echo $errArr[6]; } ?><br>
				<label for="isadmin">Is Admin?</label>
				<input type="checkbox" value="yes" id="isadmin" name="admin"><br><br>
				<input type="submit" class="btn form-flex btn-success" value="Create">
			</form>
			<div id="addEmployee"></div>
		</div>
		<div class="col-md-1">&nbsp;</div>
		<div class="col-md-4">
			<h2>View Employee Status</h2>
			<form id="searchEmp">
				<label for="listEmployee">Search Employee:</label>
				<input list="lstEmp" id="listEmployee" name="emp" placeholder="ID or Name" required>
				<datalist id="lstEmp">
				<?php 
					$empSQL = "SELECT acctID, lastName, firstName FROM emp_accounts;";
					$empQuery = $myConn->query($empSQL);
					if($empQuery->num_rows > 0) {
						while($row = $empQuery->fetch_assoc()) {
							$empID = $row['acctID'];
							$lname = $row['lastName'];
							$fname = $row['firstName'];
							$output = $empID . " - " . $fname . " " . $lname;
							echo "\t\t\t\t<option value='$output'>";
						}
					}
				?>
				</datalist>
				<input type="submit" value="search">
			</form>
			<hr><br>
			<div id="outMsg"><p class="txt-success"><strong>Select values above to show form</strong></p><!-- Edit Form --></div>
		</div>
		<div class="col-md-1">&nbsp;</div>
	</div>
</div>
<?php endif; ?>

<?php } else { ?>
<div class="row">
	<div class="container">
		<div class="col-md-1">&nbsp;</div>
		<div class="col-md-10">
		<h2><a href="./login.php?log=1">You are not logged in! Sending you to login page</a></h2>
		</div>
		<div class="col-md-1">&nbsp;</div>
	</div>
</div>
<?php header( "refresh:5;url=./login.php?log=1&ref=login" ); } ?>
<br>
<br>
<?php include("./require_footer.php"); ?>

<script>
document.getElementById("frmAdd").onfocus = function() {
	document.getElementById("success-msg").style.display = 'none';
};

document.getElementById("time-daily").onclick = function() {
	window.location.replace("./time-in-daily.php");
};
document.getElementById("time-weekly").onclick = function() {
	window.location.replace("./time-in-weekly.php");
};
document.getElementById("edit-profile").onclick = function() {
	window.location.replace("./edit.php");
};

document.getElementById("listEmployee").onchange = function() {
	ajaxEdit();
};
var form = document.getElementById("searchEmp");
function handleForm(e) { 
	ajaxEdit();
	e.preventDefault(); // Prevent submit since we are using ajax
} 
form.addEventListener('submit', handleForm);

function ajaxEdit() {
	var x = document.getElementById("listEmployee").value;
	var xhttp;
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if(xhttp.readyState == XMLHttpRequest.DONE) {
			if(xhttp.status == 200) {
				document.getElementById("outMsg").innerHTML = xhttp.responseText;
			}
		}
	};
	xhttp.open("GET", "./getemployee.php?editemp=" + x, true);
	xhttp.send();
}
</script>

</body>
</html>