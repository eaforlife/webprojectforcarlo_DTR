<!DOCTYPE html>
<!-- Use of CSS Grid: https://www.w3schools.com/css/css_grid.asp -->
<html>
<head>
	<title>Session Based Attendance - Home</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="./style/bootstrap.min.css" rel="stylesheet">
	<link href="./style/fonts/bootstrap-icons.css" rel="stylesheet">
	<style>
		body, footer {
			background-color: #EAF0FA;
		}
		#bg-container {
			height: 380px;
		}
		#bg {
			background-color: black; /* fallback background if image doesn't exist */
			background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./style/stock-photo-red-alarm-clock-1124798804.jpg'); /* edit inside url with code for url of background image
																																					* . dot means up 1 folder level. In other words 2 dots here
																																					* just means we go to the root directory where the HTML pages are */
			height: 100%;
			background-position: center;
			background-repeat: no-repeat;
			background-size: cover;
			position: inherit;
		}
	</style>
</head>
<body>

<div class="container" id="body">
	
	<div class="row"> <!-- Header Image -->
		<div class="container-fluid m-0 p-0">
			<div id="bg-container"><div id="bg"></div></div>
		</div>
	</div>
	
	<div class="row"> <!-- Navigation -->
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow mb-3 rounded">
			<div class="container-fluid">
				<a class="navbar-brand" href="./index.php">Attendance System</a>				
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navigationMenu" aria-controls="navigationMenu" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				
				<div class="collapse navbar-collapse" id="navigationMenu">
					<div class="navbar-nav me-auto ">
						<a class="nav-link active" aria-current="page" href="#">Home</a>
						<a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#page-unavailable">Settings</a>
						<a class="nav-link" id="nav-timeout" href="#">Logout <span class="text-capitalize" id="nav-userfullname"></span></a>
					</div>
					<span class="navbar-text text-success fw-bold fs-6 d-none" id="user-status-priv">Logged in as Admin</span>
				</div>
			</div>
		</nav>
	</div>
	
	<div class="row bg-light"> <!-- Content -->
		<div class="container-fluid py-5 shadow mb-2 rounded">
			<div class="row my-2">
				<div class="col-lg-8 mx-auto text-center border">
					<div class="container my-5">
						<h2 class="fw-light">Welcome <span class="text-capitalize" id="userFullName"></span>!</h2>
						<div id="user-status-in" class="text-capitalize d-none"><h2 class="text-success"><span>You are currently timed in.</span></h2></div>
						<div id="user-status-out" class="text-capitalize d-none"><h2 class="text-danger"><span>You are currently timed out.</span></h2></div>
						
						<button type="button" id="btn-user-out" class="btn btn-lg btn-danger d-none" data-bs-toggle="modal" data-bs-target="#warning-timeout">Time Out</button>
						<span class="d-inline block d-none" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="You have already signed out for the day.">
							<button type="button" id="btn-user-disable" class="btn btn-lg btn-danger" disabled>Time Out</button>
						</span>
						<button type="button" id="btn-user-in" class="btn btn-lg btn-success d-none" id="btn-timein" data-bs-toggle="modal" data-bs-target="#page-unavailable">Time In</button>
					</div>
				</div>
			</div>
			
			<div class="row mb-4">
				<div class="col-lg-8 mx-auto">
					<div class="row">
						<div class="col-lg-4 align-self-center d-none d-md-block">
							<div class="container">
								<p class="fw-bold">What do you want to do?</p>
								<div class="d-grid gap-2">
									<div class="btn-group-vertical btn-group-lg btn-quick-links" role="group" aria-label="Quick Links">
										
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-8">
							<div class="container">
								<p class="fw-bold">Employee Search</p>
								<form class="row mx-auto my-2 emp-search">
									<div class="input-group mb-2">
										<label for="emp-search-field" class="visually-hidden">Search: </label>
										<input type="text" class="form-control" id="emp-search-text" name="emp-search-text" placeholder="Search Employee ID, Name or Username" aria-label="Employee Search" required>
										<div class="invalid-tooltip">Error</div>
										<button type="submit" class="btn btn-outline-dark px-3"><i class="bi bi-search"></i></button>
										<button type="button" id="emp-list-refresh" class="btn btn-outline-dark px-3"><i class="bi bi-arrow-clockwise"></i></button>
									</div>
								</form>
								<div class="row">
									<div class="container">
										<table class="table table-light table-striped">
											<thead>
												<tr>
													<th scope="col">Emp No.</th>
													<th scope="col">Name</th>
													<th scope="col">Status</th>
												</tr>
											</thead>
											<tbody class="text-capitalize" id="tbl-employee">
											</tbody>
										</table>
										<div class="text-center table-loading">
											<div class="spinner-grow my-5" role="status">
												<span class="visually-hidden">Loading...</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<footer class="row mt-2 p-5">	<!-- Footer -->
		<div class="col-12 mx-auto text-center">
			<p class="text-muted">Session Based Attendance &copy; 2021 | Powered by Bootstrap 5.1</p>
		</div>
	</footer>
	
