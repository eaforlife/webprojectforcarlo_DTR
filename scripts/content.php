<?php
date_default_timezone_set('Asia/Manila');
require('../myCon.php');
session_start();

//var_dump($_GET);

if(isset($_GET['src'])) {
	if(cleanTxt($_GET['src']) == "menu") {
		if(isset($_GET['placement'])) {
			if(cleanTxt($_GET['placement']) == 'index') {
				if(isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
					echo "
					<a class='btn btn-dark' href='./edit.html'>Edit Profile</a>
					<a class='btn btn-dark' href='./summary.html'>Summary</a>
					<a class='btn btn-dark' href='./tools.html'>Admin Tools</a>
					<a class='btn btn-dark' href='#' data-bs-toggle='modal' data-bs-target='#warning-timeout'>Log Out</a>
					";
				} else {
					echo "
					<a class='btn btn-dark' href='./edit.html'>Edit Profile</a>
					<a class='btn btn-dark' href='./summary.html'>Summary</a>
					<a class='btn btn-dark' href='#' data-bs-toggle='modal' data-bs-target='#warning-timeout'>Log Out</a>
					";
				}
			}
		}
	}
	
	if(!empty(cleanTxt($_GET['src']) && cleanTxt($_GET['src']) == "tbl-edit-emp")) {
		if(isset($_GET['data']) && !empty(cleanTxt($_GET['data']))) {
			$data = cleanTxt($_GET['data']);
			$filedir = "./style/avatar/";
			$output = [];
			if($fetchEmp = $myConn->prepare("SELECT * FROM emp_accounts WHERE acctID=? AND status='1';")) {
				$fetchEmp->bind_param("s",$data);
				$fetchEmp->execute();
				$empResult = $fetchEmp->get_result();
				if($empResult->num_rows > 0) {
					while($row = $empResult->fetch_assoc()) {
						$output['emp-id'] = $row['acctID'];
						$output['fname'] = $row['firstName'];
						$output['lname'] = $row['lastName'];
						$output['email'] = $row['email'];
						$output['image'] = $filedir . $row['photo'];
					}
					$output['error'] = "0";
					$output['error-message'] = "OK";
				} else {
					$output['error'] = "1";
					$output['error-message'] = "Employee Not Found";
				}
				$empResult->free_result();
			} else {
				$output['error'] = "1";
				$output['error-message'] = "Database error: " . $myConn->error;
			}
			$fetchEmp->close();
		} else {
			$output['error'] = "1";
			$output['error-message'] = "Invalid input data";
		}
		echo json_encode($output);
	}
	
	if(!empty(cleanTxt($_GET['src']) && cleanTxt($_GET['src']) == "tbl-admintools")) {
		if(isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
			$data = $sort = $sortby = "";
			$currentID = $_SESSION['id'];
			if(isset($_GET['data']) && !empty(cleanTxt($_GET['data']))) {
				$data = cleanTxt($_GET['data']);
			}
			if(isset($_GET['sort']) && !empty(cleanTxt($_GET['sort']))) {
				$sort = strtoupper(cleanTxt($_GET['sort']));
			} else {
				$sort = "DESC";
			}
			if(isset($_GET['sortby']) && !empty(cleanTxt($_GET['sortby']))) {
				$sortby = cleanTxt($_GET['sortby']);
			}
			
			if(empty($data)) {
				if($sort == "DESC") {
					if($sortby == "fname") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.status='1' ORDER BY a.firstName DESC;");
					} elseif($sortby == "lname") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.status='1' ORDER BY a.lastName DESC;");
					} else {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.status='1' ORDER BY a.acctID DESC;");
					}
				} else {
					if($sortby == "fname") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.status='1' ORDER BY a.firstName ASC;");
					} elseif($sortby == "lname") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.status='1' ORDER BY a.lastName ASC;");
					} else {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.status='1' ORDER BY a.acctID ASC;");
					}
				}
			} else {
				if($sort == "DESC") {
					if($sortby == "fname") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.acctID=? AND a.status='1' ORDER BY a.firstName DESC;");
						$empQuery->bind_param("s",$data);
					} elseif($sortby == "lname") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.acctID=? AND a.status='1' ORDER BY a.lastName DESC;");
						$empQuery->bind_param("s",$data);
					} else {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.acctID=? AND a.status='1' ORDER BY a.acctID DESC;");
						$empQuery->bind_param("s",$data);
					}
				} else {
					if($sortby == "fname") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.acctID=? AND a.status='1' ORDER BY a.firstName ASC;");
						$empQuery->bind_param("s",$data);
					} elseif($sortby == "lname") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.acctID=? AND a.status='1' ORDER BY a.lastName ASC;");
						$empQuery->bind_param("s",$data);
					} else {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, a.email, (SELECT timeDateTime FROM emp_time WHERE empID=a.acctID AND timeMode='0' ORDER BY timeID DESC LIMIT 1) as lastOnline FROM emp_accounts a WHERE a.acctID=? AND a.status='1' ORDER BY a.acctID ASC;");
						$empQuery->bind_param("s",$data);
					}
				}
			}
			if($empQuery) {
				$empQuery->execute();
				$empRslt = $empQuery->get_result();
				if($empRslt->num_rows > 0) {
					while($row = $empRslt->fetch_assoc()) {
						$timeDt = new DateTime($row['lastOnline']);
						$newTime = date_format($timeDt, "t M Y g:i:s A");
						$empName = ucfirst($row['firstName']) . " " . ucfirst($row['lastName']);
						echo "<tr>
							<td>" . $row['acctID'] . "</td>
							<td>" . $empName . "</td>
							<td>" . $row['email'] . "</td>
							<td>" . $newTime . "</td>
							<td>
								<div class='btn-group'>";
						if($row['acctID'] == $currentID) {
							echo "<a href='#' class='btn btn-outline disabled'><i class='bi bi-pencil-square'></i></a>
									<a href='#' class='btn btn-outline disabled' alt='Delete'><i class='bi bi-trash'></i></a>";
						} else {
							echo "<a href='#' class='btn btn-outline' alt='Edit'><i class='bi bi-pencil-square' id='tbl-edit' data-employee='" . $row['acctID'] . "'></i></a>
									<a href='#' class='btn btn-outline' alt='Delete'><i class='bi bi-trash' id='tbl-delete' data-employee='" . $row['acctID'] . "'></i></a>";
						}
						echo "</div>
							</td>
						</tr>";
						
					}
				} else {
					echo "<h4><span class='text-warning'><i class='bi bi-exclamation-diamond-fill'></i></span> Employee Info Not Found!</h4>";
				}
				$empRslt->free_result();
			} else {
				echo "<h4><span class='text-danger'><i class='bi bi-exclamation-diamond-fill'></i></span> Unable to fetch data.!</h4><pre>" . $myConn->error . "</pre>";
			}
			$empQuery->close();
		}
	}
	
	if(!empty(cleanTxt($_GET['src']) && cleanTxt($_GET['src']) == "sel-summary")) {
		echo "<option value='all' id='all' selected>All</option>";
		if($empList = $myConn->prepare("SELECT acctID, firstName, lastName FROM emp_accounts WHERE status='1' ORDER BY acctID ASC;")) {
			$empList->execute();
			$resultList = $empList->get_result();
			if($resultList->num_rows > 0) {
				while($row = $resultList->fetch_assoc()) {
					echo "<option value='" . $row['acctID'] . "'>" . $row['acctID'] . " - " . ucfirst($row['firstName']) . " " . ucfirst($row['lastName']) . "</option>";
				}
			}
			$resultList->free_result();
		}
		$empList->close();
	}
	
	if(!empty(cleanTxt($_GET['src']) && cleanTxt($_GET['src']) == "tbl-summary")) {
		if(isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
			// if admin show all accounts
			
			if(isset($_GET['data']) || !empty(cleanTxt($_GET['data']))) {
				$data = cleanTxt($_GET['data']);
				$sort = cleanTxt($_GET['sort']);
				if($sort == "asc") {
					if($data == "id") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) ORDER BY a.acctID ASC;");
					} elseif($data == "name") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) ORDER BY a.firstName ASC;");
					} elseif($data == "dt") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) ORDER BY b.timeID ASC;");
					} elseif($data == "all") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) ORDER BY b.timeID ASC;");
					} else {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY b.timeID ASC;");
						$empQuery->bind_param("s",$data);
					}
				} else {
					if($data == "id") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) ORDER BY a.acctID DESC;");
					} elseif($data == "name") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) ORDER BY a.firstName DESC;");
					} elseif($data == "dt") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) ORDER BY b.timeID DESC;");
					} elseif($data == "all") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) ORDER BY b.timeID DESC;");
					} else {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY b.timeID DESC;");
						$empQuery->bind_param("s",$data);
					}
				}
				$empQuery->execute();
				$resultEmp = $empQuery->get_result();
				if($resultEmp->num_rows > 0) {
					while($row = $resultEmp->fetch_assoc()) {
						$timeDt = new DateTime($row['timeDateTime']);
						$newTime = date_format($timeDt, "g:i:s A");
						echo "<tr>
							<td>" . $row['acctID'] . "</td>
							<td>" . $row['firstName'] . " " . $row['lastName'] . "</td>";
						if($row['timeMode'] == "0") {
							echo "<td>Online</td>";
						} elseif($row['timeMode'] == "2") {
							echo "<td>Idle</td>";
						} else {
							echo "<td>Offline</td>";
						}
							
						echo "<td>" . $newTime . "</td>
						</tr>";
					}
					$resultEmp->free_result();
				} else {
					echo "<p><strong>No data to show.</strong></p>";
				}
				$empQuery->close();
			}
		} else {
			
			if(isset($_GET['data']) || !empty(cleanTxt($_GET['data']))) {
				$data = cleanTxt($_GET['data']);
				$sort = cleanTxt($_GET['sort']);
				if($sort == "asc") {
					if($data == "id") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY a.acctID ASC;");
						$empQuery->bind_param("s",$_SESSION['id']);
					} elseif($data == "name") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY a.firstName ASC;");
						$empQuery->bind_param("s",$_SESSION['id']);
					} elseif($data == "dt") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY b.timeID ASC;");
						$empQuery->bind_param("s",$_SESSION['id']);
					} else {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY b.timeID ASC;");
						$empQuery->bind_param("s",$_SESSION['id']);
					}
				} else {
					if($data == "id") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY a.acctID DESC;");
						$empQuery->bind_param("s",$_SESSION['id']);
					} elseif($data == "name") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY a.firstName DESC;");
						$empQuery->bind_param("s",$_SESSION['id']);
					} elseif($data == "dt") {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY b.timeID DESC;");
						$empQuery->bind_param("s",$_SESSION['id']);
					} else {
						$empQuery = $myConn->prepare("SELECT a.acctID, a.firstName, a.lastName, b.timeMode, b.timeDateTime FROM emp_accounts a RIGHT JOIN emp_time b ON a.acctID=b.empID WHERE a.status='1' AND DATE(NOW()) = DATE(b.timeDateTime) AND a.acctID=? ORDER BY b.timeID DESC;");
						$empQuery->bind_param("s",$_SESSION['id']);
					}
				}
				$empQuery->execute();
				$resultEmp = $empQuery->get_result();
				if($resultEmp->num_rows > 0) {
					while($row = $resultEmp->fetch_assoc()) {
						$timeDt = new DateTime($row['timeDateTime']);
						$newTime = date_format($timeDt, "g:i:s A");
						echo "<tr>
							<td>" . $row['acctID'] . "</td>
							<td>" . $row['firstName'] . " " . $row['lastName'] . "</td>";
						if($row['timeMode'] == "0") {
							echo "<td>Online</td>";
						} elseif($row['timeMode'] == "2") {
							echo "<td>Idle</td>";
						} else {
							echo "<td>Offline</td>";
						}
							
						echo "<td>" . $newTime . "</td>
						</tr>";
					}
					$resultEmp->free_result();
				} else {
					echo "<p><strong>No data to show.</strong></p>";
				}
				$empQuery->close();
			}
			
		}
	}
	
	if(!empty(cleanTxt($_GET['src']) && cleanTxt($_GET['src']) == "tbl-index-emp")) {
		// View employee status
		if(isset($_GET['data']) && cleanTxt($_GET['data']) != "") {
			$usn = cleanTxt($_GET['data']);
			$curD = date('Y-m-d');
			$filedir = "./style/avatar/";
			if($empQuery = $myConn->prepare("SELECT b.acctID, b.firstName, b.lastName, a.timeMode, a.timeDateTime, b.photo, (SELECT timeDateTime FROM emp_time WHERE DATE(timeDateTime) = DATE(NOW()) AND empID = ? AND timeMode = 0 ORDER BY timeID ASC LIMIT 1) as lastOnline FROM emp_time a, emp_accounts b WHERE DATE(a.timeDateTime) = DATE(NOW()) AND a.empID = ? AND a.empID = b.acctID ORDER BY a.timeID DESC LIMIT 1;")) {
				$empQuery->bind_param("ss", $usn, $usn);
				$empQuery->execute();
				$empOut = $empQuery->get_result();
				
				if($empOut->num_rows > 0) {
					while($row = $empOut->fetch_assoc()) {
						echo "<img src='" . $filedir . $row['photo'] . "' class='img-fluid img-thumbnail mb-4' alt='pic-" . $row['acctID'] . "' style='height:200px;'>
						<p><small>Employee ID:</small> <span class='font-monospace'>" . $row['acctID'] . "</span></p>
					<p><small>Employee Name:</small> <span class='font-monospace'>" . $row['firstName'] . " " . $row['lastName'] . "</span></p>";
						if($row['timeMode'] == "0") {
							$startDate = new DateTime($row['lastOnline']);
							$currentDate = new DateTime();
							$diff = date_diff($startDate, $currentDate);
							echo "<p><small>Employee Session:</small> <span class='font-monospace text-success fw-bolder fs-4'>Online</span></p>";
							echo "<p><small>Online at:</small> <span class='font-monospace'>" . date_format($startDate, "g:i:s a") . "</span></p>";
							echo "<p><small>Total Session:</small> <span class='font-monospace'>" . $diff->format('%H:%I:%S') . "</span></p>";
						}
						if($row['timeMode'] == "1") {
							$startDate = new DateTime($row['lastOnline']);
							$endDate = new DateTime($row['timeDateTime']);
							$diff = date_diff($startDate, $endDate);
							echo "<p><small>Employee Session:</small> <span class='font-monospace text-secondary fw-bolder fs-4'>Offline</span></p>";
							echo "<p><small>Online at:</small> <span class='font-monospace'>" . date_format($startDate, "g:i:s a") . "</span></p>";
							echo "<p><small>Total Session:</small> <span class='font-monospace'>" . $diff->format('%H:%I:%S') . "</span></p>";
						}
						if($row['timeMode'] == "2") {
							$startDate = new DateTime($row['lastOnline']);
							$endDate = new DateTime($row['timeDateTime']);
							$diff = date_diff($startDate, $endDate);
							echo "<p><small>Employee Session:</small> <span class='font-monospace text-warning fw-bolder fs-4'>Idle</span></p>";
							echo "<p><small>Idle for:</small> <span class='font-monospace text-warning'>" . $diff->format('%H:%I:%S') . "</span></p>";
							echo "<p><small>Online at:</small> <span class='font-monospace'>" . date_format($startDate, "g:i:s a") . "</span></p>";
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