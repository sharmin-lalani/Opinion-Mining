<?php
$con = mysql_connect("localhost","root","");

if (!$con) {
  die('Could not connect: ' . mysql_error());
}
/*select t.tag_id,t.c2,s.c1 from((SELECT tag_id,count(sentiment) as c2,company_name  FROM `tweet_tags`,company_names where sentiment='b' and company_id=tag_id group by tag_id ) as t inner join
(SELECT tag_id,count(sentiment) as c1,company_name  FROM `tweet_tags`,company_names where sentiment='s' and company_id=tag_id group by tag_id ) as s on t.tag_id=s.tag_id)*/
mysql_select_db("market_analysis", $con);

	$date_now = strtotime( "today");
	$date = strtotime("-1 day", $date_now);
	$next_date = strtotime("+1 day", $date_now);
	$date = date("Y-m-d",$date); 
	$next_date = date("Y-m-d",$next_date);

$result = mysql_query("select t.tag_id,t.c2,s.c1,t.company_name 
from((SELECT tag_id,count(sentiment) as c2,company_name  
FROM tweet_tags,company_names, tweets
where sentiment='b' 
and company_name not in ('sensex','nifty')
and tweets.valid_till between '".$date."' and '".$next_date."'
and company_id=tag_id 
and tweets.tweet_id=tweet_tags.tweet_id
 group by tag_id ) as t 
inner join
(SELECT tag_id,count(sentiment) as c1,company_name  
FROM tweet_tags,company_names,tweets
where sentiment='s'  
and company_name not in ('sensex','nifty')
and tweets.valid_till between '".$date."' and '".$next_date."'
and company_id=tag_id 
and tweets.tweet_id=tweet_tags.tweet_id
group by tag_id ) as s 
on t.tag_id=s.tag_id)");
$rows = array();
//$row = array('cn'=>45);


while($r = mysql_fetch_array($result))
{
	$row = array();
	$sell=$r['c1'];
	$buy=$r['c2'];
	//$row[0]=$r['company_name'];
	$cn=$r['company_name'];
	//echo $cn;
	$cent=$sell*100/($sell+$buy);
	settype($cent, "int");
	$result2 = mysql_query("insert into percent values('".$cn."',".$cent.")");
	//array_push($rows,$row);
	
}

$result3 = mysql_query("select * from percent order by percent desc");
$numResults = mysql_num_rows($result3);
$row1 = array();
$row1[0]='others';
$row1[1]=0;
$c=10;
$counter=0;
while($r = mysql_fetch_array($result3)) {
	
	if($c==0)
	{	
		
		$counter++;
		$row1[1]=$row1[1]+$r['percent'];
		if($counter==$numResults)
		{
			//array_push($rows,$row1);
		}
	}
	else
	{
		$row = array();
		$row[0] = $r['company_name'];
		$row[1] = $r['percent'];
		array_push($rows,$row);
		$counter++;
		$c--;
	}
}
$result4 = mysql_query("delete from percent");


/*echo $row['leadmini'];
	rsort($row);
	print_r ($row);
/*$numResults = mysql_num_rows($result);
$counter = 0;
$rows = array();
$c=4;
$row1=array();
$row1[0]='others';
$row1[1]=0;
while($r = mysql_fetch_array($result) ||$r1 = mysql_fetch_array($result1)  ) {
	
	if($c==0)
	{	
		$counter++;
		
		$row1[1]=$row1[1]+$r['c'];
		if($counter==$numResults)
		{
			array_push($rows,$row1);
		}
	}
	else
	{
		$row[0] = $r['company_name'];
		$sell = $r['c1'];
		$buy = $r['c2'];
		$row[1]=$sell/($sell+$buy)*100;
		
		array_push($rows,$row);
		$counter++;
		$c--;
	}
}
*/
print json_encode($rows, JSON_NUMERIC_CHECK);

mysql_close($con);
?> 
