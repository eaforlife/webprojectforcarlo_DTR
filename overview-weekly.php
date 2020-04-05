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
				<li><a href="./overview.php">Overview - Daily</a></li>
				<li>Overview - Weekly</li>
				<li><a href="#">Overview - Monthly</a></li>
			</ul>
			<br>
			<form method="post" id="overview-search" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<label for="inputTxt">Employee ID: </label>
				<select id="inputTxt" class="form-flex" name="emp" required>
					<option value="null" disabled selected>--- Select Employee ---</option>
<?php
	/* Let's just make it dropdown menu. You may also use textbox or datalist for textbox with autocomplete */
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
	// Dynamically get first available year in database
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
	// Auto populate month. 
	$months = array();
	for($x = 1; $x < 13; $x++) {
		$customDT = "2020-" . $x . "-01";
		$newMonth = date('F', strtotime($customDT));
		$months[$x] = $newMonth;
	}
	if(isset($_POST['m'])) {
		$fetchMonth = cleanTxt($_POST['m']);
	} else {
		$fetchMonth = date('n');
	}
	
	foreach($months as $num => $name) {
		if($fetchMonth == $num) {
			echo "\t\t\t\t\t<option value='$num' selected>$name</option>\n";
		} else {
			echo "\t\t\t\t\t<option value='$num'>$name</option>\n";
		}
	}
?>
				</select>
				<label for="selDay">Week: </label>
				<select id="selDay" name="w" required>
					<option value="null" disabled selected>--- Select Week ---</option>
<?php
// Dynamically set number of week in a year in a month.
$frmWeek = date('m');
$toWeek = date('t');
$weekYear = date('Y');

$postWeek = "";

