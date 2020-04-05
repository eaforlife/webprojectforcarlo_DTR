<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="./style/style.css">
	<link rel="stylesheet" type="text/css" href="./style/style-1.css">
</head>
<body>
<?php
session_start();
$errMsg = "";

if(isset($_POST['uname']) && isset($_POST['pword'])) {

	$username = $_POST['uname'];
	$password = $_POST['pword'];

	$username = trim($username);
	$username = htmlspecialchars($username);
	$password = trim($password);
	$password = htmlspecialchars($password);
	
	
	
	if(empty($username)&& empty($password)) {
		$errMsg = "One or more fields are empty. Please try again!";
	} else {
		$errMsg = "";
		$password = md5($password); // encrypt password
		
		require("./myCon.php");
		$loginQuery = "SELECT * FROM emp_accounts WHERE userName LIKE '%$username%' AND passWord='$password' LIMIT 1;";
		$loginResult = $myConn->query($loginQuery);
		if($loginResult->num_rows > 0) {
			while($row = $loginResult->fetch_assoc()) {
				$login_id = $row['acctID'];
				$login_name = $row['lastName'] . ", " . $row['firstName'];
				$login_level = $row['isAdmin'];
				
				$_SESSION['login-id'] = $login_id;
				$_SESSION['login-name'] = $login_name;
				$_SESSION['login-level'] = $login_level;
				$_SESSION['expire'] = time() + 3600; // 3600 = plus 1 hours
				
			}
			header("Location: ./index.php");
			die();
		} else {
			$errMsg = "One or more fields are incorrect. Check if your inputs are correct";
		}
	}
	
}

if(isset($_GET['log'])) {
	
	$login = $_GET['log'];
	$login = htmlspecialchars($login);
	$login = trim($login);
	
	// 1 = login,  0 = logout
	
	
	$message = "";
	$logID = "";
	// Check if user is currently logged in

	
	if($login == '1' && !isset($_SESSION['login-name'])) {
		// login codes ?>
		<div class="row">
			<div class="container">
				<div class="col-md-12">
					<h2>Login</h2>
					<?php 
						if(isset($_GET['ref'])) {
							if($_GET['ref'] == 'expire' && empty($errMsg)) {  // show this message if session expired
								echo "\n<p><small><span class='txt-danger' id='refMsg'>Your session has expire due to inactivity!<br>Pleae login again to access our service!</span></small></p>\n";
							} else { // show this message only if login error does not exist.
								echo "\n<p><small><span class='txt-danger' id='refMsg'>You need to login before accessing<br>our services!</span></small></p>\n";
							}
						}
						?>
					<form class="loginform" id="loginform" method="post">
						<?php if(!empty($errMsg)): ?>
						<small><span class="txt-danger"><?php echo $errMsg ?></span></small><br><br>
						<?php	endif;  ?>
						<label for="uname">Username: </label>
						<input type="text" id="uname" name="uname" placeholder="Your Username" autocomplete="username" autofocus required><br><br>
						<label for="pword">Password: </label>
						<input type="password" id="pword" name="pword" placeholder="Your Password(case sensitive)" autocomplete="current-password" required><br><br>
						<input type="submit" class="btn-success" value="Login"><input type="reset" class="btn-light">
					</form>
					<script>
						document.getElementById("uname").onkeydown = function() {
							document.getElementById("refMsg").style.display = "none";
						};
						document.getElementById("pword").onkeydown = function() {
							document.getElementById("refMsg").style.display = "none";
						};
					</script>
					<br><br>
				</div>
			</div>
		</div>
		<?php } elseif($login == '0' && isset($_SESSION['login-name'])) { ?>
		<div class="row">
			<div class="container">
				<div class="col-md-2">&nbsp;</div>
				<div class="col-md-8">
					<h2>Logging Out. Please Wait.</h2>
				</div>
				<div class="col-md-2">&nbsp;</div>
			</div>
		</div>
		<?php
		session_destroy();
		header( "refresh:5;url=./login.php?log=1" );
		} else { ?>
		<div class="row">
			<div class="container">
				<div class="col-md-2">&nbsp;</div>
				<div class="col-md-8">
					<h2>We have encountered an unexpected error. Try again later or contact our site admin. Redirecting you to the homepage.</h2>
				</div>
				<div class="col-md-2">&nbsp;</div>
			</div>
		</div>
		<?php
		header( "refresh:5;url=./index.php" );
		}
		require_once("./require_footer.php");
		} else { ?>
	<div class="row">
		<div class="container">
			<div class="col-md-2">&nbsp;</div>
			<div class="col-md-8 col-flex">
				<h2>We have encountered an unexpected error. Try again later or contact our site admin.</h2><br>
				<p><small><a href="./index.php">Redirecting you to our homepage in 10 seconds.</a></small></p>
			</div>
			<div class="col-md-2">&nbsp;</div>
		</div>
	</div>
<?php } ?>
<?php
	if(isset($_GET['logintimer']) && isset($_GET['log']) && $_GET['log'] == '1') {
		// call from ajax.
		$loginSeconds = $_GET['logintimer'];
		$loginSeconds = trim($loginSeconds);
		$loginSeconds = stripslashes($loginSeconds);
		$loginSeconds = htmlspecialchars($loginSeconds);
		
		if(time() > $loginSeconds) {
			echo "<script> window.location.href = './login.php?ref=expire'; </script>";
		}
	}
?>
</body>
</html>