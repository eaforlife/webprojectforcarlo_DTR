<!DOCTYPE html>
<html>
<head>
	<title>Session Based Attendance - Login</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="./style/bootstrap.min.css" rel="stylesheet">
	<link href="./style/fonts/bootstrap-icons.css" rel="stylesheet">
	<style>
		html,body {
			height: 100%;
		}
		body {
			background-color: #EAF0FA;
		}
	</style>
</head>
<body>
	<div class="h-100 d-flex align-items-center justify-content-center">
		<div class="container p-5 w-50 shadow bg-light mb-3 rounded">
			<div class="row">
				<div class="col text-center">
					<h1>Attendance System</h1>
					<div class="container login-validate">
						<div class="spinner-border my-5 login-loading d-none" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>
						<div class="my-5 login-success d-none">
							<p class="fw-bold text-success">Successfully logged in. Redirecting you to home page.</p>
							<div class="spinner-grow my-2" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
						</div>
						<form class="my-5 g-2" method="POST" novalidate>
							<div class="form-group input-group mb-2">
								<label class="col-lg-2 col-form-label" for="login-username">Username: </label>
								<div class="col-lg-10">
									<input type="text" class="form-control" name="login-username" id="login-username" aria-describedby="invalid-user" required>
									<div class="invalid-feedback">Invalid username/password</div>
								</div>
							</div>
							<div class="form-group input-group mb-4">
								<label class="col-lg-2 col-form-label" for="login-password">Password: </label>
								<div class="col-lg-10">
									<input type="password" class="form-control" name="login-password" id="login-password" required>
									<div class="invalid-feedback">Invalid username/password</div>
								</div>
							</div>
							<button type="submit" class="btn btn-primary"><i class="bi bi-box-arrow-in-right"></i> Login</button>
							<button type="reset" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</button>
						</form>
					</div>
				<div>
			</div>
		</div>
	</div>

<!-- Javascript end of page for faster load times -->
<script src="./scripts/bootstrap.bundle.min.js"></script>
<script>
	var loginForm = document.querySelector(".login-validate form");
	var loginInput = document.querySelector(".login-validate form input");
	
	destroySession();
	
	loginInput.addEventListener("change", function() {
		// remove any validation control when input is detected
		this.classList.remove("is-invalid");
	});
	
	loginForm.addEventListener("reset", function() {
		for(var x=0; x < loginInput.length; x++)
			loginInput[x].classList.remove("is-invalid");
	});
	
	loginForm.addEventListener("submit", function (e) {
		formMode(false);
		var xhr = new XMLHttpRequest();
		var frmData = new FormData(loginForm);
		frmData = JSON.stringify(Object.fromEntries(frmData));
		
		xhr.onload = function() {
			jsonObj = JSON.parse(this.responseText);
			if(jsonObj['error'] == "0") {
				loginForm.classList.add("d-none");
				var divSuccess = document.querySelector(".login-success");
				divSuccess.classList.remove("d-none");
				setTimeout(function() {
					window.location.href = "./index.php";
				}, 5000);
			} else {
				//loginForm.classList.add("was-validated");
				var loginInput = document.querySelectorAll(".login-validate form input");
				for(var x=0; x < loginInput.length; x++)
					loginInput[x].classList.add("is-invalid");
			}
			// return false // uncomment if you want to debug ajax/json page
			formMode(true);
		}
		xhr.open("POST", "./scripts/session-login.php", true);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.send(frmData);
		
		e.preventDefault();
		e.stopPropagation();
	});
	
	function formMode(x) {
		var el = loginForm.elements;
		if(x == true) {
			// reverts form to normal state.
			for(var ctr=0, ctrMax=el.length; ctr < ctrMax; ctr++)
				el[ctr].readOnly = false;
		} else {
			// set form to readonly. preventing any duplicate if data interruption happens.
			for(var ctr=0, ctrMax=el.length; ctr < ctrMax; ctr++)
				el[ctr].readOnly = true;
		}
	}
	
	function destroySession() {
		console.log("start session destroy");
		var xhr = new XMLHttpRequest();
		xhr.onload = function() {
			var jObj = JSON.parse(this.responseText);
			console.log(jObj);
		}
		xhr.open("GET", "./scripts/session-logout.php", true);
		xhr.send();
	}
</script>
</body>
</html>