if(isset($_POST['m']) && isset($_POST['y'])) {
	$frmWeek = cleanTxt($_POST['m']);
	$weekYear = cleanTxt($_POST['y']);
	$toWeek = date('t', strtotime($weekYear . '-' . $frmWeek . '-01'));
}
if(isset($_POST['w'])) {
	$postWeek = cleanTxt($_POST['w']);
} else {
	$postWeek = 0;
}
$ctrWeek = ceil($toWeek / 7);
for($x = 0; $x < $ctrWeek; $x++) {
	if($x == $postWeek && !empty($postWeek))
		echo "\t\t\t\t\t<option value='$x' selected>Week " . ($x + 1) . "</option>\n";
	elseif(empty($postWeek) && $x == 0)
		echo "\t\t\t\t\t<option value='$x' selected>Week " . ($x + 1) . "</option>\n";
	else
		echo "\t\t\t\t\t<option value='$x'>Week " . ($x + 1) . "</option>\n";
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
	$oWeek = $postWeek;
	
	if(isset($_POST['w'])) { // if form submit happen, use the specified values.
		$oWeek = cleanTxt($_POST['w']);
		$oYear = cleanTxt($_POST['y']);
		$oMonth = cleanTxt($_POST['m']);
		$eID = cleanTxt($_POST['emp']);
	}
	if(isset($_GET['e'])) // if GET is found use this value instead
		$eID = cleanTxt($_GET['e']);
		
	
	$setDt = $oYear . "-" . $fetchMonth . "-01";
	
	$overviewType = date('F Y', strtotime($setDt));
	$overviewType .= " Week " . ($oWeek + 1) . " Summary";
	
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
	$dailyTimeInMsg = $dailyTimeOutMsg = "";
	$postDate = date('Y-m-d', strtotime($oYear . '-' . $fetchMonth . '-01')); // set generic date of the month
	
	// Set week start and end
	$weekDayNum = intval(date('w', strtotime($postDate))); // get php week day number 0 - 6 where 0 is sunday and 6 is saturday
	$weekNumEnd = date('d', strtotime($postDate . ' +' . (6-$weekDayNum) . ' days')) + ($postWeek * 7); // get end day of each week in a month.
	//$weekNumStart = date('d', strtotime($postDate . ' -' . $weekDayNum . ' days')) + ($postWeek * 7); // start date. uncomment if you want to use this instead
	if($postWeek == '4') {
		// if form selects week 5 then do these instead.
		$weekNumEnd = intval(date('t', strtotime($postDate)));
		$counterDay = intval(date('d', strtotime($postDate . ' +' . (6-$weekDayNum) . ' days')) + (3 * 7)) + 1;
	} else {
		// otherwise do defaults
		$counterDay = $weekNumEnd;
	}
	$getWeekYear = date('W', strtotime($oYear . '-' . $fetchMonth . '-' . $weekNumEnd)); // base on the end day of the week we get the week number in a year
	$weekNumMonth = date('m', strtotime($oYear . '-' . $fetchMonth . '-' . $weekNumEnd)); // This will be used to compare during loop
	
	$timeIn = array();
	$timeOut = array();
	
	echo "<table class='overview'>\n<tr>\n<th>Date</th>\n<th>Login Time</th>\n<th>Logout Time</th>\n<th>Remarks</th>\n</tr>\n\n"; // Create Table
	for($x = 1; $x <= 7; $x++) {
		$timeIn[$x] = "NULL";
		$timeOut[$x] = "NULL";
		$newDt = date('Y-m-d', strtotime($oYear . '-' . $fetchMonth . '-' . $counterDay)); // get week number in a year. Format ISO-8601
		/*
		* Why do while instead of MySQLI's fetch assoc to grab the dates?   *
		* Well we need to set all the dates in the calendar, ignoring the   *
		* fact that a user might not be preset on a specific date.          *
		* This will make sure that the table will be in standard format     *
		* instead of missing dates on different employees.                  *
		*																	*
		* I prefer do while or while loop for this instead of the for loop. *
		* Originally I had set the loop to stop when the date falls in the  *
		* next month. But the codes are too long now to adjust this.        */
		
		
		/* Define the current month */
		$checkMonthofWeek = date('m', strtotime($oYear . '-' . $fetchMonth . '-' . $counterDay));
		if($checkMonthofWeek != $weekNumMonth) {
			/* This will check if month is different and will omit dates in the week.   *
			*  Since MySQL or PHP does not have the ability to show weeks in a month,   *
			*  we instead use PHP's date('W') as well as WEEK(<date>,3) to get the week *
			*  in a year in ISO-8601 format. MySQL supports many week type but PHP only *
			*  has 1.    																*
			*  Since the ISO-8601 format week's start at Monday we will ignore the 1st  *
			*  of the next week to show the Sunday. The do while condition takes care   *
			*  of possible overflow of days.                                            *
			*																		    *
			*  In short this is a workaround to get proper weekly reports. And this is  *
			*  why we prefer to use to get the last day of a week instead of the start. */
			$timeIn[$x] = "NULL";
			$timeOut[$x] = "NULL";
		} else {
			$summarySQL = "SELECT empID, timeMode, DATE(timeDateTime) AS timeDate, timeDateTime FROM emp_time WHERE empID='$eID' AND WEEK(timeDateTime, 3)='$getWeekYear' AND DATE(timeDateTime)='$newDt' LIMIT 2"; // sql to get week date. 
			
			$summaryQuery = $myConn->query($summarySQL);
			if($summaryQuery->num_rows > 0) {
				while($row = $summaryQuery->fetch_assoc()) {
					if($row['timeMode'] == '0') {
						$timeIn[$x] = date('H:i:s', strtotime($row['timeDateTime']));
					} else {
						$timeOut[$x] = date('H:i:s', strtotime($row['timeDateTime']));
					}
				}
			} else {
				if(date('N', strtotime($newDt)) >= 6) {
					$timeIn[$x] = 'WEEKEND';
					$timeOut[$x] = 'WEEKEND';
				} else {
					$timeIn[$x] = 'empty';
					$timeOut[$x] = 'empty';
				}
			}
			
			
		}
		
		if(empty($timeIn) && empty($timeOut)) {
			/* Do this if nothing has been set */
			echo "<tr>\n";
			echo "<td>&nbsp;</td>\n<td>&nbsp;</td>\n<td>&nbsp;</td>\n<td>&nbsp;</td>\n";
			echo "</tr>\n";
		} else {
				$tableDate = date('M j Y (D)', strtotime($oYear . '-' . $fetchMonth . '-' . $counterDay)); // Convert to readable format
				if($timeIn[$x] == 'NULL' && $timeOut[$x] == 'NULL') {
					echo "<tr>\n";
					echo "<td>&nbsp;</td>\n<td>&nbsp;</td>\n<td>&nbsp;</td>\n<td>&nbsp;</td>\n";
					echo "</tr>\n";
				} elseif ($timeIn[$x] == 'NULL' && $timeOut[$x] != 'NULL' && $timeIn[$x] != 'empty' && $timeOut[$x] != 'empty') {
					if($timeOut[$x] == '00:00:01') {
						echo "<tr class='tbl-weekday'>\n";
						echo "<td>$tableDate</td>\n<td>Not applicable</td>\n<td>" . $timeOut[$x] . "</td>\n<td class='green'>Set by admin</td>\n";
						echo "</tr>\n";
					} else {
						echo "<tr class='tbl-weekday'>\n";
						echo "<td>$tableDate</td>\n<td>&nbsp;</td>\n<td>" . $timeOut[$x] . "</td>\n<td class='red'>Did not time out!</td>\n";
						echo "</tr>\n";
					}
				} elseif ($timeIn[$x] != 'NULL' && $timeOut[$x] == 'NULL' && $timeIn[$x] != 'empty' && $timeOut[$x] != 'empty') {
					echo "<tr class='tbl-weekday'>\n";
					echo "<td>$tableDate</td>\n<td>&nbsp;</td>\n<td>" . $timeIn[$x] . "</td>\n<td class='red'>Did not time in!</td>\n";
					echo "</tr>\n";
				} elseif($timeIn[$x] != 'NULL' && $timeOut[$x] != 'NULL' && $timeIn[$x] != 'WEEKEND' && $timeIn[$x] != 'empty' && $timeOut[$x] != 'empty') {
					echo "<tr class='tbl-weekday'>\n";
					echo "<td>" . $tableDate . "</td>\n";
					echo "<td>" . $timeIn[$x] . "</td>\n";
					echo "<td>" . $timeOut[$x] . "</td>\n";
					
					if($timeIn[$x] >= '07:59:00' && $timeIn[$x] <= '08:01:00' && $timeOut[$x] >= '17:00:00' && $timeOut <= '17:01:00')
						echo "<td class='green'>No Remarks</td>\n";
					elseif(strtotime($timeIn[$x]) >= strtotime('08:01:01') && $timeOut[$x] >= '17:00:00' && $timeOut <= '17:01:00')
						echo "<td class='red'>Late by " . round(((abs(strtotime($timeIn[$x]) - strtotime('08:01:01'))) / 60) / 60) . " minutes</td>\n";
					elseif(strtotime($timeIn[$x]) <= strtotime('07:58:59') && $timeOut[$x] >= '17:00:00' && $timeOut <= '17:01:00')
						echo "<td class='red'>Early by " . round(((abs(strtotime($timeIn[$x]) - strtotime('08:01:01'))) / 60) / 60) . " minutes</td>\n";
					elseif($timeIn[$x] >= '07:59:00' && $timeIn[$x] <= '08:01:00' && strtotime($timeOut[$x]) >= '17:01:01')
						echo "<td class='red'>Overtime by " . round(((abs(strtotime($timeIn[$x]) - strtotime('08:01:01'))) / 60) / 60) . " minutes</td>\n";
					elseif($timeIn[$x] >= '07:59:00' && $timeIn[$x] <= '08:01:00' && strtotime($timeOut[$x]) <= '16:59:59')
						echo "<td class='red'>Early out by " . round(((abs(strtotime($timeIn[$x]) - strtotime('08:01:01'))) / 60) / 60) . " minutes</td>\n";
					elseif(strtotime($timeIn[$x]) >= strtotime('08:01:01') && strtotime($timeOut[$x]) >= '17:01:01')
						echo "<td class='red'>Late by " . round(((abs(strtotime($timeIn[$x]) - strtotime('08:01:01'))) / 60) / 60) . " minutes<br>Overtime by " . round(((abs(strtotime($timeIn[$x]) - strtotime('08:01:01'))) / 60) / 60) . " minutes</td>\n";
					
					else
						echo "<td class='red'>Early by " . round(((abs(strtotime($timeIn[$x]) - strtotime('08:01:01'))) / 60) / 60) . " minutes<br>Early out by " . round(((abs(strtotime($timeIn[$x]) - strtotime('08:01:01'))) / 60) / 60) . " minutes</td>\n";
					
					echo "</tr>\n";
				} elseif($timeIn[$x] == 'WEEKEND' && $timeOut[$x] == 'WEEKEND' && $timeIn[$x] != 'empty' && $timeOut[$x] != 'empty')  {
					echo "<tr class='tbl-weekend'>\n";
					echo "<td>$tableDate</td>\n<td>&nbsp;</td>\n<td>&nbsp;</td>\n<td>Weekend</td>\n";
					echo "</tr>\n";
				} else {
					echo "<tr class='tbl-weekday'>\n";
					echo "<td>$tableDate</td>\n<td>&nbsp;</td>\n<td>&nbsp;</td>\n<td class='red'>No record or Absent</td>\n";
					echo "</tr>\n";
				}
			
		}
		if($postWeek == '4')
			$counterDay++;
		else
			$counterDay--;
	}
	echo "</table>\n";
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