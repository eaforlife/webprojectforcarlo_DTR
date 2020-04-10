<!DOCTYPE html>
<!-- Use of CSS Grid: https://www.w3schools.com/css/css_grid.asp -->
<html>
<head>
<title>Date Time Record - Edit Profile</title>
<meta name='viewport' content='width=device-width, initial-scale=1.0'> <!-- Call viewport for responsive html css: https://www.w3schools.com/html/html_responsive.asp -->
<link rel="stylesheet" type="text/css" href="./style/style.css">
<link rel="stylesheet" type="text/css" href="./style/style-1.css">
</head>
<body>

<!-- header -->
<?php 
	require("./require_header.php");
 ?>

<div class="container bottom-space">
<!-- content here -->
<?php
require("./myCon.php");

$empID = $loginID;
$empName = $loginName;
$empStatus = "";
if(isset($_GET['e'])) {
	$eID = $_GET['e'];
	$eID = trim($eID);
	$eID = htmlspecialchars($eID);

	$empQuery = "SELECT * FROM emp_accounts WHERE acctID='$eID' LIMIT 1;";
	$empRslt = $myConn->query($empQuery);
	if($empRslt->num_rows > 0) {
		while($row = $empRslt->fetch_assoc()) {
			$empID = $row['acctID'];
			$empName = $row['lastName'] . ", " . $row['firstName'];
		}
	}
}
?>
<div class="row">
	<div class="container">
		<div class="col-md-1">&nbsp;</div>
		<div class="col-md-10">
			<h2>Modify Employee</h2>
		</div>
		<div class="col-md-1">&nbsp;</div>
	</div>
</div>

<div class="row">
	<div class="container">
		<div class="col-md-1">&nbsp;</div>
		<div class="col-md-5">
			<form method="post">
				<label for="empID">Employee ID: </label>
				<input type="text" id="empID" name="empID" value="<?php echo $empID; ?>" disabled><br>
				<label for="empName">Employee Name: </label>
				<input type="text" id="empName" value="<?php echo $empName; ?>" disabled><br>
				<label for="empStatus">Status: </label>
				<input type="text" id="empStatus" value="<?php echo $empStatus; ?>" disabled><br>
				<label for="empOldPass">Old Password: </label>
				<input type="password" id="empOldPass" name="oldpass" value="" autocomplete="current-password"><br>
				<label for="empPass1">Old Password: </label>
				<input type="password" id="empPass1" name="pass1" value="" autocomplete="new-password"><br>
				<label for="empPass2">Old Password: </label>
				<input type="password" id="empPass2" name="pass2" value="" autocomplete="new-password"><br>
			</form>
		</div>
		<div class="col-md-5">
			
		</div>
		<div class="col-md-1">&nbsp;</div>
	</div>
</div>

<?php 
	require("./require_footer.php");
 ?>
<script>
var empID = document.getElementById("empID");
var empName = document.getElementById("empName");
var empStatus = document.getElementById("empStatus");

function editRow(x) {
	window.location.href = './edit.php?e=' + x.toString();
}
</script>
</body>
</html>