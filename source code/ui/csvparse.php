<?php echo $_GET["callback"]; 
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


$tag=$_REQUEST['id'];


$file = fopen("percent.csv","w");
// Fetch the data
$date = strtotime( "today");
$nxt_yr_date=strtotime("1 January 2013");
while( $nxt_yr_date <= $date )
{
	$date=date("Y-m-d",$date);
	//echo $date.' ';

	$query = "
	SELECT count(sentiment) as c2 
	FROM `tweet_tags`,company_names, tweets
	where sentiment='b'
	and tag_id=".$tag."
	and company_id=tag_id 
	AND tweets.tweet_id = tweet_tags.tweet_id
	and valid_till='".$date."'
	";
	$result = mysql_query( $query );
	$r = mysql_fetch_assoc( $result );
	$buy=$r['c2'];
	//echo $buy.'<br>';
	$query = "
	SELECT count(sentiment) as c1 
	FROM `tweet_tags`,company_names, tweets
	where sentiment='s'
	and tag_id=".$tag."
	and company_id=tag_id 
	AND tweets.tweet_id = tweet_tags.tweet_id
	and valid_till='".$date."'
	";
	$result = mysql_query( $query );
	$r= mysql_fetch_assoc( $result );
	$sell=$r['c1'];
	
	if($buy!=0 || $sell!=0)
	{
		$line = array();
		$cent=$buy*100/($sell+$buy);
		array_push($line,$date);
		array_push($line,$cent);
		fputcsv($file,$line);
		
	}
	$date = strtotime($date);
	$date = strtotime("-1 day", $date);
}
fclose($file);
echo "(";
if (($handle = fopen("percent.csv", "r")) !== FALSE) {
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
    $days_array_asc = array_reverse($days_array);
    print json_encode($days_array_asc, JSON_NUMERIC_CHECK);
    fclose($handle);
}
echo ")";
// Close the connection
mysql_close($link);
?>