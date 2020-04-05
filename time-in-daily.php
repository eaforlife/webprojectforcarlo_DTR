<!DOCTYPE html>
<html>
<head>
<title>Date Time Record - Time In - Daily</title>
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
	$outMsg = "";
	$active = 1; // 0 disable, 1 time-in, 2 time-out
	
	// Check if user is timed-in or currently on time-out.
	
	$now = date('Y-m-d 00:00:01');
	//$now = date('Y-m-d H:m:s', strtotime($nowDT . ' -1 day'));
	$checkDate = "SELECT * FROM emp_time WHERE empID='$loginID' AND timeDateTime='$now' AND timeMode='1';";  // Check if it's set to Weekly or Monthly
	$checkDateQuery = $myConn->query($checkDate);
	if($checkDateQuery->num_rows > 0) {
		$active = 0; 
	} else {
		// If it's not weekly or monthly then this is daily login. Execute below
		$checkDaily = "SELECT * FROM emp_time WHERE empID='$loginID' AND timeMode='1' ORDER BY timeDateTime DESC LIMIT 1;";
		$checkDailyQuery = $myConn->query($checkDaily);
		if($checkDailyQuery->num_rows > 0) {
			$active = 1;
		} else {
			$active = 3;
		}
		
	}
	
	
	if(isset($_POST['mode'])) {
		$mySQLDT = date('Y-m-d H:i:s'); // Convert PHP time to Date Time MySQL format
		$outDT = date('h:i:sa M d Y');
		if($_POST['mode'] == 'in') {
			
			$timeInSQL = "INSERT INTO emp_time VALUES (NULL, '$loginID', '0', '$mySQLDT');";
			if($myConn->query($timeInSQL) === TRUE) {
				$outMsg = "<p>Timed in at: <span class='txt-success'>$outDT</span></p>";
				$timeMode = 1;
			} else {
				$outMsg = "<p><span class='txt-danger'>Something went wrong. Try to time-in again later!</span></p>";
				$timeMode = 0;
			}
			
		}
		
		if($_POST['mode'] == 'out') {
			
			$timeInSQL = "INSERT INTO emp_time VALUES (NULL, '$loginID', '1', '$mySQLDT');";
			if($myConn->query($timeInSQL) === TRUE) {
				$outMsg = "<p>Timed out at: <span class='txt-success'>$outDT</span></p>";
				$timeMode = 0;
			} else {
				$outMsg = "<p><span class='txt-danger'>Something went wrong. Try to time-out again later!</span></p>";
				$timeMode = 1;
			}
			
		}
	}
?>

<div class="container">
	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-6 justify-center">
		<?php
			if($active == 1) {
				echo "<h2>Time In</h2>";
			} elseif($active == 2) {
				echo "<h2>Time Out</h2>";
			} else {
				echo "<h2>Error</h2>";
			}
		?>
		
		</div>
		<div class="col-md-3"></div>
	</div>
</div>

<div class="container">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8 justify-center">
		<p>Hello <?php echo $loginName; ?>!</p>
		
		<?php if($active == 0): ?>
		<form method="post" id="frmIn">
			<p class="txt-danger"><strong>You are currently timed-in. Maybe via weekly or monthly.</strong></p>
			<button class="btn btn-lg btn-danger" id="checkIn" disabled>Currently Timed In</button>
		</form>
		<?php endif ?>
		
		<?php if($active == 1): ?>
		<form method="post" id="frmIn">
			<input type="hidden" name="mode" value="in">
			<button class="btn btn-lg btn-success" id="checkIn">Time In</button>
		</form>
		<?php echo $outMsg; ?>
		<?php endif ?>
		
		<?php if($active == 2): ?>
		<form method="post" id="frmOut">
			<input type="hidden" name="mode" value="out">
			<button class="btn btn-lg btn-success" id="checkOut">Time Out</button>
		</form>
		<?php endif ?>
		
		</div>
		<div class="col-md-2"></div>
	</div>
</div>
<br>
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
		<table id="empHistory" style="width: 100%;"></table>
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

document.getElementById("historyForm").addEventListener("submit", function(event){
  event.preventDefault()
});

function historyAjax() {
	var xhttp;
	var x = <?php echo $loginID; ?>;
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