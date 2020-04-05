<?php

require_once("./myCon.php");

if(isset($_POST['empID']) && isset($_POST['empName']) && isset($_POST['mode'])) {
	$empID = cleanTxt($_POST['empID']);
	$empName = cleanTxt($_POST['empName']);
	
	$delStatus = false; // time records delete
	$delAccount = false;
	
	// Delete Employee
	if(cleanTxt($_POST['mode']) == 'delete') {
		
		if(!empty($empID) && !empty($empName)) {
			$delTime = "DELETE FROM emp_time WHERE empID='$empID'";
			if($myConn->query($delTime) === true)
				$delStatus = true;
			else
				$delStatus = false;
			
			if($delStatus === true) {
				$delEmp = "DELETE FROM emp_accounts WHERE acctID='$empID';";
				if($myConn->query($delEmp) === true)
					$delAccount = true;
				else
					$delAccount = false;
			}
			
			if($delStatus === true && $delAccount === true) {
				$outputMsg = "Deleted succcessfully. Remember this action is irreversible.";
				$hiddenMsg = "<p id='fail-redirect' class='txt-success' style='display:none;'><small><a href='./records.php'>Click here if you were not redirected after 10 seconds</a></small></p>";
			} elseif($delStatus === true && $delAccount === false) {
				$outputMsg = "Something went wrong. Unable to delete employee account.But able to delete employee time records!";
				$hiddenMsg .= "<p id='fail-redirect' class='txt-danger' style='display:none;'><small><a href='./records.php'>Click here if you were not redirected after 10 seconds</a></small></p>";
			} elseif($delStatus === false && $delAccount === true) {
				$outputMsg = "Something went wrong. Unable to delete employee time records.But able to delete employee account!";
				$hiddenMsg .= "<p id='fail-redirect' class='txt-danger' style='display:none;'><small><a href='./records.php'>Click here if you were not redirected after 10 seconds</a></small></p>";
			} else {
				$outputMsg = "Something went wrong. Unable to delete employee records!";
				$hiddenMsg .= "<p id='fail-redirect' class='txt-danger' style='display:none;'><small><a href='./records.php'>Click here if you were not redirected after 10 seconds</a></small></p>";
			}
			echo "<script>";
			echo "setTimeout(function(){ document.getElementById('fail-redirect').style.display = 'block'; }, 4000);";
			echo "alert('" . $outputMsg . "');";
			echo "window.location.href('./records.php');";
			echo "</script>";
		}
		
	}
	
}



