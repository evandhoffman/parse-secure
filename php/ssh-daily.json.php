<?php
	header("Content-Type: text/plain");

	$dbc = pg_connect("dbname=sshlog host=127.0.0.1 user=sshlog password=sshlog");
	$res = pg_query("select remote_addr, count(1) as c from ssh_hack_attempts where (now() - datetime) < interval '30 days' group by remote_addr");

	if (!$res) {
		echo "DB Error.  Sadface.\n";
		exit;
	}

	$data = array();
	while ($row = pg_fetch_assoc($res)) {
		$data[] = array($row['remote_addr'], intval($row['c']));
	//	print "<tr><td>$row[remote_addr]</td><td>$row[c]</td></tr>\n";
	}
	
	print json_encode($data);
?>
