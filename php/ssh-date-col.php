<?php

	$days = 30;
	if (isset($_GET['days'])) {
		$x = intval($_GET['days']);
		if ($x > 0 && $x < 10000) {
			$days = $x;
		}
	}

	$sql_date_format = 'Mon DD, YYYY';

	$dbc = pg_connect("dbname=sshlog host=127.0.0.1 user=sshlog password=sshlog");
	$res = pg_query("select to_char(a.the_date, '$sql_date_format') as the_date, count(b.remote_addr) as c  from dates a
left join ssh_hack_attempts b on to_char(a.the_date,'$sql_date_format') =
to_char(b.datetime,'$sql_date_format') where (now() - a.the_date) < interval '$days days' and
a.the_date <= now() group by a.the_date order by a.the_date;");
//	$res = pg_query("select case when country_code is null then 'unknown' else country_code end as country_code, count(1) as c from ssh_hack_attempts where (now() - datetime) < interval '$days days' group by country_code");

	if (!$res) {
		echo "DB Error.  Sadface.\n";
		exit;
	}

	$data = array();
	$dates = array();
	while ($row = pg_fetch_assoc($res)) {
		$dates[] = $row['the_date'];
		$data[] = intval($row['c']);
	//	print "<tr><td>$row[remote_addr]</td><td>$row[c]</td></tr>\n";
	}
	
	#json_encode($data);
?>
<html>
<head>
<title>Hacks by date for the past <?php print $days; ?> Days</title>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js" type="text/javascript"></script>
<script src="/highcharts/js/highcharts.js" type="text/javascript"></script>

<script type='text/javascript'>
			
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						defaultSeriesType: 'column'
					},
					title: {
						text: 'Daily SSH Attacks'
					},
					xAxis: {
						labels: {
							rotation: 90,
							align: "left"
						},
						categories: 
							<?php print json_encode($dates); ?>
					},
					yAxis: {
						min: 0,
						title: {
							text: 'Attempts'
						}
					},
					legend: {
						layout: 'vertical',
						backgroundColor: '#FFFFFF',
						align: 'left',
						verticalAlign: 'top',
						x: 100,
						y: 70,
						floating: true,
						shadow: true
					},
					tooltip: {
						formatter: function() {
							return ''+
								this.x +': '+ this.y +' mm';
						}
					},
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						}
					},
				        series: [{
						name: 'Haxors',
						data: <?php print json_encode($data); ?>
				
					}]
				});
				
				
			});
				
	
</script>

</head>
<body>


<div id='container' style="width: 800px; height: 500px; margin: 0 auto"></div>

<a href="ssh-country-pie.php">Attacks broken out by country</a>

</body>
</html>
