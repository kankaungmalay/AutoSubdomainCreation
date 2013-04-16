<?php
# Copyright (c) 2009, cPanel, Inc.
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without modification, are permitted provided 
# that the following conditions are met:
#
# * Redistributions of source code must retain the above copyright notice, this list of conditions and the 
#   following disclaimer.
# * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the 
#   following disclaimer in the documentation and/or other materials provided with the distribution.
# * Neither the name of the cPanel, Inc. nor the names of its contributors may be used to endorse or promote 
#   products derived from this software without specific prior written permission.
#
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED 
# WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A 
# PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR 
# ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED 
# TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) 
# HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING 
# NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
# POSSIBILITY OF SUCH DAMAGE.

require_once ('mysql_connect.php'); 

include("includes/xmlapi.php");

$ip = getenv('REMOTE_HOST');

$root_pass = getenv('REMOTE_PASSWORD');
// cPanel User Name eg; admin123
$account = "xxxx";
// cPanel link eg; example.com
$xmlapi = new xmlapi('xxxx');
// cPanel password
$xmlapi->password_auth($account,'xxxx');
$xmlapi->set_output("xml");
// cPanel port, it's not whm port
$xmlapi->set_port('2083');
$xmlapi->set_debug(1);
?>

<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
  	<title>Subdomain Creation</title>
  	<link rel="stylesheet" href="styles.css" type="text/css"/>
  	<script type="text/javascript" src="#"></script>
  	<link href="favicon.ico" rel="shortcut icon" />
</head>
<body>
<div id="container">
	<div id="header">
		<h2>Let's create subdomains externally without loggging into cPanel!</h2>
		<div id="nav">
			<ul>
				<?php 
					if(isset($uid)) 
					{
				?>
				<li><a href="logout.php?logout=<?php echo $uid; ?>">Log Out</a></li>
				<li>Hi <?php if(isset($username)) echo $username; ?>!</li>
				<?php					
					}
					else 
					{
						if(isset($_GET['user']))
						{
				?>
						<li><a href="xmlapicalls.php">Create New</a></li>
				<?php	
						}
						else 
						{
				?>
						<li><a href="xmlapicalls.php?user=login">Log In</a></li>		
				<?php	
						}
					}		
				?>
			</ul>
		</div><!-- end of nav -->
	</div><!-- end of header -->