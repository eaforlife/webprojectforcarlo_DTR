<?php

require("./myCon.php");

function cleanTxt($x) {
	$x = trim($x);
	$x = stripslashes($x);
	$x = htmlspecialchars($x);
	return $x;
}


if(isset($_GET['e'])) {
	
	/* For AJAX (Asynchronous Javascript And Xml)
	* e variable = employee ID.
	* s/o variable = style. For sorting via month, etc
	* 
	* e variable is important while s/o variable is optional
	* url/page.php?e=?&s=?&d=?&o=?
	*/
	
	$empID = cleanTxt($_GET['e']);
	$style = "";
	$order = "";
	$month = "";
	$date = "";
	
	$historySQL = "";
	
	
	
	
	if(isset($_GET['s']) && isset($_GET['m']) && isset($_GET['o'])) {
		$style = cleanTxt($_GET['s']);
		$order = cleanTxt($_GET['o']);
		$month = $_GET['m'];
		
		$date = "2020-$month-01 00:00:01";
		
			if($style == 'month' && $order == 'ASC') {
				$historySQL = "SELECT * FROM emp_time WHERE empID='$empID' AND MONTH(timeDateTime)=MONTH('$date') ORDER BY timeDateTime ASC";
			}
			
			if($style == 'month' && $order == 'DESC') {
				$historySQL = "SELECT * FROM emp_time WHERE empID='$empID' AND MONTH(timeDateTime)=MONTH('$date') ORDER BY timeDateTime DESC";
			}
			
			if($month == "all" && $order == 'ASC') {
				$historySQL = "SELECT * FROM emp_time WHERE empID='$empID' ORDER BY timeDateTime ASC";
			}
			
			if($month == "all" && $order == 'DESC') {
				$historySQL = "SELECT * FROM emp_time WHERE empID='$empID' ORDER BY timeDateTime DESC";
			}
	}
	
	if(isset($_GET['a'])) {
		$historySQL = "SELECT * FROM emp_time WHERE empID='$empID' ORDER BY timeDateTime DESC";
	}
	
	
	$historyQuery = $myConn->query($historySQL);
	if($historyQuery->num_rows > 0) {
		echo "<h2>For Employee ID: $empID </h2>";
		echo "<table>";
		echo "<tr>";
		echo "<th>Status</th>";
		echo "<th>Time</th>";
		echo "</tr>";
		while($row = $historyQuery->fetch_assoc()) {
			$dbDT = strtotime($row['timeDateTime']);
			$newDT = date('D h:i:sA M d Y', $dbDT);
			if($row['timeMode'] == 0) {
				echo "<tr bgcolor='#44AE59'>";
				echo "<td>Logged In</td>";
				echo "<td>$newDT</td>";
				echo "</tr>";
			} else {
				echo "<tr bgcolor='#2CACC9'>";
				echo "<td>Logged Out</td>";
				echo "<td>$newDT</td>";
				echo "</tr>";
			}
		}
		echo "</table>";
	}
}

?>