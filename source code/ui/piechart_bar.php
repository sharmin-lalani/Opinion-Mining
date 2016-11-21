d <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<title>Market Analysis</title>
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=iso-8859-1" />

	
	<meta name="keywords" content="" />
	<meta name="description" content="" />	
	<meta http-equiv="imagetoolbar" content="no" />
	<link href="style.css" rel="stylesheet" type="text/css" />
	
	<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
	</head>

<body>

		<br><br><br>
		
		<div align="center" style="top:50px;">
		
			
			<?php // content="text/plain; charset=utf-8"
			$date_now = strtotime( "today");
	$date = strtotime("-1 day", $date_now);
	$next_date = strtotime("+1 day", $date_now);
			$date = date("Y-m-d",$date); 
			$next_date = date("Y-m-d",$next_date);
			require_once ('jpgraph-3.5.0b1/src/jpgraph.php');
			require_once ('jpgraph-3.5.0b1/src/jpgraph_bar.php');		
			$host="localhost"; // Host name 
			$username="root"; // Mysql username 
			$password=""; // Mysql password 
			$db_name="market_analysis"; // Database name 
			mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
			mysql_select_db("$db_name")or die("cannot select DB");
			$company = $_REQUEST['company'];
			$query1 = mysql_query("SELECT company_id FROM company_names WHERE company_name='".$company."'");
			while($r1 = mysql_fetch_array($query1))
			{
				$c_id=$r1['company_id'];
			}
			$query = mysql_query("SELECT sentiment FROM tweet_tags,tweets WHERE tag_id=".$c_id." and tweets.tweet_id=tweet_tags.tweet_id and valid_till between '".$date."' and '".$next_date."'");
			
			$buy=0;
			$sell=0;
			while($r = mysql_fetch_array($query))
			{	
				$sent  = $r['sentiment'];
				if($sent != NULL)
				{
					if($sent=='b')
					{
						$buy++;
					}
					else if($sent=='s')
					{
						$sell++;
					}
				}
			}
			if($buy==0 && $sell==0)
			{
			echo '<h2>No predictions for this company today.</h2></div>';
			
			}
			else
			{
			$b=$buy*100/($buy+$sell);
			$s=$sell*100/($buy+$sell);
			$datay=array($b,$s);
			// Create the graph. These two calls are always required
			//echo 
			$graph = new Graph(300,300,'auto');
			$graph->SetScale("textlin");
	
			//$theme_class="DefaultTheme";
			//$graph->SetTheme(new $theme_class());
	
			// set major and minor tick positions manually
			$graph->yaxis->SetTickPositions(array(0,5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100), array(15,45,75,105,135));
			$graph->SetBox(false);
	
			//$graph->ygrid->SetColor('gray');
			$graph->ygrid->SetFill(false);
			$graph->xaxis->SetTickLabels(array('Buy','Sell'));
			$graph->yaxis->HideLine(false);
			$graph->yaxis->HideTicks(false,false);
			
			// Create the bar plots
			$b1plot = new BarPlot($datay);
			
			// ...and add it to the graPH
			$graph->Add($b1plot);


			$b1plot->SetColor("white");
			$b1plot->SetFillGradient("#4B0082","white",GRAD_LEFT_REFLECTION);
			$b1plot->SetWidth(45);
			$graph->title->Set($company);
			// Display the graph
			$img = $graph->Stroke(_IMG_HANDLER);
			ob_start();
			imagepng($img);
			$imageData = ob_get_contents();
			ob_end_clean();
			//$graph->Stroke();
			/*echo '<div id="review" style="float:right;">'.$buy.' experts say buy the shares.</br>'.$sell.' experts say sell the shares.</div>';
			*/
			?>
			<div id="container1" style=" margin: 0 auto;">
			<a style="font-size:15px; text-decoration:underline; float:right;" href="display_line.php?id=<?php echo $c_id; ?>&company_name=<?php echo $company;?>"  >Show the opinion trends for this company</a>
			<br/><br/>
			<div style="font-size:18px">Current Prediction:<br/><br/></div>
			<img src="data:image/png;base64,
			<?php
			echo(base64_encode($imageData));
			?>" />
			<div style=" text-align:center; ">
				<h3 style="color:green;"><?php echo number_format((float)$b, 2, '.', ''); ?>  % of analyst say buy.</h3>
				<h3 style="color:red;"><?php echo number_format((float)$s, 2, '.', '');  ?>  % of analyst say sell.</h3>
			 </div>
			

			</div>
		<?php } ?>
</div>

</body>
</html>

	

