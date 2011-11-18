<?php

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
	
	#json_encode($data);
?>
<html>
<head>
<title>By IP for the past X Days</title>

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
						text: 'IP addresses that have attempted to hack my home linux box over the past 30 days.'
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

<H1>If this works there should be a pie chart below.</H1>

<div id='piechart'></div>

<H2>Pie chart is above here.</H2>

</body>
</html>
