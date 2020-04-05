<!DOCTYPE html>
<html>
<head>
<title>Date Time Record - Overview</title>
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
<div class="row">
	<div class="container">
		<div class="col-md-1">&nbsp;</div>
		<div class="col-md-10">
			<h2>Overview - Daily</h2>
		</div>
		<div class="col-md-1">&nbsp;</div>
	</div>
</div>
<?php
function cleanTxt($x) {
	$x = trim($x);
	$x = stripslashes($x);
	$x = htmlspecialchars($x);
	return $x;
}

?>
<div class="row">
	<div class="container">
		<div class="col-md-12">
			<br>
			<ul class="breadcrumb">
				<li>Overview - Daily</li>
				<li><a href="./overview-weekly.php">Overview - Weekly</a></li>
				<li><a href="#">Overview - Monthly</a></li>
			</ul>
			<br>
			<form method="post" id="overview-search" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<label for="inputTxt">Employee ID: </label>
				<select id="inputTxt" class="form-flex" name="emp" required>
					<option value="null" disabled selected>--- Select Employee ---</option>
<?php
	$listEmployee = "SELECT * FROM emp_accounts;";
	$employees = array();
	$queryListEmp = $myConn->query($listEmployee);
	if($queryListEmp->num_rows > 0) {
		while($row = $queryListEmp->fetch_assoc()) {
			$employees[$row['acctID']] = $row['acctID'] . ' : ' . $row['firstName'] . ' ' . $row['lastName'];
		}
	}
	
	if(isset($_POST['emp'])) {
		$employeeID = cleanTxt($_POST['emp']);  // use form selected employee id
	} elseif(isset($_GET['e'])) {
		$employeeID = cleanTxt($_GET['e']);  // use from different page variable employee id
	} else {
		$employeeID = $loginID;   // use default employee id which is your id
	}
	foreach($employees as $num => $empName) {
		if(!empty($employeeID) && $num == $employeeID) {
			echo "\t\t\t\t\t<option value='$num' selected>$empName</option>\n";
		} else {
			if($num == $loginID) {
				echo "\t\t\t\t\t<option value='$num' selected>$empName</option>\n";
			} else {
				echo "\t\t\t\t\t<option value='$num'>$empName</option>\n";
			}
		}
	}
	// We clean mysqli query results then also clear variable values. So it won't be used on codes below
	mysqli_free_result($queryListEmp);
	$employeeID = "";
	unset($employees);
?>
				</select><br><br>
				<label for="selYear">Year: </label>
				<select id="selYear" name="y" required>
					<option value="null" disabled selected>--- Select Year ---</option>
<?php
	// Dynamically get earliest available year in database
	$currentYear = date('Y');
	$fromYear = "";
	$sqlYear = "SELECT timeDateTime FROM emp_time ORDER BY timeDateTime ASC LIMIT 1;";
	$yearQuery = $myConn->query($sqlYear);
	if($yearQuery->num_rows > 0) {
		while($row = $yearQuery->fetch_assoc()) {
			$fromYear = date('Y', strtotime($row['timeDateTime']));
		}
	}
	$yearCtr = $currentYear - $fromYear;
	if(isset($_POST['y'])) {
		$fetchYear = cleanTxt($_POST['y']);
	} else {
		$fetchYear = date('Y');
	}
	for($x = $fromYear; $x <= $currentYear; $x++) {
		if(!empty($fetchYear)) {
			if($fetchYear==$x)
				echo "\t\t\t\t\t<option value='$x' selected>$x</option>\n";
		} else {
			if($x == date('Y')) {
				echo "\t\t\t\t\t<option value='$x' selected>$x</option>\n";
			} else {
				echo "\t\t\t\t\t<option value='$x'>$x</option>\n";
			}
		}
	}
	// We clean mysqli query results then also clear variable values. So it won't be used on codes below
	mysqli_free_result($yearQuery);
	$currentYear = $fromYear = $sqlYear = $yearCtr = $fetchYear = "";
?>
				</select>
				<label for="selMonth">Month: </label>
				<select id="selMonth" name="m" required>
					<option value="null" disabled selected>--- Select Month ---</option>
<?php
	// Auto populate month
	$months = array();
	for($x = 1; $x < 13; $x++) {
		$customDT = "2020-" . $x . "-01";
		$newMonth = date('F', strtotime($customDT));
		$months[$x] = $newMonth;
	}
	if(isset($_POST['m'])) {
		$fetchMonth = cleanTxt($_POST['m']);
	} else {
		$fetchMonth = date('m');
	}
	foreach($months as $num => $name) {
		if(!empty($fetchMonth) && $num == $fetchMonth) {
			echo "\t\t\t\t\t<option value='$num' selected>$name</option>\n";
		} else {
			if($num == date('n')) {
				echo "\t\t\t\t\t<option value='$num' selected>$name</option>\n";
			} else {
				echo "\t\t\t\t\t<option value='$num'>$name</option>\n";
			}
		}
	}
?>
				</select>
				<label for="selDay">Day: </label>
				<select id="selDay" name="d" required>
					<option value="null" disabled selected>--- Select Day ---</option>
