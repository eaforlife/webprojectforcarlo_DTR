<?php
date_default_timezone_set('Asia/Manila');
require('../myCon.php');
session_start();

//var_dump($_GET);

if(isset($_GET['src'])) {
	if(!empty(cleanTxt($_GET['src']) && cleanTxt($_GET['src']) == "tbl-index")) {
		// employee fill table
		if(isset($_SESSION['admin'])) {
			if(isset($_GET['data'])) {
				$searchTxt = cleanTxt($_GET['data']);
				$curD = date('Y-m-d');
				if($searchQuery = $myConn->prepare("SELECT emp_accounts.acctID, emp_accounts.userName, emp_accounts.firstName, emp_accounts.lastName, emp_time.empID, emp_time.timeDateTime, emp_time.timeMode FROM emp_accounts INNER JOIN emp_time ON emp_accounts.acctID = emp_time.empID WHERE (emp_accounts.acctID LIKE ? OR emp_accounts.userName LIKE ? OR emp_accounts.firstName LIKE ? OR emp_accounts.lastName LIKE ?) AND DATE(emp_time.timeDateTime) = ? ORDER BY timeDateTime DESC;")) {
					$searchTxt = "%" . $searchTxt . "%";
					$searchQuery->bind_param("sssss",$searchTxt,$searchTxt,$searchTxt,$searchTxt,$curD);
					$searchQuery->execute();
					$searchRslt = $searchQuery->get_result();
					$checkID = "";
					if($searchRslt->num_rows > 0) {
						while($row = $searchRslt->fetch_assoc()) {
								if($row['timeMode'] == "0") {
									if($checkID != $row['acctID']) {
									echo "<tr data-bs-toggle='modal' data-bs-target='#emp-info' data-bs-employee='" . $row['acctID'] . "'>
											<th scope='row'>" . $row['acctID'] . "</th>
											<td class='text-capitalize'>" . $row['lastName'] . ", " . $row['firstName'] . "</td>
											<td>
												<div class='spinner-grow spinner-grow-sm text-success' role='status'>
													<span class='visually-hidden'>Loading...</span>
												</div>
											</td>
										</tr>";
									}
								}
								if($row['timeMode'] == "1") {
									if($checkID != $row['acctID']) {
									echo "<tr data-bs-toggle='modal' data-bs-target='#emp-info' data-bs-employee='" . $row['acctID'] . "'>
											<th scope='row'>" . $row['acctID'] . "</th>
											<td class='text-capitalize'>" . $row['lastName'] . ", " . $row['firstName'] . "</td>
											<td>
												<div class='spinner-grow spinner-grow-sm text-secondary' role='status'>
													<span class='visually-hidden'>Loading...</span>
												</div>
											</td>
										</tr>";
									}
								}
								$checkID = $row['acctID'];
							}
							
					}
					$searchQuery->free_result();
					$searchQuery->close();
				} else {
					echo "<h2 class='text-error'>Error Fetch Data</h2><p class='font-monospace'>" . $myConn->error . "</p>";
				}
			} else {
				$curD = date('Y-m-d');
				if($searchQuery = $myConn->prepare("SELECT emp_accounts.acctID, emp_accounts.userName, emp_accounts.firstName, emp_accounts.lastName, emp_time.empID, emp_time.timeDateTime, emp_time.timeMode FROM emp_accounts INNER JOIN emp_time ON emp_accounts.acctID = emp_time.empID WHERE DATE(emp_time.timeDateTime) = ? ORDER BY timeDateTime DESC;")) {
					$searchQuery->bind_param("s",$curD);
					$searchQuery->execute();
					$searchRslt = $searchQuery->get_result();
					$checkID = "";
					if($searchRslt->num_rows > 0) {
						while($row = $searchRslt->fetch_assoc()) {
								if($row['timeMode'] == "0") {
									if($checkID != $row['acctID']) {
									echo "<tr data-bs-toggle='modal' data-bs-target='#emp-info' data-bs-employee='" . $row['acctID'] . "'>
											<th scope='row'>" . $row['acctID'] . "</th>
											<td class='text-capitalize'>" . $row['lastName'] . ", " . $row['firstName'] . "</td>
											<td>
												<div class='spinner-grow spinner-grow-sm text-success' role='status'>
													<span class='visually-hidden'>Loading...</span>
												</div>
											</td>
										</tr>";
									}
								}
								if($row['timeMode'] == "1") {
									if($checkID != $row['acctID']) {
									echo "<tr data-bs-toggle='modal' data-bs-target='#emp-info' data-bs-employee='" . $row['acctID'] . "'>
											<th scope='row'>" . $row['acctID'] . "</th>
											<td class='text-capitalize'>" . $row['lastName'] . ", " . $row['firstName'] . "</td>
											<td>
												<div class='spinner-grow spinner-grow-sm text-secondary' role='status'>
													<span class='visually-hidden'>Loading...</span>
												</div>
											</td>
										</tr>";
									}
								}
								$checkID = $row['acctID'];
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