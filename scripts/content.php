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
					<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#page-unavailable'>Edit Profile</button>
					<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#page-unavailable'>Summary</button>
					<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#page-unavailable'>Admin Tools</button>
					<button type='button' class='btn btn-dark'>Log Out</button>
					";
				} else {
					echo "
					<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#page-unavailable'>Edit Profile</button>
					<button type='button' class='btn btn-dark' data-bs-toggle='modal' data-bs-target='#page-unavailable'>Summary</button>
					<button type='button' class='btn btn-dark'>Log Out</button>
					";
				}
			}
		}
	}
	
	if(!empty(cleanTxt($_GET['src']) && cleanTxt($_GET['src']) == "tbl-index")) {
		// employee fill table
		if(isset($_SESSION['admin'])) {
			if(isset($_GET['data']) && cleanTxt($_GET['data']) != "") {
				$searchTxt = cleanTxt($_GET['data']);
				$curD = date('Y-m-d');
				// SELECT a.*, b.* FROM (SELECT * FROM emp_time WHERE DATE(timeDateTime)=? ORDER BY timeID DESC LIMIT 1000) a LEFT JOIN emp_accounts b ON a.empID=b.acctID WHERE b.acctID LIKE ? OR b.userName LIKE ? OR b.firstName LIKE ? OR b.lastName LIKE ? GROUP BY a.empID;
				
				if($searchQuery = $myConn->prepare("SELECT a.firstName, a.lastName, a.userName, a.acctID, a.timeMode FROM (SELECT * FROM emp_time t LEFT JOIN emp_accounts ac ON t.empID=ac.acctID WHERE DATE(timeDateTime)=? ORDER BY timeID DESC LIMIT 1000) a WHERE a.acctID LIKE ? OR a.userName LIKE ? OR a.firstName LIKE ? OR a.lastName LIKE ? GROUP BY a.empID;")) {
					$searchTxt = "%" . $searchTxt . "%";
					$searchQuery->bind_param("sssss",$curD,$searchTxt,$searchTxt,$searchTxt,$searchTxt);
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
				if($searchQuery = $myConn->prepare("SELECT a.*, b.acctID, b.firstName, b.lastName FROM (SELECT * FROM emp_time WHERE DATE(timeDateTime)=? ORDER BY timeID DESC LIMIT 1000) a LEFT JOIN emp_accounts b ON a.empID=b.acctID GROUP BY a.empID;")) {
					$searchQuery->bind_param("s",$curD);
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