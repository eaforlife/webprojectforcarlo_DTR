<!DOCTYPE html>
<html>
<head>
<title>Date Time Record - Overview - Weekly</title>
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
			<h2>Overview</h2>
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
// fetch GET data if set: employee ID, year, month

if(isset($_GET['e'])) {
	$employeeID = $fetchYear = $fetchMonth = "";
	$employeeID = cleanTxt($_GET['e']);
	
	if(isset($_GET['m']) && isset($_GET['y'])) {
		$fetchYear = cleanTxt($_GET['y']);
		$fetchMonth = cleanTxt($_GET['m']);
	}
}

?>
<div class="row">
	<div class="container">
		<div class="col-md-12">
			<br>
			<form method="get">
				<label for="inputTxt">Employee ID: </label>
				<input type="text" class="form-flex" id="inputTxt" name="e" placeholder="Employee ID" <?php if(!empty($employeeID)) { echo "value='$employeeID' required"; } else { echo "required"; } ?>><br>
				<label for="selYear">Year: </label>
				<select id="selYear" name="y" required>
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
						
						for($x = $fromYear; $x <= $currentYear; $x++) {
							if(!empty($fetchYear)) {
								if($fetchYear==$x)
									echo "<option value='$x' selected>$x</option>\n";
							} else {
								echo "<option value='$x'>$x</option>\n";
							}
						}
						
					?>
					<option value="null" disabled<?php if(empty($fetchYear)) { echo " selected"; } ?>>--- Select Year ---</option>
				</select>&nbsp | &nbsp;
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
	foreach($months as $num => $name) {
		if($num == $fetchMonth) {
			echo "\t\t\t\t\t<option value='$num' selected>$name</option>\n";
		} else {
			echo "\t\t\t\t\t<option value='$num'>$name</option>\n";
		}
	}
?>
				</select>
				<label for="selWeek">Week: </label>
				<select id="selWeek" name="w" required>
					<option value="all"<?php if(!isset($_GET['w'])) { echo " selected"; } ?>>All</option>
					<option value="0"<?php if(isset($_GET['w']) && $_GET['w'] == '0') { echo " selected"; } ?>>Week 1</option>
					<option value="1"<?php if(isset($_GET['w']) && $_GET['w'] == '1') { echo " selected"; } ?>>Week 2</option>
					<option value="2"<?php if(isset($_GET['w']) && $_GET['w'] == '2') { echo " selected"; } ?>>Week 3</option>
					<option value="3"<?php if(isset($_GET['w']) && $_GET['w'] == '3') { echo " selected"; } ?>>Week 4</option>
					<option value="4"<?php if(isset($_GET['w']) && $_GET['w'] == '4') { echo " selected"; } ?>>Week 5</option>
					<option value="null"<?php if(!isset($_GET['w'])) { echo " selected"; } ?> disabled>--- Select Week ---</option>
				</select><br>
				<input type="submit" class="btn btn-lg btn-info" value="Search">
				<button class="btn btn-lg btn-info" id="print-btn">Print</button>
			</form>
			<br>
		</div>
	</div>
</div>

<!-- Print Content Below -->
<?php 
if(isset($_GET['e']) && isset($_GET['m']) && isset($_GET['y']) && isset($_GET['w'])) {
	// Fetch user details
	
	$srch = cleanTxt($_GET['e']);
	$year = cleanTxt($_GET['y']);
	$month = cleanTxt($_GET['m']);
	$week = cleanTxt($_GET['w']);
	
	$setDt = $year . "-" . $month . "-01";
	
	if($week == 'all') {
		$overviewType = "Month of " . date('F', strtotime($setDt));
	} else {
		$week += 1;
		$overviewType = "Month of " . date('F', strtotime($setDt)) . ", Week " . $week;
	}
	$overviewType .= " Summary";
	
	$userSQL = "SELECT * FROM emp_accounts WHERE acctID='$srch';";
	$userQuery = $myConn->query($userSQL);
	if($userQuery->num_rows > 0) {
		while($row = $userQuery->fetch_assoc()) {
			$employee_ID = $row['acctID'];
			$employee_Name = $row['firstName'] . " " . $row['lastName'];
		}
	}
}
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
					<!-- Week -->
<?php endif; ?>
<?php

	/*if(isset($_POST['search']) && isset($_POST['year']) && isset($_POST['month']) && isset($_POST['week']))*/
