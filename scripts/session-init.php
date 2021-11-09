<?php
//header("Content-Type: application/json", true);

require("../myCon.php");
session_start();

/* $json = file_get_contents('php://input'); 
$post = json_decode($json, true);

var_dump($json);
echo "\n";
var_dump($post);
echo "\n";
var_dump($_POST);
echo "\n";
var_dump($_SESSION);
echo "\n";
echo "test: " . $_POST['src']; 
echo "\n"; */

if(isset($_POST['src'])) {
	$src = cleanTxt($_POST['src']);
	
	if($src == "index") {
		// json for index page
		if(isset($_SESSION['id'])) { // check if session actually exists.
			$userID = $_SESSION['id'];
			$userName = $_SESSION['emp-name'];
			if($_SESSION['admin'] == "1")
				$isAdmin = "true";
			else
				$isAdmin = "false";
			unset($_SESSION['error']); // clear any error message
			
			$output = array("error" => "0", "user-id" => "$userID", "user-name" => $userName, "user-admin" => $isAdmin);
		} else {
			$output = array("error" => "1", "message" => "session not found");
			$_SESSION['error'] = "Unknown login token. Please try to login again later.";
		}
		//var_dump($output);
		echo json_encode($output);
	}
	
	if($src == "login") {
		// json for login page
		if(isset($_SESSION['error'])) {
			unset($_SESSION['error']);
			$output = array("error" => "0", "message" => "Error cleared.");
		} else {
			$output = array("error" => "1", "message" => "No changes made.");
		}
		echo json_encode($output);
	}
}

function cleanTxt($x) {
	$x = trim($x);
	$x = htmlspecialchars($x);
	return $x;
}

?>