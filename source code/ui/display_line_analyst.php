<?php header('Access-Control-Allow-Origin: *'); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Highstock with dynamic data</title>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript">
		
		$(function() {
		$.getJSON('csvparse_analyst.php?callback=?', function(data) {
				alert(data);
				// Create the chart
				$('#container').highcharts('StockChart', {
					
		
					rangeSelector : {
						selected : 1
					},
		
					title : {
						text : 'ABC Stock Price'
					},
					
					series : [{
						name : 'ABC',
						data : data,
						tooltip: {
							valueDecimals: 2
						}
					}]
				});
			});
		
		});
		
		</script>
	    <script src="js/highstock.js"></script>
		<script src="js/exporting.js"></script>


	</head>
	<body>
		<div id="container" style="height: 500px; min-width: 500px"></div>
	</body>
</html>