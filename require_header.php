<?php
	//date_default_timezone_set("Asia/Manila");  // IMPORTANT! Change timezone accordingly. MySQL/MariaDB only saves in Zulu time so we convert it in PHP. Otherwise browser will use different time!
												 // Timezone list: https://www.php.net/manual/en/timezones.php
	session_start();
	require("./myCon.php");
	if(isset($_SESSION['login-name']) && isset($_SESSION['login-id']) && isset($_SESSION['expire'])) {
		$loginName = $_SESSION['login-name'];
		$loginID = $_SESSION['login-id'];
		$loginLevel = $_SESSION['login-level'];
		
		if($_SESSION['expire'] > time()) {
			$_SESSION['expire'] = time() + 3600;
		} else {
			session_destroy();
			header("Location: ./login.php?log=1&ref=expire");
		}
	} else {
		header("Location: ./login.php?log=1&ref=login");
	}
	if(!isset($_SESSION['login-level'])) {
		$loginLevel = '0'; // Avoid error 
	}

?>

<header class="shadow">
	<div class="bg-container">
		<div class="bg">
			<div class="bg-txt">
				<a href="./index.php">Date Time Record</a><?php // Website Name. Redirect to Home Page ?>
			</div>
		</div>
	</div>
	
	<div class="menu-nav">
		<a name="top" class="active" disabled> menu </a>
		<?php if($loginLevel === '1'): ?><a href='./records.php'>Records</a><?php endif; ?>
		<div class="dropdown">
			<button class="dropbtn">Tools</button>
			<div class="dropdown-content">
				<a href="./edit.php">Edit Profile</a>
				<a href="./time-in-daily.php">Time-In/Time-out - Daily</a>
				<?php if($loginLevel === '1'): ?>
				<a href="./time-in-weekly.php">Time-In/Time-out - Weekly</a>
				<a href="./time-in-monthly.php">Time-In/Time-out - Monthly</a>
				<a href="./overview.php">Time Record Audit</a>
				<?php endif; ?>
			</div>
		</div>
		<?php if(!isset($_SESSION['login-name'])): ?>
			<a href='./login.php?log=1'>Login</a>
		<?php else: ?>
			<a href='./login.php?log=0'>Logout <?php echo $loginName; ?>?</a>
		<?php endif; ?>
		<div class="nav-dropdown">
			<button class="btn btn-primary active"><div class="hamburger"></div><div class="hamburger"></div><div class="hamburger"></div></button>
			<div class="nav-dropdown-content">
				<a href='./index.php' active>Home</a>
				<?php if($loginLevel == '1'): ?> <a href='./records.php'>Records</a><?php endif; ?>
				<a href="./edit.php">Edit Profile</a>
				<a href="./time-in-daily.php">Time-In/Time-out - Daily</a>
				<a href="./time-in-weekly.php">Time-In/Time-out - Weekly</a>
				<a href="./time-in-monthly.php">Time-In/Time-out - Monthly</a>
				<?php if($loginLevel === '1'): ?> <a href="./overview.php">Time Record Audit</a><?php endif; ?>
				
				<?php if(!isset($_SESSION['login-name'])): ?>
				<a href='./login.php?log=1'>Login</a>
				<?php else: ?>
				<a href='./login.php?log=0'>Logout</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</header>
