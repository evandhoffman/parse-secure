SSH hacking attempts
<?php

	$dbc = pg_connect("dbname=sshlog host=127.0.0.1 user=sshlog password=sshlog");
	$res = pg_query("select remote_addr, count(1) as c from ssh_hack_attempts where (now() - datetime) < interval '30 days' group by remote_addr");

	if (!$res) {
		echo "DB Error.  Sadface.\n";
		exit;
	}

	print "<table border='1'><tr><td>Date</td><td>IP</td></tr>";
	while ($row = pg_fetch_assoc($res)) {
		print "<tr><td>$row[remote_addr]</td><td>$row[c]</td></tr>\n";
	}
	print "</table>\n";
?>
