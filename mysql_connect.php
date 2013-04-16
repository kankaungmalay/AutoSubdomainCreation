<?php 

// This script sets the error reporting and logging for the site.

//error_reporting (0); // Production level
error_reporting (E_ALL); // Development level

// Use my own error handling function.
function my_error_handler ($e_number, $e_message, $e_file, $e_line) {
	$message = 'An error occurred in script ' . $e_file . ' on line ' . $e_line . ": $e_message";
	//error_log ($message, 1, 'mon@databasemarketing.com.sg'); // Production (send email)
	echo '<font color="red" size="1">', $message, '</font>'; // Development (print the error in red)
}
set_error_handler('my_error_handler');

// This file contains the database access information. This file also establishes a connection to MySQL and selects the database.

// Set the database access information as constants.
DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', 'root');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'auto_subdomain');

if ($dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD)) { // Make the connnection.

	if (!mysql_select_db (DB_NAME)) { // If it can't select the database.
	
		// Handle the error.
		my_error_handler (mysql_errno(), 'Could not select the database: ' . mysql_error());	
		
		// Print a message to the user, include the footer, and kill the script.
		echo '<p><font color="red">The site is currently experiencing technical difficulties. We apologize for any inconvenience.</font></p>';
		
		exit();
		
	} // End of mysql_select_db IF.
	
} else { // If it couldn't connect to MySQL.

	// Print a message to the user, include the footer, and kill the script.
	my_error_handler (mysql_errno(), 'Could not connect to the database: ' . mysql_error());
	echo '<p><font color="red">The site is currently experiencing technical difficulties. We apologize for any inconvenience.</font></p>';
	
	exit();
	
} // End of $dbc IF.

// Function for escaping and trimming form data.
function escape_data ($data) { 
	global $dbc;
	if (ini_get('magic_quotes_gpc')) {
		$data = stripslashes($data);
	}
	return mysql_real_escape_string (trim ($data), $dbc);
} // End of escape_data() function.
?>