if(isset($_GET['e']) && isset($_GET['p'])) {
	
	echo "<tr>";
	echo "<th>ID</th>";
	echo "<th>Employee Name</th>";
	echo "<th>E-Mail</th>";
	echo "<th>Status</th>";
	echo "<th>Action</th>";
	echo "</tr>";
	
	if($_GET['p'] == "records" && $_GET['e'] == "null") {
		$tableSQL = "SELECT * FROM emp_accounts;";
		$tblRslt = $myConn->query($tableSQL);
		
		if($tblRslt->num_rows >= 0) {
			while($row = $tblRslt->fetch_assoc()) {
				$empName = $row['lastName'] . ", " . $row['firstName'];
				$empID = $row['acctID'];
				echo "<tr>\n";
				
				echo "<td>$empID</td>\n";
				echo "<td>$empName</td>\n";
				echo "<td>" . $row['email'] . "</td>\n";
				if($row['status'] == 1)
					echo "<td class='txt-success'>Active</td>\n";
				else
					echo "<td class='txt-warning'>Archive</td>\n";
				echo "<td>";
				echo "<form method='post' id='tableAction-" . $row['acctID'] . "' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>\n";
				echo "<input type='hidden' value='$empName' id='empName$empID' name='empName'>\n";
				echo "<input type='hidden' value='$empID' id='empID$empID' name='empID'>\n";
				echo "<input type='hidden' value='delete' id='del' name='mode'>\n";
				echo "<button type='button' class='btn btn-success form-flex' name='frmView' onclick='modalViewEmp($empID)'>View Attendace</button>\n";
				echo "<button type='button' class='btn btn-info form-flex' name='frmEdit' onclick='modalEditEmp($empID)'>Edit</button>\n";
				echo "<button type='button' class='btn btn-danger form-flex' name='frmDel' onclick='modalDelEmp($empID)'>Delete</button>\n";
				echo "</form>\n";
				echo "</td>";
				echo "</tr>\n";
			}
		}
	}
	
	if($_GET['p'] == "records") {
		$searchTxt = $_GET['e'];
		$searchTxt = trim($searchTxt);
		$searchTxt = htmlspecialchars($searchTxt);
		
		
		$tableSQL = "SELECT * FROM emp_accounts WHERE lastName LIKE '%$searchTxt%' OR firstName LIKE '%$searchTxt%' OR acctID LIKE '%$searchTxt%';";
		$tblRslt = $myConn->query($tableSQL);
		
		if($tblRslt->num_rows >= 0) {
			while($row = $tblRslt->fetch_assoc()) {
				$empName = $row['lastName'] . ", " . $row['firstName'];
				$empID = $row['acctID'];
				echo "<tr>\n";
				
				echo "<td>$empID</td>\n";
				echo "<td>$empName</td>\n";
				echo "<td>" . $row['email'] . "</td>\n";
				if($row['status'] == 1)
					echo "<td class='txt-success'>Active</td>\n";
				else
					echo "<td class='txt-warning'>Archive</td>\n";
				echo "<td>";
				echo "<form method='post' id='tableAction-" . $row['acctID'] . "' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>\n";
				echo "<input type='hidden' value='$empName' id='empName$empID' name='empName'>\n";
				echo "<input type='hidden' value='$empID' id='empID$empID' name='empID'>\n";
				echo "<input type='hidden' value='delete' id='del' name='mode'>\n";
				echo "<button type='button' class='btn btn-success form-flex' name='frmView' onclick='modalViewEmp($empID)'>View Attendace</button>\n";
				echo "<button type='button' class='btn btn-info form-flex' name='frmEdit' onclick='modalEditEmp($empID)'>Edit</button>\n";
				echo "<button type='button' class='btn btn-danger form-flex' name='frmDel' onclick='modalDelEmp($empID)'>Delete</button>\n";
				echo "</form>\n";
				echo "</td>";
				echo "</tr>\n";
			}
		}
	}
	echo "</table>";
}

if(isset($_GET['editemp'])) {
	
	// For index page search employee
	
	$empID = trim($_GET['editemp']);
	$empID = stripslashes($empID);
	$empID = htmlspecialchars($empID);
	
	$empID = substr($empID, 0, 10);
	
	$empSQL = "SELECT * FROM emp_accounts WHERE acctID='$empID';";
	$empQuery = $myConn->query($empSQL);
	if($empQuery->num_rows > 0) {
		while($row = $empQuery->fetch_assoc()) {
			echo "<form method='post' action='./getemployee.php'>\n";
			echo "<label for='empID'>Employee ID:</label>\n";
			echo "<input type='text' name='empID' id='empID' value='" . $row['acctID'] . "' readonly><br>\n";
			echo "<label for='lname'>Last Name: </label>\n";
			echo "<input type='text' name='lname' id='lname' value='" . $row['lastName'] . "' disabled><br>\n";
			echo "<label for='fname'>First Name: </label>\n";
			echo "<input type='text' name='fname' id='fname' value='" . $row['firstName'] . "' disabled><br>\n";
			echo "<label for='pass'>Reset Password: </label>\n";
			echo "<input type='checkbox' name='pass' id='pass' value='reset'><br>\n";
			if($row['status'] == '0') {
				echo "<label for='acct'>Restore Account: </label>\n";
				echo "<input type='checkbox' name='account' id='acct' value='restore'><br>\n";
			} else {
				echo "<label for='acct'>Delete Account: </label>\n";
				echo "<input type='checkbox' name='account' id='acct' value='delete'><br>\n";
			}
			echo "<br><input type='submit' class='btn form-flex btn-info' value='Edit'><br>";
			echo "</form>";
		}
	} else {
		echo "<p class='txt-danger'>Employee '" . $empID . "' not found. Make sure you enter the correct 'ID' or 'Name' then try again later.</p>";
	}
	
}


function cleanTxt($x) {
	$x = trim($x);
	$x = stripslashes($x);
	$x = htmlspecialchars($x);
	return $x;
}

?>