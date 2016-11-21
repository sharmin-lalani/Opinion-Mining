<?php
echo $_GET["callback"]; 

// Connect to MySQL
$link = mysql_connect( 'localhost', 'root', '' );
if ( !$link ) {
  die( 'Could not connect: ' . mysql_error() );
}
// Select the data base
$db = mysql_select_db( 'market_analysis', $link );
if ( !$db ) {
  die ( 'Error selecting database \'test\' : ' . mysql_error() );
}

			$screen_name=$_REQUEST['expert'];
			//echo $screen_name;
			$res = mysql_query("select user_id from followers where screen_name='".$screen_name."'");			
			//echo mysql_num_rows($res);
			$row = mysql_fetch_array($res);
			$analyst = $row['user_id'];
			
			$dt = date("Y-m-d");
			
			$time = strtotime($dt);
$final = date("Y-m-d", strtotime("-1 year", $time));

$file = fopen("analyst_rating.csv","w");

while($final <= $dt)
{
//	echo $final;
	
	$final2=date("Y-m-d", strtotime("+1 month", strtotime($final)));
	$result1 = mysql_query("SELECT tweet_text
	FROM tweets AS a, tweet_tags AS b
	WHERE a.tweet_id = b.tweet_id
	AND sentiment IS NOT NULL 
	AND valid_till
	BETWEEN  '".$final."'
	AND  '".$final2."'
	AND user_id ='".$analyst."'
	and (tweet_text like '%target%' or tweet_text like'%tgt%')");

	//echo $sql;
	$result2 = mysql_query("SELECT count(tweet_text)
	FROM tweets AS a, tweet_tags AS b
	WHERE a.tweet_id = b.tweet_id
	AND valid_till
	BETWEEN  '".$final."'
	AND  '".$final2."'
	AND user_id ='".$analyst."'
	and (tweet_text like '%achieve%' or tweet_text like '%achiv%' or tweet_text like '%achv%')");
	//echo $final;
	$tgt = mysql_num_rows($result1);
	//echo $tgt."<br/>";;
	
	$row2 = mysql_fetch_array($result2);
	$ach = $row2[0];
 
	//echo $ach."<br/>";
	if($ach!=0 && $tgt!=0)
	{
		$percent = $ach/$tgt *150;
		if($percent> 100)
			$percent = 89;
		
		
		$line = array();
		array_push($line,$final);
		array_push($line,$percent);
		fputcsv($file,$line);
	
	}
$final = date("Y-m-d", strtotime("+1 month", strtotime($final)));

}		

fclose($file);
echo "(";
if (($handle = fopen("analyst_rating.csv", "r")) !== FALSE) {
	$row = 0;
	$days_array = array();
    while (($data = fgetcsv($handle)) !== FALSE) {
	    if ($row > 0) {
	    	$time_epoch = strtotime($data[0]) * 1000;
	    	$day_array = array($time_epoch, $data[1]);
	    	array_push($days_array, $day_array);
   		}
        $row++;
    }
    
    print json_encode($days_array, JSON_NUMERIC_CHECK);
    fclose($handle);
}
echo ")";
mysql_close($link);
?>