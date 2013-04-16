<?php 

# Copyright (C) 2013 by kankaungmalay.
# All rights reserved.

ob_start();

session_start();

include('header.php');

if (isset($_POST['create'])) 
{ //create subdomain
	
	$b = TRUE;
	$error = NULL;
	
	// Check for a subdomain name. checking letters, numbers and underscore btw 4 and 64, dot is not allowed in subdoman name
	if (preg_match ("/^[[:alnum:]_ ]{4,64}$/i", stripslashes(trim($_POST['subdomain'])))) 
	{
		$subdomain = escape_data($_POST['subdomain']);
	} 
	else 
	{
		$b=FALSE;
		$error .= 'Please enter a valid Subdomain Name!<br />';
	}	
	
	// Check for a username. checking letters, numbers and underscore btw 4 and 20
	if (preg_match ("/^[[:alnum:]_ ]{4,20}$/i", stripslashes(trim($_POST['username'])))) 
	{
		$username = escape_data($_POST['username']);
	} 
	else 
	{
		$b=FALSE;
		$error .= 'Please enter a valid Username!<br />';
	}
	
	// Check for an email address.
	if (preg_match ("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/", stripslashes(trim($_POST['email'])))) 
	{
		$email = escape_data($_POST['email']);
	} 
	else 
	{
		$b=FALSE;
		$error .= 'Please enter a valid Email Address!<br />';
	}
	
	// Check for a password and match against the confirmed password.
	if (preg_match ("/^[[:alnum:]]{4,20}$/", stripslashes(trim($_POST['password'])))) 
	{
		if ($_POST['password'] == $_POST['comfirm_pw']) 
		{
			$password = md5(escape_data($_POST['password']));
		} 
		else 
		{
			$b=FALSE;
			$error .= 'Your password did not match the confirmed password!<br />';
		}
	} 
	else 
	{
		$b=FALSE;
		$error .= 'Please enter a valid Password!<br />';
	}
	
	if ($b==TRUE) 
	{ // If everything's OK.
	
		$add = TRUE;
		
		// Make sure the subdomain name is available.
		$query_sub = "SELECT `id` FROM `users` WHERE `subdomain`='$subdomain'";		

		$result_sub = @mysql_query ($query_sub);

		if (mysql_num_rows($result_sub) != 0) 
		{ 
			$add = FALSE;
			$error .= 'This subdomain name is already taken.<br />'; 
		}
	
		// Make sure the Username is available.
		$query = "SELECT id FROM users WHERE username='$username'";		

		$result = @mysql_query ($query);

		if (mysql_num_rows($result) != 0) 
		{
			$add = FALSE;
			$error .= 'This username is already taken.<br />'; 
		}
			//if sudomain and username both are available
			if($add == TRUE) {
			
			//create subdomain
			$sub_name = $subdomain;
			$rootdomain = 'example.com';
			$usecanonicalname = 0; // used in api1
			$disallowdot = 1;
			$dir = '/public_html/subdomains/' . $subdomain;		
			$account = "xxxx";		
			
			$create = $xmlapi->api2_query($account, 'SubDomain','addsubdomain', array('dir'=>$dir,'disallowdot'=>$disallowdot,'domain'=>$sub_name,'rootdomain'=>$rootdomain));	
			//$create = $xmlapi->api1_query($account,'SubDomain','addsubdomain',array($sub_name,$rootdomain,$usecanonicalname,$disallowdot,$dir));  
			$xml = new SimpleXMLElement($create);
			foreach ($xml->data as $list) {
				$subdm_result = $list->result;
			}			
			// created sub domain		
			if($subdm_result == 1){	
					
				//create db, dbuser, add user to db	
				function getUniqueCode($length = "")
				{	
					$code = md5(uniqid(rand(), true));
					if ($length != "") return substr($code, 0, $length);
					else return $code;
				}
				
				$cpuser = "xxxx"; // cpanel account name
				$newdb = $username;  //unique - 64 chars - $cpuser_username(unique,20)
				$newuser = getUniqueCode(6); //unique - 16 chars - $cpuser_random-unique code
				$newpass = $username; //username(20 chars)
				
				// create a database //
				$adddb = $xmlapi->api1_query($cpuser, "Mysql", "adddb", array($newdb) );
				$xmldb = new SimpleXMLElement($adddb);
				
				// create a virtual database user //
				$adduser = $xmlapi->api1_query($cpuser, "Mysql", "adduser", 
	          array( $newuser, $newpass ) 
	          );
	      $xmluser = new SimpleXMLElement($adduser);
	          
				// create the relationship between virtual user and database //
				$literaldb = $cpuser . '_' . $newdb;
				$literaluser = $cpuser . '_' . $newuser;
				
				$xmlapi->api1_query($cpuser, "Mysql", "adduserdb",
				         array($literaldb,$literaluser,'SELECT INSERT UPDATE DELETE')
				         );	
				//created db, dbuser, add user to db         
		    if((empty($xmldb->error)) && (empty($xmluser->error))) { 
				         	
				// After creating subdomain, db, dbuser then Add the user account to our database.
				$query = "INSERT INTO users (subdomain, username, password, email, db_name, db_user, entrydate) VALUES ('$subdomain', '$username', '$password', '$email', '$literaldb', '$literaluser', NOW())";		

				$result = @mysql_query ($query); // Run the query.

				if ($result) { // If db insert is OK.
				
					/*max id*/
					$max ="SELECT `id`, `username` FROM `users` WHERE `id`=(SELECT MAX(id) FROM `users`)";
					
					$rst_max = @mysql_query($max);
					
					$row_max = mysql_fetch_array ($rst_max, MYSQL_NUM);
					
					$_SESSION['id'] = $row_max[0];
					$_SESSION['username'] = $row_max[1];
					
					ob_end_clean(); // Delete the buffer.
					// Send an email, if desired.
					header ("Location:  http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/subdomain.php");
					exit();			
					
				} else { // If it did not run OK.
					// Send a message to the error log, if desired.
					$error .= 'You could not be registered due to a system error. We apologize for any inconvenience.<br />';
				}//end of add data to db	
				
				}else{// Not created DB and DBUser
					$error .= $xmldb->error.'<br />';
					$error .= $xmluser->error.'<br />';
					$error .='Your Cpanel database could not be created. Try again later';	
				}
			}else{// Not created Subdomain
				$error .= $list->reason.'<br />';
				$error .= 'Your Cpanel subdomain could not be created. Try again later.';
			}	
			
			}
			

		mysql_close(); // Close the database connection.

	} else { // If one of the data tests failed.
		$error .= 'Please try again.'; 	
	}

} // End of the main Create Domain conditional. 

