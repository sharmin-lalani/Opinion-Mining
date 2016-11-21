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
	window.onload=function()
		{
			table=document.getElementById("table");
		}
	
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
		searchReq.open("GET","get_comp_tweets.php?comp_name="+name+"&set=sell",true);
		
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
	var str="";
	for(var i=0;i<len; i++)
	{
		str = str+'<a href="analyst_profile.php?expert='+rows[i][0]+'">'+rows[i][0]+'</a><br/>';
		str = str+rows[i][1]+'<br/>';

		str=str+"<br/>";
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
			var options_a = {
				chart: {
	                renderTo: 'container1',
	                plotBackgroundColor: null,
	                plotBorderWidth: null,
	                plotShadow: false
	            },
	            title: {
	                text: 'Todays best sells'
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
										//window.location.href = 'get_comp_tweets.php?comp_name='+this.name+'&set=buy';
										window.open('piechart_bar.php?company='+this.name, "", 'width=710,height=555,left=160,top=170')
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
	        
	        $.getJSON("sell.php", function(json) {
				options_a.series[0].data = json;
				//alert(options_a.series[0].data);
	        	chart = new Highcharts.Chart(options_a);
	        });
	        
	        
	      });   
	</script>
		<!--<script type="text/javascript">
		//alert("sell");
		$(document).ready(function() {
			chart = new Highcharts.Chart({ 
				chart: {
	                renderTo: 'container2',
	                plotBackgroundColor: null,
	                plotBorderWidth: null,
	                plotShadow: false
	            },
	            title: {
	                text: 'Todays best sells'
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
	                name: 'BUY',
					center:[20%],
	                data: []
	            },
				{
	                type: 'pie',
	                name: 'SELL',
					center:[80%]
	                data: []
	            }]
	        }
	        
	        $.getJSON("sell.php", function(json) {
				chart.series[0].data = json;
				alert(chart.series[0].data);
	        	//chart = new Highcharts.Chart(options_b);
	        })
	        $.getJSON("buy.php", function(json) {
				chart.series[1].data = json;
				alert(chart.series[1].data);
	        	//chart = new Highcharts.Chart(options_b);
	        });
			
			
	        
	      });   
	</script>-->
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
		<!--<ul>
			<li><a href="index.php"><h2 id="idea">Todays best buy</h2></a></li>
			<li><a href="index.php"><h2 id="idea">Todays best sell</h2></a></li>
		</ul>
		-->
			<div id="container1" style="min-width: 400px; height: 400px; margin: 0 auto;"></div>
		<div id="table"></div>
		
		</div>
		
		<div id="left">
			<h2>Select Company</h2>
			<?php
			$host="localhost"; // Host name 
			$username="root"; // Mysql username 
			$password=""; // Mysql password 
			$db_name="market_analysis"; // Database name 
			mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
			mysql_select_db("$db_name")or die("cannot select DB");
			
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
			<a style="color: #11A0CF;font-size:18px;" href="index.php"> View todays best buy</a>					
		</div>		
	</div>
</div>
<div id="footer">

	<p>Copyright © 2005 All Rights Reserved </p>
</div>
</body>
</html>
