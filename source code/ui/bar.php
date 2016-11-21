<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
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
<div id="container">
	<div id="header">
	
	<div>
	<!--
		<ul>
			<li><a href="">home</a></li>
			<li><a href="">about us</a></li>
			<li><a href="">contact us</a></li>
			<li><a href="">links</a></li>
		</ul>
	-->	
	</div>
	
	</div>
	<div id="content">
		
		<div id="right" style="float:right;">
		
			
			<?php // content="text/plain; charset=utf-8"
			
			require_once ('jpgraph-3.5.0b1/src/jpgraph.php');
			require_once ('jpgraph-3.5.0b1/src/jpgraph_bar.php');		
			$host="localhost"; // Host name 
			$username="root"; // Mysql username 
			$password=""; // Mysql password 
			$db_name="market_analysis"; // Database name 
			mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
			mysql_select_db("$db_name")or die("cannot select DB");
			$company = $_REQUEST['company'];
			
			$date_now = strtotime( "today");
			$date = strtotime("-1 day", $date_now);
			$next_date = strtotime("+1 day", $date_now);
			$date = date("Y-m-d",$date); 
			$next_date = date("Y-m-d",$next_date);
			
			if($company == "Select a Company")
			{
				echo 'Invalid Company</br></div>';
			}
			else
			{
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
			$graph = new Graph(350,350,'auto');
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
			?>
			<div id="container1" style=" margin: 0 auto; ">
			<a style="font-size:15px; text-decoration:underline; float:right;" href="display_line.php?id=<?php echo $c_id; ?>&company_name=<?php echo $company;?>" target="_blank" >Show the opinion trends for this company</a>
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
			 <div style=" font-size:12px;">
			 <h2 id="idea">Buy Recommendations:</h2>
			 <?php
			 
			 $res1 = mysql_query("select a.tweet_text,c.screen_name from tweets as a,tweet_tags as b,followers as c
								where a.tweet_id = b.tweet_id 
								and c.user_id= a.user_id
								and sentiment= 'b'
								and tag_id='".$c_id."'
								and valid_till between '".$date."' and '".$next_date."'
								order by valid_till desc
								limit 10");
								
			$res2 = mysql_query("select a.tweet_text, c.screen_name from tweets as a,tweet_tags as b, followers as c
								where a.tweet_id=b.tweet_id 
								and sentiment= 's'
								and tag_id='".$c_id."'
								and valid_till between '".$date."' and '".$next_date."'
								order by valid_till desc
								limit 10");
								
			if(mysql_num_rows($res1)==0)
				echo 'No buy predictions for this company.</br></br>';
			while($row1 = mysql_fetch_array($res1))
			{
				echo '<a href=analyst_profile.php?expert='.$row1[1].'>'.$row1[1].'</a></br>';
				echo $row1[0].'<br/><br/>';
					
			}
			?>
			</div>
			<div style=" font-size:12px;">
			 <h2 id="idea">Sell Recommendations:</h2>
			<?php
			
			if(mysql_num_rows($res2)==0)
				echo 'No sell predictions for this company.</br></br>';			
			while($row2 = mysql_fetch_array($res2))
			{
				echo '<a href=analyst_profile.php?expert='.$row2[1].'>'.$row2[1].'</a></br>';
				echo $row2[0].'<br/><br/>';
					
			}
			
			 ?>
			</div>
			</div>
		</div>
		<?php } }?>
		<div id="left" style="float:left;">
			<h2>Select Company</h2>
			
			
			<?php
			//company selection
			$query="SELECT company_id,company_name FROM company_names";
			$result=mysql_query($query);
		
			echo '<form name="companies" method="post" action="bar.php">
				  <select name="company">
				  <option value="Select a Company" selected>Select a Company</option>
				  ';
			
			while ($row = mysql_fetch_array($result))
			{
				echo '<option value="'.$row[1].'">'.$row[1].'</option>';
			}
			echo '</select>
				  </br>
				  <input type="submit" value="Get Review">
				  </form>';
				  
			?>
			<br><br><br>
			<h2>Select Expert</h2>
			<?php
			//expert selection
			$query="SELECT user_id,screen_name FROM followers";
			$result=mysql_query($query);
		
			echo '<form name="experts" method="post" action="analyst_profile.php">
				  <select name="expert">
				  <option value="Select an Expert" selected>Select an Expert</option>';
			
			while ($row = mysql_fetch_array($result))
			{
				echo '<option value="'.$row[1].'">'.$row[1].'</option>';
			}
			echo '</select>
				  </br>
				  <input type="submit" value="Get Review">
				  </form>';
			?>
			<br/><br/><br/>
			<a style="color: #11A0CF;font-size:18px;" href="best_sells.php"> View todays best sell</a>	
			<br/><br/>
			<a style="color: #11A0CF;font-size:18px;" href="index.php"> View todays best buy</a>	
		</div>		
	</div>
</div>
<div id="footer">

	<p>Copyright © 2005 All Rights Reserved </p>
</div>
</body>
</html>

	