if (isset($_POST['login'])) { 

	$b = TRUE;
	$error = '';
	
	if (empty($_POST['username'])) { 
	
		$b=FALSE;
		
		$error .= 'You forgot to enter your username!<br />';
		
	} else {
	
		$username = escape_data($_POST['username']);
		
	}
	
	if (empty($_POST['password'])) { 
	
		$b=FALSE;
		
		$error .= 'You forgot to enter your password!<br />';
		
	} else {
	
		$password = md5(escape_data($_POST['password']));
		
	}
	
	if ($b==TRUE) { 
	
		$query = "SELECT id, username FROM users WHERE username='$username' AND password='$password'";
		
		$result = @mysql_query ($query);
		
		$row = mysql_fetch_array ($result, MYSQL_NUM); 
		
		if ($row) { 
					
				$_SESSION['id'] = $row[0];
				$_SESSION['username'] = $row[1];

				ob_end_clean(); // Delete the buffer.
				
				header ("Location:  http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/subdomain.php");
				exit();
				
		} else { 
			 
			$error .= 'The username and password entered do not match those on file.<br />';
		}
		
		mysql_close(); // Close the database connection.
		
	} else {
		
		$error .='Please try again.';	
	}

} // End of the main Login conditional. 


?>

<div id="content">
<?php if(isset($error)) echo '<div class="error">' . $error . '</div> ';?>
<?php if(isset($alert)) echo '<div class="alert">' . $alert . '</div> ';?>
	<form method="post" action="<?php echo $_SERVER ['PHP_SELF']; ?>" id="createdomain" name="formcheck" onsubmit="return formCheck(this);">
        		
 			<?php 
				if(isset($_GET['user'])){
					echo '
					<h3>LogIn</h3>
					<div class="field">User Name</div>
	        		<div class="field"><input type="text" name="username"  /></div>   
	        		<div class="field">Password</div>
	        		<div class="field"><input type="password" name="password"  /></div>
	        		<div class="submit"><input type="submit" name="login" value="Log In" /></div>'; 
				}else {
					echo '
					<h3>Create sub domain</h3>					
					<div class="field">SubDomain Name</div>
	        		<div class="field"><input type="text" name="subdomain" size="20" maxlength="64" /> .<span>example.com</span></div>  
	        		<div class="field">User Name</div>
	        		<div class="field"><input type="text" name="username" size="20" maxlength="20"  /><br /><span>User Name should be any letters, numbers and underscore between 4 and 20</span></div>   
	        		<div class="field">Password</div>
	        		<div class="field"><input type="password" name="password"  /></div>   
	        		<div class="field">Confirm Password</div>
	        		<div class="field"><input type="password" name="comfirm_pw"  /></div> 
	        		<div class="field">Email</div>
	        		<div class="field"><input type="text" name="email"  /></div>      
	            <div class="submit"><input type="submit" name="create" value="Create" /></div>';	
				}			
 			?>	
  	</form>   
	<div class="clear"></div> 
</div><!-- end of content -->
<?php include('footer.php');?>	