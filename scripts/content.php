<?php
date_default_timezone_set('Asia/Manila');
require('../myCon.php');
session_start();

//var_dump($_GET);

if(isset($_GET['src'])) {
	if(cleanTxt($_GET['src']) == "menu") {
		if(isset($_GET['placement'])) {
			if(cleanTxt($_GET['placement']) == 'index') {
				if(isset($_SESSION['admin'])) {
					echo "
					<button type='button' class='btn btn-dark' id='goto-edit'>Edit Profile</button>
					<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#page-unavailable'>Summary</button>
					<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#page-unavailable'>Admin Tools</button>
					<button type='button' class='btn btn-dark'>Log Out</button>
					";
				} else {
					echo "
					<button type='button' class='btn btn-dark' id='goto-edit'>Edit Profile</button>
					<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#page-unavailable'>Summary</button>
					<button type='button' class='btn btn-dark'>Log Out</button>
					";
				}
			}
		}
	}
	
	if(!empty(cleanTxt($_GET['src']) && cleanTxt($_GET['src']) == "tbl-index-emp")) {
		// View employee status
		if(isset($_GET['data']) && cleanTxt($_GET['data']) != "") {
			$usn = cleanTxt($_GET['data']);
			$curD = date('Y-m-d');
			
			if($empQuery = $myConn->prepare("SELECT b.acctID, b.firstName, b.lastName, a.timeMode, a.timeDateTime, MAX(a.timeMode) as currentMode, (SELECT timeDateTime FROM emp_time WHERE DATE(timeDateTime) = DATE(NOW()) AND empID = ? AND timeMode = 0 ORDER BY timeID ASC LIMIT 1) as lastOnline FROM emp_time a, emp_accounts b WHERE DATE(a.timeDateTime) = DATE(NOW()) AND a.empID = ? AND a.empID = b.acctID ORDER BY a.timeID DESC LIMIT 1;")) {
				$empQuery->bind_param("ss", $usn, $usn);
				$empQuery->execute();
				$empOut = $empQuery->get_result();
				
				if($empOut->num_rows > 0) {
					while($row = $empOut->fetch_assoc()) {
						echo "<p>Employee ID: <span class='font-monospace'>" . $row['acctID'] . "</span></p>
					<p>Employee Name: <span class='font-monospace'>" . $row['firstName'] . " " . $row['lastName'] . "</span></p>";
						if($row['currentMode'] == "0") {
							$startDate = new DateTime($row['lastOnline']);
							$currentDate = new DateTime();
							$diff = date_diff($startDate, $currentDate);
							echo "<p>Employee Session: <span class='font-monospace text-success fw-bolder fs-4'>Online</span></p>";
							echo "<p>Online at: <span class='font-monospace'>" . date_format($startDate, "g:i:s a") . "</span></p>";
							echo "<p>Total Session: <span class='font-monospace'>" . $diff->format('%H:%I:%S') . "</span></p>";
						}
						if($row['currentMode'] == "1") {
							$startDate = new DateTime($row['lastOnline']);
							$endDate = new DateTime($row['timeDateTime']);
							$diff = date_diff($startDate, $endDate);
							echo "<p>Employee Session: <span class='font-monospace text-secondary fw-bolder fs-4'>Offline</span></p>";
							echo "<p>Online at: <span class='font-monospace'>" . date_format($startDate, "g:i:s a") . "</span></p>";
							echo "<p>Total Session: <span class='font-monospace'>" . $diff->format('%H:%I:%S') . "</span></p>";
						}
						if($row['currentMode'] == "2") {
							$startDate = new DateTime($row['lastOnline']);
							$endDate = new DateTime($row['timeDateTime']);
							$diff = date_diff($startDate, $endDate);
							echo "<p>Employee Session: <span class='font-monospace text-warning fw-bolder fs-4'>Idle</span></p>";
							echo "<p class='text-warning'>Idle for: <span class='font-monospace'>" . $diff->format('%H:%I:%S') . "</span></p>";
							echo "<p>Online at: <span class='font-monospace'>" . date_format($startDate, "g:i:s a") . "</span></p>";
						}
					}
				} else {
					echo "<p>Unknown employee data. Please try again later.</p>";
				}
			} else {
				echo "<p>An error has occurred while fetch data from server. Error :" . $myConn->error . "</p>";
			}
			
		} else {
			echo "<p>An unknown error has occurred. Please try again later.</p>";
		}
	}
	
	if(!empty(cleanTxt($_GET['src']) && cleanTxt($_GET['src']) == "tbl-index")) {
		// employee fill table
		if(isset($_SESSION['admin']) && $_SESSION['admin'] != "") {
			if(isset($_GET['data']) && cleanTxt($_GET['data']) != "") {
				$searchTxt = cleanTxt($_GET['data']);
				$curD = date('Y-m-d');
				// SELECT a.*, b.* FROM (SELECT * FROM emp_time WHERE DATE(timeDateTime)=? ORDER BY timeID DESC LIMIT 1000) a LEFT JOIN emp_accounts b ON a.empID=b.acctID WHERE b.acctID LIKE ? OR b.userName LIKE ? OR b.firstName LIKE ? OR b.lastName LIKE ? GROUP BY a.empID;
				
				if($searchQuery = $myConn->prepare("SELECT a.firstName, a.lastName, a.userName, a.acctID, a.timeMode FROM (SELECT * FROM emp_time t LEFT JOIN emp_accounts ac ON t.empID=ac.acctID WHERE DATE(timeDateTime)=DATE(NOW()) ORDER BY timeID DESC LIMIT 1000) a WHERE a.acctID LIKE ? OR a.userName LIKE ? OR a.firstName LIKE ? OR a.lastName LIKE ? GROUP BY a.empID;")) {
					$searchTxt = "%" . $searchTxt . "%";
					$searchQuery->bind_param("ssss",$searchTxt,$searchTxt,$searchTxt,$searchTxt);
					$searchQuery->execute();
					$searchRslt = $searchQuery->get_result();
					if($searchRslt->num_rows > 0) {
						while($row = $searchRslt->fetch_assoc()) {
							echo "<tr data-bs-toggle='modal' data-bs-target='#emp-info' data-bs-employee='" . $row['acctID'] . "'>
									<th scope='row'>" . $row['acctID'] . "</th>
									<td class='text-capitalize'>" . $row['lastName'] . ", " . $row['firstName'] . "</td>";
							if($row['timeMode'] == "0") {
								echo "<td data-bs-toggle='tooltip' data-bs-placement='top' title='Online'><span class='text-success'><i class='bi bi-circle-fill'></i></span></td>";
							}
							if($row['timeMode'] == "1") {
								echo "<td data-bs-toggle='tooltip' data-bs-placement='top' title='Offline'><span class='text-secondary'><i class='bi bi-circle-fill'></i></span></td>";
							}
							if($row['timeMode'] == "2") {
								echo "<td data-bs-toggle='tooltip' data-bs-placement='top' title='Idle'><span class='text-warning'><i class='bi bi-circle-fill'></i></span></td>";
							}
							echo "</tr>";
						}
					}
					$searchQuery->free_result();
					$searchQuery->close();
				} else {
					echo "<h2 class='text-error'>Error Fetch Data</h2><p class='font-monospace'>" . $myConn->error . "</p>";
				}
			} else {
				$curD = date('Y-m-d');
				if($searchQuery = $myConn->prepare("SELECT a.*, b.acctID, b.firstName, b.lastName FROM (SELECT * FROM emp_time WHERE DATE(timeDateTime)=DATE(NOW()) ORDER BY timeID DESC LIMIT 1000) a LEFT JOIN emp_accounts b ON a.empID=b.acctID GROUP BY a.empID;")) {
					//$searchQuery->bind_param("s",$curD);
					$searchQuery->execute();
					$searchRslt = $searchQuery->get_result();
					if($searchRslt->num_rows > 0) {
						while($row = $searchRslt->fetch_assoc()) {
							echo "<tr data-bs-toggle='modal' data-bs-target='#emp-info' data-bs-employee='" . $row['acctID'] . "'>
									<th scope='row'>" . $row['acctID'] . "</th>
									<td class='text-capitalize'>" . $row['lastName'] . ", " . $row['firstName'] . "</td>";
							if($row['timeMode'] == "0") {
								echo "<td data-bs-toggle='tooltip' data-bs-placement='top' title='Online'><span class='text-success'><i class='bi bi-circle-fill'></i></span></td>";
							}
							if($row['timeMode'] == "1") {
								echo "<td data-bs-toggle='tooltip' data-bs-placement='top' title='Offline'><span class='text-secondary'><i class='bi bi-circle-fill'></i></span></td>";
							}
							if($row['timeMode'] == "2") {
								echo "<td data-bs-toggle='tooltip' data-bs-placement='top' title='Idle'><span class='text-warning'><i class='bi bi-circle-fill'></i></span></td>";
							}
							echo "</tr>";
						}
					}
					$searchQuery->free_result();
					$searchQuery->close();
				} else {
					echo "<h2 class='text-error'>Error Fetch Data</h2><p class='font-monospace'>" . $myConn->error . "</p>";
				}
			}
		}
	}
}

function cleanTxt($x) {
	$x = trim($x);
	$x = htmlspecialchars($x);
	return $x;
}

?>