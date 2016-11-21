<?php header('Access-Control-Allow-Origin: *'); ?>
<!DOCTYPE HTML>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Market Analysis</title>
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript">
		
		$(function() {
		var expert=document.getElementById("hidden1");
		expert = expert.innerHTML;
		/*var c_name=document.getElementById("hidden2");
		c_name = c_name.innerHTML;*/
		//alert(id);
		var link = 'analyst_rating.php?callback=?&expert='+expert;
		//alert(link);
		$.getJSON(link, function(data) {
				if(data == '')
				{
					var m = document.getElementById("message");
					m.innerHTML = "Not Sufficient Data";
				}
				
				else
				{
				$('#container').highcharts('StockChart', {
					
		
					rangeSelector : {
						selected : 1
					},
		
					title : {
						text : expert+": Success trend"
					},
					
					series : [{
						name : expert,
						data : data,
						tooltip: {
							valueDecimals: 2
						}
					}]
				});
				}
			});
		
		});
		
		</script>
	    <script src="js/stock/highstock.js"></script>
		<script src="js/stock/exporting.js"></script>


	</head>
	<body>
	<div id="message" style="font-size:30px; text-align:center;"></div>
		<div id="container" style="height: 500px; min-width: 500px"></div>
	</body>
	<?php 
		$expert = $_GET["expert"];
		//$company_name=$_GET["company_name"];
	?>
	<div id="hidden1" style="display:none; "><?php echo $expert; ?></div>
	
</html>