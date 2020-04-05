<!DOCTYPE html>
<!-- Use of CSS Grid: https://www.w3schools.com/css/css_grid.asp -->
<html>
<head>
<title>Date Time Record - View All Records</title>
<meta name='viewport' content='width=device-width, initial-scale=1.0'> <!-- Call viewport for responsive html css: https://www.w3schools.com/html/html_responsive.asp -->
<link rel="stylesheet" type="text/css" href="./style/style.css">
<link rel="stylesheet" type="text/css" href="./style/style-1.css">
</head>
<body>

<!-- header -->
<?php 
	try {
		require("./require_header.php");
	} catch (ErrorException $err) {
		echo "<h1>Unable to load header module. Please try again later.</h1>";
		echo "<p>" . print_r($err) . "</p>";
		die();
	}
	
 ?>

<div class="container bottom-space">
	<div class="row">
		<div class="col-md-12 justify-center">
			<h2 id="test">Employer's Dashboard</h2>
		</div>
	</div>

	<div class="row bottom-space">
		<div class="col-md-2">&nbsp;</div>
		<div class="col-md-8 col-flex">
			<form method="post" class="searchForm" action="#">
				<label for='searchbox'>Search for:&nbsp;</label>
				<input type="text" name="srchTxt" id="searchbox" class="form-flex" placeholder="Search for name or employee id">
				<input type="submit" value="Search">
			</form>
		</div>
		<div class="col-md-2">&nbsp;</div>
	</div>
	<hr class="shadow">
	<div class="row">
		<div class="col-md-12">&nbsp;</div>
	</div>
	<div class="row">
		<div class="col-md-1">&nbsp;</div>
		<div class="col-md-10 col-flex">
			<table id="tableContent"></table>
		</div>
		<div class="col-md-1">&nbsp;</div>
		
		<!-- modal -->
		<div class="col-md-1">&nbsp;</div>
		<div id="delModal" class="modal-cancel">
			<div class="modal-cancel-box">
				<div class="modal-cancel-header">
					<span id="close">&times;</span>
					<h2>Warning!</h2>
				</div>
				<div class="modal-cancel-body">
					<p>Are you sure you want to delete "<span id='delModalValue'> </span>"?</p>
				</div>
				<div class="modal-cancel-footer">
					<button class="btn btn-danger" id="tableActionYes">Continue</button>
					<button class="btn btn-info" id="tableActionCancel">Cancel</button>
				</div>
			</div>
		</div>
			
	</div>
</div>

<?php 
	try {
		require("./require_footer.php");
	} catch (ErrorException $err) {
		echo "<h1>Unable to load footer module. Please try again later.</h1>";
		echo "<p>" . print_r($err) . "</p>";
		die();
	}
 ?>
<!-- javascript here -->
<script>
	var modal = document.getElementById("delModal");
	var btnDel = document.getElementById("frmDel");
	var btnClose = document.getElementById("tableActionCancel");
	var btnX = document.getElementById("close");
	
	var srchText = document.getElementById("searchbox");
	
	fetchEmp();
	
	srchText.onkeyup = function() {
		fetchEmp();	
	}
	
	btnClose.onclick = function() {
		clearModal();
	}
	btnX.onclick = function() {
		clearModal();
	}
	window.onclick = function(event) {
		if(event.target == modal) {
			clearModal();
		}
	}
	function modalEditEmp(x) {
		window.location.href = './edit.php?e=' + x.toString();
	}
	function modalViewEmp(x) {
		var dt = new Date();
		var m = dt.getMonth();
		var y = dt.getFullYear();
		var m = parseInt(m) + 1;
		var newM = m.toString();
		if(newM.length == "1") {
			newM = "0" + newM;
		}
		window.location.replace("./overview.php?e=" + x);
	}
	function modalDelEmp(x) {
		var empName = "empName" + x.toString();
		document.getElementById("delModalValue").innerHTML = document.getElementById(empName).value;
		modal.style.display = "block";
		
		if(x.toString() == '<?php echo $loginID; ?>') {
			document.getElementById("tableActionYes").disabled = true;
			document.getElementById("tableActionYes").innerHTML = "Can't delete yourself!";
			
		} else {
			document.getElementById("tableActionYes").disabled = false;
			document.getElementById("tableActionYes").innerHTML = "Delete";
			document.getElementById("tableActionYes").onclick = function() {
				document.getElementById("tableAction-"+x.toString()).submit();
				clearModal();
			}
		}
	}
	function clearModal() {
		modal.style.display = "none";
		document.getElementById("delModalValue").innerHTML = " ";
	}
	
	function fetchEmp() {
		var xhttp;
		var x = document.getElementById("searchbox").value;
		xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if(xhttp.readyState == XMLHttpRequest.DONE) {
				if(xhttp.status == 200) {
					document.getElementById("tableContent").innerHTML = xhttp.responseText;
				}
			}
		};
		xhttp.open("GET", "getemployee.php?p=records&e="+x, true);
		xhttp.send();
	}
	
</script>
</body>
</html>