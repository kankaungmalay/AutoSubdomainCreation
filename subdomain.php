<?php 

# Copyright (C) 2013 by kankaungmalay.
# All rights reserved.

ob_start();

session_start();

if(isset($_SESSION['id']))
{
	$uid = $_SESSION['id'];
}
if(isset($_SESSION['username']))
{
	$username = $_SESSION['username'];
}

include('header.php'); 
?>
<div id="content">
<?php

if(!empty($uid))
{
	$query = "SELECT `subdomain`, `username`, `email`, `db_name`, `db_user`, `entrydate` FROM `users` WHERE `id`='$uid'";
	$result = @mysql_query($query);
}
while($row = mysql_fetch_array ($result, MYSQL_NUM))
{
		//for subdomain
		$lists = $xmlapi->api2_query($account,'SubDomain','listsubdomains');
		$subdomain = array();
		$xml = new SimpleXMLElement($lists);
			foreach ($xml->data as $list) {
				$subdomain[] = $list;
		}
		$url = '';
		foreach ($subdomain as $domain){
			if($domain->subdomain == $row[0]) {
				$url = $domain->domain;
				$dir = $domain->basedir; // not available
				break;
			}
		} //end sudomain foreach loop
		
		//for database
		$dbs = $xmlapi->api2_query($account, "MysqlFE", "listdbs");
		$dbdata = array();
		$xmldb = new SimpleXMLElement($dbs);
			foreach ($xmldb->data as $data) {
				foreach ($data->userlist as $userlist){
					$dbdata[] = $userlist;
				}
			}

		foreach ($dbdata as $domain){
			if($domain->db == $row[3]) {
				$dbname = $domain->db;
				$dbusr = $domain->user; // not available
				break;
			}
		} //end database foreach loop		

echo "<div id='createdomain'>";
echo "<table align='center' cellpadding='2' cellspacing='1' >";
			
			echo "<tr>
						<th width='20%' class='tddata'>URL</th>
						<th width='10%' class='tddata'>User Name</th>
						<th width='20%' class='tddata'>Email</th>
						<th width='20%' class='tddata'>Server Space</th>
						<th width='10%' class='tddata'>DB Name</th>
						<th width='10%' class='tddata'>DB User</th>
						<th width='10%' class='tddata'>Date</th>
					</tr>";
			echo "<tr>";
			
			echo "<td class='tddata'><a href='http://". $url ."'>" . $url . "</a></td>";
			echo "<td class='tddata'>" . $row[1] . "</td>";
			echo "<td class='tddata'>" . $row[2] . "</td>";
			echo "<td class='tddata'>" . $dir ."</td>";
			echo "<td class='tddata'>" . $dbname . "</td>";
			echo "<td class='tddata'>" . $dbusr . "</td>";
			echo "<td class='tddata'>" . $row[5] . "</td>";
			echo "</tr>";
			
echo "</table>";
echo "</div>";
}
?>
</div><!-- end of content -->
<?php include('footer.php');?>