if(isset($_GET['e']) && isset($_GET['m']) && isset($_GET['y']) && isset($_GET['w'])){
	
	echo "<table class='overview'>\n<tr>\n<th>Date</th>\n<th>Login Time</th>\n<th>Logout Time</th>\n<th>Remarks</th>\n</tr>"; // Create Table
	
	$srch = cleanTxt($_GET['e']);
	$year = cleanTxt($_GET['y']);
	$month = cleanTxt($_GET['m']);
	$week = cleanTxt($_GET['w']);
	
	$setDt = $year . "-" . $month . "-01";
	$date = date('Y-m-d', strtotime($setDt));
	
	if($week == 'all') {
		$maxCtr = intval(date('t', strtotime($date))) - 1; // convert to int
		$stringdt = $year . "-" . $month . "-" . $maxCtr;
		$getDate = date('Y-m-d', strtotime($stringdt));
	} else {
		$maxCtr = 7;
		switch($week) {
			case '0':
				$setDt = $year . "-" . $month . "-01";
				$weekDayNum = date('w', strtotime($setDt));
				$weekNumEnd = date('d', strtotime($setDt . ' +' . (6-$weekDayNum) . ' days'));
				$date = date('Y-m-d', strtotime($year . "-" . $month . "-" . $weekNumEnd));
				break;
			case '1':
				$setDt = $year . "-" . $month . "-01";
				$weekDayNum = date('w', strtotime($setDt));
				$weekNumEnd = date('d', strtotime($setDt . ' +' . (6-$weekDayNum) . ' days')) + 7;
				$date = date('Y-m-d', strtotime($year . "-" . $month . "-" . $weekNumEnd));
				break;
			case '2':
				$setDt = $year . "-" . $month . "-01";
				$weekDayNum = date('w', strtotime($setDt));
				$weekNumEnd = date('d', strtotime($setDt . ' +' . (6-$weekDayNum) . ' days')) + 14;
				$date = date('Y-m-d', strtotime($year . "-" . $month . "-" . $weekNumEnd));
				break;
			case '3':
				$setDt = $year . "-" . $month . "-01";
				$weekDayNum = date('w', strtotime($setDt));
				$weekNumEnd = date('d', strtotime($setDt . ' +' . (6-$weekDayNum) . ' days')) + 21;
				$date = date('Y-m-d', strtotime($year . "-" . $month . "-" . $weekNumEnd));
				break;
			case '4':
				$setDt = $year . "-" . $month . "-01";
				$weekDayNum = date('w', strtotime($setDt));
				$weekNumEnd = date('d', strtotime($setDt . ' +' . (6-$weekDayNum) . ' days')) + 28;
				$date = date('Y-m-d', strtotime($year . "-" . $month . "-" . $weekNumEnd));
				break;
			case '5':
				$setDt = $year . "-" . $month . "-01";
				$weekDayNum = date('w', strtotime($setDt));
				$weekNumEnd = date('d', strtotime($setDt . ' +' . (6-$weekDayNum) . ' days')) + date('t', strtotime($setDt));
				$date = date('Y-m-d', strtotime($year . "-" . $month . "-" . $weekNumEnd));
				break;
			default:
				$setDt = $year . "-" . $month . "-01";
				$weekDayNum = date('w', strtotime($setDt));
				$weekNumEnd = date('d', strtotime($setDt . ' +' . (6-$weekDayNum) . ' days'));
				$date = date('Y-m-d', strtotime($year . "-" . $month . "-" . $weekNumEnd));
		}
		$weekNum = date('W', strtotime($date));
		$weekNum = intval($weekNum);
		$getDate = $date; // then we get full week by adding 7 addings or +6 on strtotime()
	}
	
	$x = 0; // counter to use instead of for loop. we will use while loop so we can break the loop when the week ends on a different month.
	do {
		// loops through the days of the week
		$y = $maxCtr - $x;
		$y = $y - 1;
		$weekStr = date('Y-m-d', strtotime($getDate . ' -' . $y . ' days')); //'2020-03-' . $x;
		
		// Set SQL query
		if($week == 'all')
			$sql = "SELECT * FROM emp_accounts JOIN emp_time ON emp_accounts.acctID=emp_time.empID WHERE emp_accounts.acctID='$srch' AND MONTH(emp_time.timeDateTime) = MONTH('$getDate') AND YEAR(emp_time.timeDateTime) = YEAR('$getDate') AND DATE(emp_time.timeDateTime) = '$weekStr';";
		else
			$sql = "SELECT * FROM emp_accounts JOIN emp_time ON emp_accounts.acctID=emp_time.empID WHERE emp_accounts.acctID='$srch' AND MONTH(emp_time.timeDateTime) = MONTH('$getDate') AND YEAR(emp_time.timeDateTime) = YEAR('$getDate') AND WEEK(emp_time.timeDateTime,3) = '$weekNum' AND DATE(emp_time.timeDateTime) = '$weekStr';";
		
		if(date('m', strtotime($getDate)) != date('m', strtotime($weekStr)))
			break; // stop loop if month is different
		
		$sqlQuery = $myConn->query($sql);
		if(date("N", strtotime($weekStr)) >= 6) {
			// Weekend do this. in PHP 5.1> = Week starts from 1-7 where 6 is Saturday and 7 is Sunday! Otherwise 0 is Sunday.
			
			echo "<tr class='tbl-weekend'>\n";
			echo "<td>" . date("M j Y (D)", strtotime($weekStr)) . "</td>\n";
			echo "<td>None</td>\n";
			echo "<td>None</td>\n";
			echo "<td>Weekend</td>\n";
			echo "</tr>\n";
			
		} else {
			// If it's not weekend then do this
			echo "<tr class='tbl-weekday'>";
			if($sqlQuery->num_rows > 0) {
				$timeIn = $timeOut = "";
				echo "<td>" . date("M j Y (D)", strtotime($weekStr)) . "</td>\n";
				while($row = $sqlQuery->fetch_assoc()) {
					if($row['timeMode'] == '0')
						$timeIn = date("G:i:s A", strtotime($row['timeDateTime']));
					if($row['timeMode'] == '1')
						$timeOut = date("G:i:s A", strtotime($row['timeDateTime']));
					
				}
				if(!empty($timeIn))
					echo "<td>$timeIn</td>\n";
				else
					echo "<td>n/a</td>\n";
				
				echo "<td>$timeOut</td>\n";
				// Set remarks
				// If late 
				$err = array();
				$msg = array();
				if(!empty($timeIn) && !empty($timeOut)) {
					$setFromTime = strtotime("07:59:00am");
					$setToTime = strtotime("08:00:59am");
					if(strtotime($timeIn) >= $setFromTime && strtotime($timeIn) <= $setToTime) {
						$msg[1] = "<p class='txt-success'>On Time</p>\n";
					} else {
							$timeInLate = (strtotime($timeIn) - $setToTime) / 60;
							$timeInLate = ceil($timeInLate);
							$err[0] = "<p class='txt-danger'>Late by: - $timeInLate minutes.</p>\n";
						
					}
				} else {
					$msg[0] = "<p class='txt-success'>Set by admin</p>\n";
					
					
				}
				if(!empty($timeOut) && !empty($timeIn)) {
					$setFromTime = strtotime("05:00:00pm");
					$setToTime = strtotime("05:00:59pm");
					if(strtotime($timeOut) >= $setFromTime && strtotime($timeOut) <= $setToTime) {
						$msg[2] = "<p class='txt-success'>On Time</p>\n";
					} else {
						if(empty($timeIn) && strtotime($timeOut) != strtotime('00:00:01')) {
							$err[1] = "<p class='txt-danger'>Did not time in/out</p>\n";
						} else {
							$timeOutLate = (strtotime($timeIn) - $setToTime) / 60;
							$timeOutLate = ceil($timeOutLate);
							$err[1] = "<p class='txt-danger'>Overtime by: - $timeOutLate minutes.</p>\n";
						}
						
					}
				} else {
					$msg[0] = "<p class='txt-success'>Set by admin</p>\n";
				}
				
				echo "<td>\n";
				if(array_key_exists(0, $msg)) {
					echo $msg[0];
				}
				if(array_key_exists(1, $msg)) {
					echo $msg[1];
				} else {
					if(array_key_exists(0,$err)) {
						echo $err[0];
					} elseif(array_key_exists(2, $err)) {
						echo $err[2];
					}
				}
				
				if(array_key_exists(2, $msg)) {
					echo $msg[2];
				} else {
					if(array_key_exists(1,$err)) {
						echo $err[1];
					}
				}
				
				echo "</td>\n";
				
			} else {
				echo "<td>" . date("M j Y (D)", strtotime($weekStr)) . "</td>\n";
				echo "<td>None</td>\n";
				echo "<td>None</td>\n";
				echo "<td class='red'><p>Absent/No record</p></td>\n";
			}
			echo "</tr>\n";
		}
		$x += 1;
	} while($x < $maxCtr);
	echo "</table>"; // close table
} else {
	// Not set form
	echo "<h2>Select and input fields above this message.</h2>";
}
echo "<br><p class='print-generated'>Generated at: " . date('l, F jS Y - H:i:s') . "</p>";
echo "<p class='print-generated'>Generated by: " . $loginID . "</p>";
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
printbtn.onclick = function() {
	window.print();
}

function showPrint() {
	if(emp == "" && year == "null" && month == "null" && week == "null") {
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
</script>
</body>
</html>