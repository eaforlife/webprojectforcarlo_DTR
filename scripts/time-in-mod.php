<?php
	require("../myCon.php");
	// PHP JSON for time-in time-out.
	
	// Init values
	date_default_timezone_set('Asia/Manila');
	$active= $userID = $timemode = $output = "null"; // default value for json validation
	$now = date('Y-m-d 00:00:01');
	$mySQLDT = date('Y-m-d H:i:s'); // Convert PHP time to Date Time MySQL format
	$outDT = date('h:i:sa M d Y');
	
	// PHP validation
	if(isset($_POST['uid'])) {
		if(!empty(data_validation($_POST['uid']))) {
			$userID = data_validation($_POST['uid']);
			if(isset($_POST['action'])) {
				if(!empty(data_validation($_POST['action']))) {
					$action = data_validation($_POST['action']);
					
					if($action == "check") {
						// Check if user is timed out for the day. Return error if timed out.
						// Also checks if user is new and now throw out error but allow time-in.
						
						// return active 0 disable, 1 time-in, 2 time-out, 3 - empty entry
						// time_mode 0 can time in, 1 can time out, 2 can't do either
						$checkDate = "SELECT * FROM emp_time WHERE empID='$userID' AND DATE(NOW())<=DATE('$now') AND timeMode='0' ORDER BY timeDateTime DESC LIMIT 1;";
						$checkDateQuery = $myConn->query($checkDate);
						if($checkDateQuery->num_rows > 0) {
							$active = "0"; // currently timed in, can time out.
							$timemode = "1";
							$output = "one and " . $now;
						} else {
							// If it's not weekly or monthly then this is daily login. Execute below
							// 1000000002
							$checkDaily = "SELECT * FROM emp_time WHERE empID='$userID' AND timeMode='1' AND DATE(NOW())=DATE('$now') ORDER BY timeDateTime DESC LIMIT 1;";
							$checkDailyQuery = $myConn->query($checkDaily);
							if($checkDailyQuery->num_rows > 0) {
								$active = "1"; // currently timed out for the day.
								$timemode = "2";
								$output = "two and " . $now;
							} else {
								$active = "2"; // no record can time in.
								$timemode = "0";
								$output = "three and " . $now;
							}
						}
						//$output = "null";
					}
					
					if($action == "in") {
						$timeInSQL = "INSERT INTO emp_time VALUES (NULL, '$userID', '0', '$mySQLDT');";
						if($myConn->query($timeInSQL) === TRUE) {
							$output = "<p>Timed in at: <span class='txt-success'>$outDT</span></p>";
						} else {
							$output = "<p><span class='txt-danger'>Something went wrong. Try to time-in again later!</span></p>";
						}
						$timemode = "1";
					}
					
					if($action == "out") {
						$timeInSQL = "INSERT INTO emp_time VALUES (NULL, '$userID', '1', '$mySQLDT');";
						if($myConn->query($timeInSQL) === TRUE) {
							$output = "<p>Timed out at: <span class='txt-success'>$outDT</span></p>";
						} else {
							$output = "<p><span class='txt-danger'>Something went wrong. Try to time-out again later!</span></p>";
						}
						$timemode = "0";
					}
					
					if($action == "test1") {
						$output = "test 1 test 1";
						$timemode = "0";
					} 
					
					if($action == "test2") {
						$output = "test 2 test 2";
						$timemode = "1";
					} 
				}
			} else {
				$output = "Unknown command. Please try again later or contact administrator.";
			}
		} else {
			$output = "Unknown or corrupted employee info. Please try again later or contact administrator.";
		}
	} else {
		$output = "Unknown or corrupted employee info. Please try again later or contact administrator.";
	}
	
	$json_out = array("output"=>$output, "active"=>$active, "timemode"=>$timemode, "employee"=>$userID);
	echo json_encode($json_out);
	
	// validation checks
	function data_validation($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

?>