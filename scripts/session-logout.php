<?php
	session_start();
	
	if(isset($_SESSION)) {
		// todo: add to database
		
		session_unset();
		session_destroy();
		$output = array("status" => "OK");
		echo json_encode($output);
	}
?>