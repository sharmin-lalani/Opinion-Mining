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
	<script type="text/javascript">
	
	var expert;
	
	function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		alert("Your Browser does not support ajax!");
	}
}

//Our XmlHttpRequest object to get the auto suggest
var searchReq = getXmlHttpRequestObject();

//Called from keyup on the search textbox.
//Starts the AJAX request.
function getlist(name) 
{
	//alert(name);
	if (searchReq.readyState == 4 || searchReq.readyState == 0) 
	{
		searchReq.open("GET","get_analyst_tweets.php?comp_name="+name+"&expert="+expert+"&set=buy",true);
		
		searchReq.onreadystatechange = addlist; 
		searchReq.send(null);
	}		
}



function addlist()
{
	if (searchReq.readyState == 4 && searchReq.status==200)
{
	//var str =searchReq.responseText.split("\n");
	
	//alert(searchReq.responseText);
	//table.innerHTML = searchReq.responseText;
	var rows = JSON.parse(searchReq.responseText);
	//alert(rows[0][0]);
	
	var len=rows.length;
	//alert(len);
	var str="<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
	for(var i=0;i<len; i++)
	{
		for(var j=0; j<2; j++)
		{
			str = str+rows[i][j]+"<br/>";
		}
		str=str+"<br/>"
	}
	table.innerHTML = str;
	
/*	for(i=0; i < str.length-1; i++) {
		alert(i);
		var t= str[i].split("<br/>")
		alert(t);*/
		//Build our element string. 
		/*var suggest = "<tr><td>" + "<a href=profile.jsp?username="+str[i]+">"+str[i] + "</a></td><td><a href=\"start_game.jsp?username="+str[i]+"\">&nbsp;&nbsp;&nbsp;Start a Game</a></td></tr>";
		table.innerHTML += suggest;*/
	/*	var suggest = "<tr><td>" + "<a href=#>"+t[0] + "</a>&nbsp;&nbsp;&nbsp;</td>"+
		"<td>"+t[1]+"</td></tr>";
		table.innerHTML += suggest;*/
//	}
	/*if(str.length==1)
	table.innerHTML = "<tr><td>There are no results to display.</td></tr>";*/
	
	
	
	
}
}

		$(document).ready(function() {
			table=document.getElementById("table");
			var ex=document.getElementById("hidden");
			expert = ex.innerHTML;
			//alert(expert);
		
		
		
			var options_a = {
				chart: {
	                renderTo: 'container1',
	                plotBackgroundColor: null,
	                plotBorderWidth: null,
	                plotShadow: false
	            },
	            title: {
	                text: ''
	            },
	            tooltip: {
	                formatter: function() {
	                    return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
	                }
	            },
	            plotOptions: {
	                pie: {
	                    allowPointSelect: true,
	                    cursor: 'pointer',
						point:{
								events: {
									click: function() {
										//alert (this.name);
										getlist(this.name);
										
													  }
										}
							},
	                    dataLabels: {
	                        enabled: true,
	                        color: '#000000',
	                        connectorColor: '#000000',
	                        formatter: function() {
	                            return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage) +' %';
	                        }
	                    }
	                }
	            },
	            series: [{
	                type: 'pie',
	                name: 'Browser share',
	                data: []
	            }]
	        }
			//alert("hi");
			var link = "analyst_buy.php?analyst="+expert;
			//alert(link);
	        $.getJSON(link, function(json) {
				options_a.series[0].data = json;
	        	chart = new Highcharts.Chart(options_a);
	        });
	        
	        
	      });   
		  
	</script>
		
	<script src="js/highcharts.js"></script>
	<script src="js/exporting.js"></script>
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
		
		<div id="right" style="font-color: black">
		
		<?php 
			$host="localhost"; // Host name 
			$username="root"; // Mysql username 
			$password=""; // Mysql password 
			$db_name="market_analysis"; // Database name 
			mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
			mysql_select_db("$db_name")or die("cannot select DB");
		
			$expert = $_REQUEST['expert'];
			$query = "select rating from followers where screen_name='".$expert."'";
			$res = mysql_query($query);
			$r = mysql_fetch_array($res);
			$rating = $r[0];
			
			$date_now = strtotime( "today");
			$date = strtotime("-1 day", $date_now);
			$next_date = strtotime("+1 day", $date_now);
			$date = date("Y-m-d",$date); 
			$next_date = date("Y-m-d",$next_date);
			//echo $date;
			//echo $next_date;
	
		$result_s = mysql_query("select a.tweet_id,count( a.sentiment), a.tag_id, company_name
							    from tweet_tags as a, tweets as b , company_names
								where b.user_id = (select user_id from followers where screen_name='STOCKSTIPS') 
								and b.valid_till between '".$date."' and '".$next_date."'
								and a.tweet_id = b.tweet_id
								and sentiment='s'
								and company_name not in ('sensex','nifty')
								and company_id=tag_id
								group by tag_id
                                order by valid_till desc
                                limit 3");
								
		$result_b = mysql_query("select a.tweet_id,count( a.sentiment), a.tag_id, company_name
							    from tweet_tags as a, tweets as b , company_names
								where b.user_id = (select user_id from followers where screen_name='".$expert."') 
								and b.valid_till between '".$date."' and '".$next_date."'
								and a.tweet_id = b.tweet_id
								and sentiment='b'
								and company_name not in ('sensex','nifty')
								and company_id=tag_id
								group by tag_id
								order by valid_till desc
                                limit 3");
		$rows_b = array();
		$rows_s = array();
		$final_s = array();
		$final_b = array();
		$i = 0;
		$j = 0;
		while($r = mysql_fetch_array($result_b))
		{
			$count=$r[1];
			$cn=$r[3];
			$rows_b[$cn] = $count;
		}
		while($r = mysql_fetch_array($result_s))
		{
			$count=$r[1];
			$cn=$r[3];
			$rows_s[$cn] = $count;
		}
		foreach($rows_s as $s => $cs)
			foreach($rows_b as $b => $cb)
				{
					if($s == $b)
						{
							if($cs > $cb)
								{
									$final_s[$s] = $cs;
									unset($rows_s[$s]);
									unset($rows_b[$s]);
								}
							if($cb > $cs)
								{
									$final_b[$b] = $cb;
									unset($rows_s[$b]);
									unset($rows_b[$b]);
								}
						}
				}
		$final_s = array_merge($final_s,$rows_s);
		$final_b = array_merge($final_b,$rows_b);
		//print_r($final_s );
		//print_r($final_b);
				
		?>
		
		<div>
		<div style="color:black; font-size:24px"><center>Analyst: <?php echo $expert;?></center> </div>
<br/>		<div style="color:black; font-size:18px"><center>Rating : <?php echo $rating;?></center></div>
<br/>

		<div id="container1" style=" margin: 0 auto; font-size:18px; text-decoration:underline;">
			<center><a href="display_analyst_trend.php?expert=<?php echo $expert; ?>" target="_blank" >Show the success trends for this analyst</a></center>
		</div>
		<br/>
		<br />
		<div style="color:#E22727;font-size:16px;">
		<h2 id="idea">Buy Recommendations: </h2></a>
			<?php	
					if($final_b)
					{
						foreach($final_b as $r => $v){?>
					
						<a href="#" style=" font-size:16px;" onclick="window.open('piechart_bar.php?company=<?php echo $r ?>', 'newwindow', 'width=710,height=555,left=160,top=170'); return false;"></br><?php echo $r ;}?></a>
					<?php }
					else echo"No buy predictions today.";?>
		</div>
		<br />
		<br />
		<div style="color:#E22727; font-size:16px;">
		<h2 id="idea">Sell Recommendations:</h2></a>
			<?php
					
					if($final_s)
						{
						foreach($final_s as $r => $v){?>
						<a href="#" style=" font-size:16px;" onclick="window.open('piechart_bar.php?company=<?php echo $r ?>', 'newwindow', 'width=710,height=555,left=160,top=170'); return false;"></br><?php echo $r ;}?></a>
					<?php } 
					else echo"No sell predictions today.";?>
		</div>
		<br /><br />
		<div style=" font-size:12px;">
			<h2 id="idea">Past Predictions by <?php echo $expert; ?>:</h2>
			<?php
			$result1 = mysql_query("SELECT tweet_text,valid_till
			FROM tweets AS a, tweet_tags AS b
			WHERE a.tweet_id = b.tweet_id
			AND user_id = (select user_id from followers where screen_name='".$expert."') 
			
			AND SENTIMENT IS NOT NULL
			and valid_till > '2014-01-01'
			order by valid_till desc limit 100");
			if(mysql_num_rows($result1)==0)
			echo 'No predictions.<br/>';
			while($r = mysql_fetch_array($result1))
			{
				echo $r[0].'<br/>';
				//echo $r[1].'</br>';
				$dt = strtotime($r[1]);
				$next = strtotime("-1 day", $dt); 
				echo date("Y-m-d",$next).'<br/><br/>';
			}
			?>
		</div>
		</div>
			<!--<div id="container1" style="min-width: 400px; height: 400px; margin: 0 auto;"></div>-->
		
		</div>
		<div id="left">
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
<div id="hidden" style="display:hidden;"><?php echo $expert;?></div>
<div id="footer">

	<p>Copyright © 2005 All Rights Reserved </p>
</div>
</body>
</html>
