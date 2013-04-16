<?php

# Copyright (C) 2013 by kankaungmalay.
# All rights reserved.

session_start();

// If no logout variable exists, redirect the user.
if (isset($_GET['logout'])) 
{

	$_SESSION = array(); // Destroy the variables.

	session_destroy(); // Destroy the session itself.

	header ("Location:  http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/xmlapicalls.php");
	
	exit(); // Quit the script.
	
} 
else 
{ 
	echo 'Could Not Log Out';
}

?>