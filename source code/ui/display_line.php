<?php header('Access-Control-Allow-Origin: *'); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Market Analysis</title>
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript">
		
		$(function() {
		var id=document.getElementById("hidden1");
		id = id.innerHTML;
		var c_name=document.getElementById("hidden2");
		c_name = c_name.innerHTML;
		//alert(id);
		var link = 'csvparse.php?callback=?&id='+id;
		//alert(link);
		$.getJSON(link, function(data) {
				//alert(data);
				// Create the chart
				$('#container').highcharts('StockChart', {
					
		
					rangeSelector : {
						selected : 1
					},
		
					title : {
						text : c_name
					},
					
					series : [{
						name : c_name,
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
	<?php 
		$id = $_GET["id"];
		$company_name=$_GET["company_name"];
	?>
	<div id="hidden1" style="display:none; "><?php echo $id; ?></div>
	<div id="hidden2" style="display:none;"><?php echo $company_name; ?></div>
</html>