// Initialize bootstrap utilities
var sessionModal = new bootstrap.Modal(document.querySelector("#page-unavailable"));
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl);
});
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl);
});

var inactivityTime = function () {
	var time;
	window.onload = resetTimer;
	// DOM Events
	document.onmousemove = resetTimer;
	document.onkeydown = resetTimer;

	function setidle() {
		var xhr = new XMLHttpRequest();
		xhr.onload = function() {
			//console.log("idle");
			//console.log(this.responseText);
			window.location.href = "./login.html";
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

function getSession() {
	//console.log("Session checked.");
	var xhr = new XMLHttpRequest();
	xhr.onload = function() {
		//console.log(this.responseText);
		try {
			var o = JSON.parse(this.responseText);
			if(o['session'] == "0") {
				document.querySelector("#page-unavailable h5").innerHTML = "System Message";
				document.querySelector("#page-unavailable .modal-body").innerHTML = o['session-msg'];
				sessionModal.show();
			}
		} catch(ex) {
			console.log(ex);
		}
	}
	xhr.open("POST", "./scripts/session-init.php", true);
	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xhr.send("src=session");
}

document.querySelector("#btn-timeout").addEventListener("click", function() {
	//console.log("Stopped Timer");
	var xhr = new XMLHttpRequest();
	xhr.onload = function() {
		//console.log("idle");
		window.location.href = "./login.html";
	}
	xhr.open("GET", "./scripts/session-logout.php", true);
	xhr.send();
	//stopSessionTimer();
});
document.querySelector("#page-unavailable").addEventListener("hidden.bs.modal", function() {
	window.location.href = "./login.html";
});

