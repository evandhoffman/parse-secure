SSH hacking attempts
<?php
require_once "Net/GeoIP.php";

$geoip = Net_GeoIP::getInstance("/var/www/html/GeoLiteCity.dat", Net_GeoIP::SHARED_MEMORY);

	$dbc = pg_connect("dbname=sshlog host=127.0.0.1 user=sshlog password=sshlog");
	$res = pg_query("select * from ssh_hack_attempts order by datetime desc limit 500");

	if (!$res) {
		echo "DB Error.  Sadface.\n";
		exit;
	}

	print "<table border='1'><tr><td>Date</td><td>IP</td><td>User</td><td>Where?</td></tr>";
	while ($row = pg_fetch_assoc($res)) {
		$location = $geoip->lookupLocation($row['remote_addr']);
		//print_r($location);
		print "<tr><td>$row[datetime]</td><td>$row[remote_addr]</td><td>$row[username]</td><td>$location->countryName, $location->city</td></tr>\n";
	}
	print "</table>\n";
?>