</div>

<!-- Modals/Tooltips -->
<div class="modal fade" id="warning-timeout" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="warning-timeout-title">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="warning-timeout-title"><span class="text-danger"><i class="bi bi-exclamation-circle-fill"></i></span> Are you sure you want to time out?</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				By selecting continue, any session timer that's active on your account will be terminated. Do you wish to continue?
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" id="btn-timeout"><i class="bi bi-exclamation-circle"></i> Continue</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="emp-info" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="emp-info-title">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="emp-info-title"><span class="text-info"><i class="bi bi-info-circle-fill"></i></span> Employee Status</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="emp-search-output">
				<div class="text-capitalize">
					<p>Employee ID: <span class="font-monospace emp-modal-id"></span></p>
					<p>Employee Name: <span class="font-monospace emp-modal-name"></span></p>
					<p>Employee Session: <span class="font-monospace text-success fw-bolder fs-4">Active</span></p>
					<p>Session Time: <span class="font-monospace">03:00:45</span></p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="page-unavailable" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="page-unavailable">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="emp-info-title"><span class="text-warning"><i class="bi bi-tools"></i></span> Page Not Available</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				The page you are trying to view is not available yet.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- Javascript end of page for faster load times -->
<script src="./scripts/bootstrap.bundle.min.js"></script>
<script>
	//console.log("Hello World.");
	var counter = 0;
	var sessionTime;
	var refreshTable;
	//var sessionTime = setInterval(sessionTimer, 1000);
	
	// Initialize Page
	init();
	window.onload = function() {
	  inactivityTime();
	}
	
	// Initialize bootstrap utilities
	var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
	var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
	  return new bootstrap.Popover(popoverTriggerEl)
	})
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})
	// main functions
	function init() {
		const xhr = new XMLHttpRequest();
		xhr.onload = function() {
			//console.log(this.responseText);
			try {
				var initObj = JSON.parse(this.responseText);
				console.log(initObj);
				if(initObj['error'] == "0") {
					initmenu("index");
					document.querySelector("#nav-userfullname").innerHTML = initObj['user-name'];
					document.querySelector("#userFullName").innerHTML = initObj['user-name'];
					// todo if currently timed it conditions
					document.querySelector("#user-status-in").classList.remove("d-none");
					document.querySelector("#btn-user-out").classList.remove("d-none");
					if(initObj['user-admin'] == "true")
						document.querySelector("#user-status-priv").classList.remove("d-none");
					
					sessionTime = setInterval(sessionTimer, 1000);
					fillTable();
					refreshTable = setInterval(fillTable, 10000);
				} else {
					console.log(initObj['message']); // debug
					window.location.href = "./login.php"; // page should not load if something went wrong here. go back to login page. uncomment after debug
				}
			} catch(err) {
				console.log("init error");
				console.log(err);
			}
		}
		xhr.open("POST", "./scripts/session-init.php", true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.send("src=index");
	}
	
	function initmenu(data) {
		if(data == null)
			data = "";
		const xhr = new XMLHttpRequest();
		xhr.onload = function() {
			if(data == "index") {
				document.querySelector(".btn-quick-links").innerHTML = this.responseText;
			}
		}
		xhr.open("GET", "./scripts/content.php?src=menu&placement="+data, true);
		xhr.send();
	}
	
	function fillTable(data) {
		document.querySelector(".table-loading").classList.remove("d-none");
		if(data == null)
			data = "";
		const xhr = new XMLHttpRequest();
		xhr.onload = function() {
			document.querySelector("#tbl-employee").innerHTML = this.responseText;
			document.querySelector(".table-loading").classList.add("d-none");
			//console.log("table ajax loaded");
		}
		xhr.open("GET", "./scripts/content.php?src=tbl-index&data="+data, true);
		xhr.send();
	}
	
	// set session timer
	function sessionTimer() {
		_hrs = counter / 3600;
		_min = (_hrs * 60) - (Math.floor(_hrs) * 60);
		_sec = (_min * 60) - (Math.floor(_min) * 60);
		_hrs = Math.floor(_hrs);
		_min = Math.floor(_min);
		_sec = Math.floor(_sec);
		if(_hrs <= 9) {
			_hrs = "0" + _hrs;
		}
		if(_min >= 60) {
			_min = "00";
		}
		if(_min <= 9) {
			_min = "0" + _min;
		}
		if(_sec >= 60) {
			_sec = "00";
		}
		if(_sec <= 9) {
			_sec = "0" + _sec;
		}
		var output = _hrs + ":" + _min + ":" + _sec;
		document.getElementById("userSessionTimer").innerHTML = "Time in session: " + output;
		counter++;
	}
	
	function stopSessionTimer() {
		clearInterval(sessionTime);
	}
	
	function stopTableTimer() {
		clearInterval(refreshTable);
	}
	
	var inactivityTime = function () {
		var time;
		window.onload = resetTimer;
		// DOM Events
		document.onmousemove = resetTimer;
		document.onkeydown = resetTimer;

		function setidle() {
				var xhr = new XMLHttpRequest();
				xhr.onload = function() {
					console.log("idle");
					window.location.href = "./login.php";
				}
				xhr.open("GET", "./scripts/session-logout.php?src=idle", true);
				xhr.send();
				
		}

		function resetTimer() {
			clearTimeout(time);
			time = setTimeout(setidle, 300000) // 5mins or 300,000ms idle
			// 1000 milliseconds = 1 second
		}
	};
	
	var empInfoModal = document.getElementById("emp-info");
	empInfoModal.addEventListener("show.bs.modal", function (e) {
		var tblData = e.relatedTarget;
		var empTblId = tblData.getAttribute("data-bs-employee");
		var empTblName = tblData.getAttribute("data-bs-name");
		var empModalInfo = document.querySelector("#emp-search-output .emp-modal-id");
		var empModalInfoName = document.querySelector("#emp-search-output .emp-modal-name");
		empModalInfoName.innerHTML = empTblName;
		empModalInfo.innerHTML = empTblId;
	});
	document.querySelector(".emp-search").addEventListener("submit", function(e) {
		refreshTable = setInterval(fillTable(e.target.value), 5000);
		e.preventDefault();
	});
	document.querySelector("#emp-search-text").addEventListener("input", function(e) {
		stopTableTimer();
		refreshTable = setInterval(fillTable(e.target.value), 5000);
	});
	
	document.getElementById("btn-timeout").addEventListener("click", function() {
		//console.log("Stopped Timer");
		window.location.href = "./login.php";
		stopSessionTimer();
	});
	document.getElementById("nav-timeout").addEventListener("click", function() {
		//console.log("Stopped Timer");
		window.location.href = "./login.php";
		stopSessionTimer();
	});
</script>
</body>
</html>