<?php
	if(isset($_POST['m']) && isset($_POST['y']) && isset($_POST['d'])) {
		$setM = cleanTxt($_POST['m']);
		$setY = cleanTxt($_POST['y']);
		$setD = cleanTxt($_POST['d']);
	} else {
		$setM = date('m');
		$setY = date('Y');
		$setD = date('d');
	}
	$setMaxDay = date('t', strtotime($setY . '-' . $setM . '-01')); // 2020-01-01 or yyyy-mm-dd
	
	$days = array();
	for($x = 1; $x <= $setMaxDay; $x++) {
		$days[$x] = date('d', strtotime($setY . '-' . $setM . '-' . $x)); // 2020-01-x
	}
	foreach($days as $num => $name) {
		if($num == $setD) {
			echo "\t\t\t\t\t<option value='$num' selected>$name</option>\n";
		} else {
			echo "\t\t\t\t\t<option value='$num'>$name</option>\n";
			
		}
	}
?>
				</select><br><br>
				<input type="submit" class="btn form-flex btn-info" value="Search"><br>
				<button class="btn form-flex btn-info" id="print-btn" onclick="window.print()">Print</button>
			</form>
			<br>
		</div>
	</div>
</div>

<!-- Print Content Below -->
<?php 
	// use defaults when nothing is set
	$eID = $loginID;
	$oYear = date('Y');
	$oMonth = date('m');
	$oDay = date('d');
	
	if(isset($_POST['d'])) { // if form submit happen, use the specified values.
		$oDay = cleanTxt($_POST['d']);
		$oYear = cleanTxt($_POST['y']);
		$oMonth = cleanTxt($_POST['m']);
		$eID = cleanTxt($_POST['emp']);
	}
	if(isset($_GET['e'])) // if GET is found use this value instead
		$eID = cleanTxt($_GET['e']);
		
	
	$setDt = $oYear . "-" . $oMonth . "-" . $oDay;
	
	$overviewType = date('F jS Y', strtotime($setDt));
	$overviewType .= " Daily Summary";
	
	$userSQL = "SELECT * FROM emp_accounts WHERE acctID='$eID';";
	$userQuery = $myConn->query($userSQL);
	if($userQuery->num_rows > 0) {
		$row = mysqli_fetch_assoc($userQuery);
		$employee_ID = $row['acctID'];
		$employee_Name = $row['firstName'] . " " . $row['lastName'];
	}
	mysqli_free_result($userQuery);

?>

<?php if(!empty($overviewType) && !empty($employee_ID) && !empty($employee_Name)): ?>
<div class="print-area bottom-space">
	<div class="print-content">
		<div class="row">
			<div class="container">
				<div class="col-md-12">
					<h2><span id="overviewType"><?php echo $overviewType; ?></span></h2>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="container">
				<div class="col-md-12 col-flex">
					<p><strong>Employee ID: </strong> <u><?php echo $employee_ID; ?></u>&nbsp|&nbsp;</p>
					<p><strong>Employee Name: </strong> <u><?php echo $employee_Name; ?></u>&nbsp|&nbsp;</p>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="container">
				<div class="col-md-12">
					<!-- Result -->
<?php endif; ?>

