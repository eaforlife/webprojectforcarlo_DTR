<!DOCTYPE html>
<html>
<head>
<title>Date Time Record - Time In - Weekly</title>
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

<?php
	$ctr = 0;
	$outMsg = "";
	$active = 1;
	
	// Check if today has been set by monthly or weekly login
	$now = date('Y-m-d');
	$checkDate = "SELECT * FROM emp_time WHERE empID='$loginID' AND DATE(timeDateTime)='$now';";
	$checkDateQuery = $myConn->query($checkDate);
	if($checkDateQuery->num_rows > 0) {
		$active = 0;
	} else {
		$active = 1;
	}
	
	function isWeekend($date) {
		// 6 = Saturday, 0 = Sunday
		$weekDay = date('w', strtotime($date));
		if($weekDay === '0' || $weekDay === '6') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	function cleanTxt($x) {
		$x = trim($x);
		$x = stripslashes($x);
		$x = htmlspecialchars($x);
		return $x;
	}
	
	if(isset($_POST['date']) && isset($_POST['days']) && isset($_POST['empListId'])) {
		
		$selDays = cleanTxt($_POST['days']); // 7
		$selDate = $_POST['date'] . " 00:00:01";
		$empID = cleanTxt($_POST['empListId']);
		
		if(!empty($selDate) && !empty($selDays)) {
			for($x = 0; $x <= $selDays - 1; $x++) {
				// If weekend add 2 days
				$setDT = date('Y-m-d H:i:s', strtotime($selDate . ' -' . $x . ' day'));
				$y = $x;
				if(isWeekend($setDT) === TRUE) {
					$y = $x + 2;
				}
				$currDate = date('Y-m-d H:i:s', strtotime($selDate . ' -' . $y . ' day'));
				
				$sqlDT = "SELECT * FROM emp_time WHERE empID='$empID' AND DATE(timeDateTime)=DATE('$currDate');";
				$queryDT = $myConn->query($sqlDT);
				if($queryDT->num_rows > 0) {
					$ctr += 1;
				} else {
					$addDt = "INSERT INTO emp_time VALUES(NULL, '$empID', '1', '$currDate');";
					if($myConn->query($addDt) === TRUE) {
						$outMsg = "<div id='results-success'><br><hr><br><p><span class='txt-success' id='results'>Successfully addded days for employee ID: $empID!</span></p></div>";
					} else {
						$ctr += 1;
					}
					
				}
				
				if($ctr > 0) {
					$outMsg = "<div id='results-err'><br><hr><br><p class='txt-danger'>One or more fields already exists. So only adding date which do not exists.</p></div>";
				}
				
			}
			
		}
	}
?>

<div class="container">
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6 justify-center">
		<h2>Login - Weekly</h2>
		
		</div>
		<div class="col-md-3"></div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8 justify-center">
		<p>Hello <?php echo $loginName; ?>!</p>
		<form method="post" id="frmIn">
			<label for="empList">Employee:</label>
			<select id="empList" name="empListId" class="form-flex" required>
			<?php 
				$empSQL = "SELECT acctID, lastName, firstName FROM emp_accounts;";
				$empQuery = $myConn->query($empSQL);
				if($empQuery->num_rows > 0) {
					while($row = $empQuery->fetch_assoc()) {
						$empID = $row['acctID'];
						$lname = $row['lastName'];
						$fname = $row['firstName'];
						if($empID == $loginID) {
							$output = $empID . " - " . $fname . " << YOURS >>";
							echo "\t\t\t\t<option value='$empID' selected>$output</option>\n";
						} else {
							$output = $empID . " - " . $fname . " " . $lname;
							echo "\t\t\t\t<option value='$empID'>$output</option>\n";
						}
					}
				}
			?>
			</select><br>
			<label for="fromDate">From: </label>&nbsp;<input type="date" name="date" id="dateDate" min="2019-01-01" max="<?php $frmNowDT = date('Y-m-d'); echo date('Y-m-d', strtotime($frmNowDT . '-1 day')); ?>" value="<?php $frmNowDT = date('Y-m-d'); echo date('Y-m-d', strtotime($frmNowDT . '-1 day')); ?>" required><br>
			<label for="dateDial">Days(1-7): </label>&nbsp;<input type="number" name="days" id="numDays" min="1" max="7" step="1" value="1" required><br><br>
			<input type="submit" class="btn btn-lg btn-success" id="checkIn" value="Time In - Weekly"></button>
			<input type="reset" class="btn btn-lg" value="Reset">
		</form>
		<?php echo $outMsg; ?>
		
		</div>
		<div class="col-md-2"></div>
	</div>
</div>
<hr>
<div class="container">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8 justify-center">
		<h4>History:</h4>
		<form id="historyForm" class="col-flex">
			<label for="sortMonth">Sort By: Month: </label>&nbsp;
			<select id="sortMonth">
				<option value="all" selected>All</option>
			<?php
				// Auto populate month
				$months = array();
				
				for($x = 0; $x < 12; $x++) {
					$setTime = mktime(0,0,0,date('n') - $x,1);
					$months[date('n', $setTime)] = date('F', $setTime);
				}
				
				foreach($months as $num => $name) {
					echo "<option value='$num'>$name</option>\n";
				}
			?>
			</select>&nbsp;
			<label for="sortStyle">Sort: </label>&nbsp;
			<select id="sortStyle">
				<option value="ASC">Ascending</option>
				<option value="DESC" selected>Descending</option>
			</select>
		</form>
		<br>
		<div id="empHistory"></div>
		</div>
		<div class="col-md-2"></div>
	</div>
</div>
<br>


<?php 
	include("./require_footer.php");
 ?>

<script>
historyAjax();

document.getElementById("sortMonth").onchange = function() {
	historyAjax();
};

document.getElementById("sortStyle").onchange = function() {
	historyAjax();
};

document.getElementById("empList").onchange = function() {
	historyAjax();
};

document.getElementById("historyForm").addEventListener("submit", function(event){
  event.preventDefault()
});

function checkDate(x) {
	var el = document.activeElement.id; // get id of current input field
	var d = new Date(x); // set x to date. getDay() returns 0-6 which translates to Sunday - Saturday
	
	if(d.getDay() == 0 || d.getDay() == 6) {
		// if date is weekend, reset field.
		document.getElementById(el).value = "";
	}
}

setTimeout(function () {
	document.getElementById("results-err").style.display = "none";
}, 5000);
setTimeout(function () {
	document.getElementById("results-success").style.display = "none";
}, 5000);

function historyAjax() {
	var xhttp;
	var x = document.getElementById("empList").value;
	var m = document.getElementById("sortMonth").value;
	var o = document.getElementById("sortStyle").value;
	var url = "listHistory.php?e="+x+"&s=month&o="+o+"&m="+m;
	
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if(xhttp.readyState == XMLHttpRequest.DONE) {
			if(xhttp.status == 200) {
				document.getElementById("empHistory").innerHTML = xhttp.responseText;
			}
		}
	};
	xhttp.open("GET", url, true);
	xhttp.send();
};
</script>
</body>
</html>