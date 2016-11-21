<?php

$company = $_GET['comp_name'];
$set = $_GET['set'];

$con = mysql_connect("localhost","root","");

if (!$con) {
  die('Could not connect: ' . mysql_error());
}

mysql_select_db("market_analysis", $con);
$date_now = strtotime( "today");
	$date = strtotime("-1 day", $date_now);
	$next_date = strtotime("+1 day", $date_now);
	$date = date("Y-m-d",$date); 
	$next_date = date("Y-m-d",$next_date);

if ($set=='buy')
{
	$sql = "SELECT followers.screen_name, tweet_text, created_at
	FROM tweets, tweet_tags, followers
	WHERE tag_id = ( 
	SELECT company_id
	FROM company_names
	WHERE company_name =  '".$company."' ) 
	AND tweets.tweet_id = tweet_tags.tweet_id
	AND sentiment =  'b'
	and tweets.valid_till between '".$date."' and '".$next_date."'
	AND tweets.user_id = followers.user_id
	ORDER BY created_at desc";
	$result = mysql_query($sql);
	//echo $sql;
}
else
{
	$sql = "SELECT followers.screen_name, tweet_text, created_at
	FROM tweets, tweet_tags, followers
	WHERE tag_id = ( 
	SELECT company_id
	FROM company_names
	WHERE company_name =  '".$company."' ) 
	AND tweets.tweet_id = tweet_tags.tweet_id
	AND sentiment =  's'
	and tweets.valid_till between '".$date."' and '".$next_date."'
	AND tweets.user_id = followers.user_id
	ORDER BY created_at desc";
	$result = mysql_query($sql);
		//echo $sql;
}

$rows=array();


while($r = mysql_fetch_array($result))
{
	$row=array();
	$row[0] = $r[0];
	$row[1] = $r[1];
	$row[2]= $r[2];

	array_push($rows, $row);
	//echo $name.'<br/>';
	//echo $text.'<br/>';
	//echo $time.'\n\r';
}
print json_encode($rows, JSON_NUMERIC_CHECK);
//print_r($rows);




?>