<?php
	
	
	$weeklyMonthly = 0; // false 0 or true 1
	$dailyTimeInMsg = $dailyTimeOutMsg = $timeIn = $timeOut = "";
	
	$newDt = date('Y-m-d', strtotime($oYear . '-' . $oMonth . '-' . $oDay));
	$summarySQL = "SELECT empID, timeMode, DATE(timeDateTime) AS timeDate, timeDateTime FROM emp_time WHERE empID='$eID' AND DATE(timeDateTime)='$newDt';";
	$summaryQuery = $myConn->query($summarySQL);
	if($summaryQuery->num_rows > 0) {
		while($row = $summaryQuery->fetch_assoc()) {
			if($row['timeMode'] == '0') {
				$timeIn = date('H:i:s', strtotime($row['timeDateTime']));
				if(date('H:i:s', strtotime($row['timeDateTime'])) == '00:00:01') {
					$weeklyMonthly = 1; // If admin set monthly or weekly on employee it would show timeMode = 0 and timeDateTime = 00:00:01 in the database.
				} else {
					$weeklyMonthly = 0;
					$timeOutFrom = strtotime('17:00:00');
					$timeOutTo = strtotime('17:00:59');
					if(strtotime($row['timeDateTime']) >= $timeOutFrom && strtotime($row['timeDateTime']) <= $timeOutTo) {
						$dailyTimeInMsg = "";
					} else {
						$diff = strtotime($row['timeDateTime']) - $timeOutTo;
						$dailyTimeInMsg = "Late by " . $diff . " minutes!";
					}
				}
			} else {
				$timeOut = date('H:i:s', strtotime($row['timeDateTime']));
				if(!empty($timeIn)) {
					// If time in is set we assume that user logged using daily time in
					$weeklyMonthly = 0;
					$timeInFrom = strtotime('08:00:00');
					$timeOutTo = strtotime('08:00:59');
					if(strtotime($row['timeDateTime']) >= $timeInFrom && strtotime($row['timeDateTime']) <= $timeInFrom) {
						$dailyTimeOutMsg = "";
					} elseif (strtotime($row['timeDateTime']) <= $timeInFrom) {
						$diff = strtotime($row['timeDateTime']) - $timeInFrom;
						$dailyTimeOutMsg = "Early out by " . $diff . " minutes!";
					} else {
						$diff = strtotime($row['timeDateTime']) - $timeInFrom;
						$dailyTimeOutMsg = "Overtime by " . $diff . " minutes!";
					}
				}
				if(empty($timeIn) && $timeOut != '00:00:01') {
					$weeklyMonthly = 0;
					$dailyTimeOutMsg = "Did not time in";
				}
			}
		}
		$txtDt = strtotime($oYear . '-' . $oMonth . '-' . $oDay);
		echo "<table class='overview'>\n<tr>\n<th>Date</th>\n<th>Login Time</th>\n<th>Logout Time</th>\n<th>Remarks</th>\n</tr>"; // Create Table
		echo "<tr class='tbl-weekday'>";
		echo "<td>" . date('l, F d Y', strtotime($newDt)) . "</td>";
		// Present. Create table row
		if($weeklyMonthly == '0' && !empty($timeIn) && !empty($timeOut)) {
			if(empty($dailyTimeInMsg) || empty($dailyTimeOutMsg)) {
				echo "<td>" . date('H:i:s A' , strtotime($timeIn)) . "</td><td>" . date('H:i:s A' , strtotime($timeOut)) . "</td><td class='green'>No remarks</td>";
			} else {
				echo "<td>" . date('H:i:s A' , strtotime($timeIn)) . "</td><td>" . $timeOut . "</td><td class='red'>";
				if(empty($dailyTimeInMsg) && !empty($dailyTimeOutMsg)) {
					echo $dailyTimeOutMsg;
				}
				if(!empty($dailyTimeInMsg) && empty($dailyTimeOutMsg)) {
					echo $dailyTimeInMsg;
				}
				if(!empty($dailyTimeInMsg) && !empty($dailyTimeOutMsg)) {
					echo $dailyTimeInMsg . "<br>";
					echo $dailyTimeOutMsg . "<br>";
				}
				echo "</td>";
			}
		} else {
		//if($weeklyMonthly == '1' && empty($timeIn) && !empty($timeOut)) {
			if(empty($dailyTimeOutMsg)) {
				echo "<td>Not applicable</td><td>" . date('H:i:s A' , strtotime($timeOut)) . "</td><td class='green'>Set by admin</td>";
			} else {
				echo "<td>Not applicable</td><td>" . date('H:i:s A' , strtotime($timeOut)) . "</td><td class='red'>";
				echo $dailyTimeOutMsg;
				echo "</td>";
			}
		}
		echo "</tr></table>";
		mysqli_free_result($summaryQuery);
	} else {
		//$newDt = date('Y-m-d'); // else get today
		echo "<table class='overview'>\n<tr>\n<th>Date</th>\n<th>Login Time</th>\n<th>Logout Time</th>\n<th>Remarks</th>\n</tr>"; // Create Table
		if(date('N', strtotime($newDt)) >= 6) { // create row
			// weekend?
			echo "<tr class='tbl-weekend'><td>" . date('l, F d Y', strtotime($newDt)) . "</td>"; 
		} else {
			echo "<tr class='tbl-weekday'><td>" . date('l, F d Y', strtotime($newDt)) . "</td>"; 
		}
		
		if(date('N', strtotime($newDt)) >= 6) {
			// Must be weekend
			echo "<td>None</td><td>None</td><td>Weekend</td>";
		} else {
			// absent / no records
			echo "<td>None</td><td>None</td><td class='red'>Absent / No record</td>";
		}
		echo "</tr></table>";
	}
	
	if(isset($_GET['e'])) {
		// remove GET variable
		
	}
?>
						</div>
					</div>
				</div>
		</div>
	</div>
<!-- End of Print Format -->

<?php require("./require_footer.php"); ?>
<script>
var emp = document.getElementById("e");
var year = document.getElementById("y");
var month = document.getElementById("m");
var week = document.getElementById("w");
var printbtn = document.getElementById("print-btn");

showPrint();

week.onchange = function() {
	if(isNull == true) {
		showPrint();
	}
};
month.onchange = function() {
	if(isNull == true) {
		showPrint();
	}
};
week.onchange = function() {
	if(isNull == true) {
		showPrint();
	}
};
printbtn.onclick = function(e) {
	e.preventDefault();
};

function showPrint() {
	if(emp == "null" && year == "null" && month == "null" && week == "null") {
		printbtn.disabled = true;
	} else {
		printbtn.disabled = false;
	}
}
function isNull() {
	if(year == "null" || month == "null" || week == "null") {
		return false;
	} else {
		return true;
	}
}

/* Prevent form submission alert box since our form uses POST method */
document.getElementById('overview-search').submit = function(e) {
	e.preventDefault();
	window.history.back();
};
document.getElementById('overview-search').onsubmit = function(e) {
	e.preventDefault();
	window.history.back();
};
</script>
</body>
</html>