<?php

	$days = 30;
	if (isset($_GET['days'])) {
		$x = intval($_GET['days']);
		if ($x > 0 && $x < 10000) {
			$days = $x;
		}
	}

	$dbc = pg_connect("dbname=sshlog host=127.0.0.1 user=sshlog password=sshlog");
	$res = pg_query("select case when country_code is null then 'unknown' else country_code end as country_code, count(1) as c from ssh_hack_attempts where (now() - datetime) < interval '$days days' group by country_code");

	if (!$res) {
		echo "DB Error.  Sadface.\n";
		exit;
	}

	$data = array();
	while ($row = pg_fetch_assoc($res)) {
		$data[] = array($row['country_code'], intval($row['c']));
	//	print "<tr><td>$row[remote_addr]</td><td>$row[c]</td></tr>\n";
	}
	
	#json_encode($data);
?>
<html>
<head>
<title>Hacks by country for the past <?php print $days; ?> Days</title>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
<script src="/highcharts/js/highcharts.js" type="text/javascript"></script>

<script type='text/javascript'>
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'piechart',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: 'Countries that have attempted to hack this box over the past <?php print $days; ?> days.'
					},
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
						}
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								color: '#000000',
								connectorColor: '#000000',
								formatter: function() {
									return '<b>'+ this.point.name +'</b>: '+this.y+' ('+ this.percentage.toFixed(2) +'%)';
								}
							}
						}
					},
				    series: [{
						type: 'pie',
						name: 'Browser share',
						data: <?php echo json_encode($data); ?>
					}]
				});
			});
				
</script>

</head>
<body>


<div id='piechart'></div>


</body>
</html>
