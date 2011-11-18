SSH hacking attempts
<?php
require_once "Net/GeoIP.php";


	$dbc = pg_connect("dbname=sshlog host=127.0.0.1 user=sshlog password=sshlog");
	$res = pg_query("select * from ssh_hack_attempts order by datetime desc limit 30");

	if (!$res) {
		echo "DB Error.  Sadface.\n";
		exit;
	}

	print "<table border='1'><tr><td>Date</td><td>IP</td><td>User</td><td>Where?</td></tr>";
	while ($row = pg_fetch_assoc($res)) {
		//print_r($location);
		print "<tr><td>$row[datetime]</td><td>$row[remote_addr]</td><td>$row[username]</td><td>$row[country_name], $row[city]</td></tr>\n";
	}
	print "</table>\n";
?>
