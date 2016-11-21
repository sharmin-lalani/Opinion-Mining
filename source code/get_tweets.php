<?php
set_time_limit ( 2000 );
require('twitteroauth.php'); // path to twitteroauth library

$consumerkey = 'j6xColoE0EmckxeHe5YiKw';
$consumersecret = 'KWHVXBHKO19gmHhuSv3PBAOMKiCfzBE9zArEOYerPM';
$accesstoken = '2316824239-9ZsYNrtdlInaXuKyxHlZhK5rq8Bk2541xC0uwmN';
$accesstokensecret = 'uxHiF9wHqiPpcwTRgcHoOzwZBNKOSRGgqgInrtxbyIfG9';

session_start();
$host="localhost"; // Host name 
$username="root"; // Mysql username 
$password=""; // Mysql password 
$db_name="market_analysis"; // Database name 
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$twitter = new TwitterOAuth($consumerkey, $consumersecret, $accesstoken, $accesstokensecret);
//$user_list = fopen("data/followers_list.txt","r");


$r= mysql_query("Select screen_name from followers");
if(mysql_num_rows($r)) 
{
    while (($res = mysql_fetch_assoc($r))) 
	{
		
		$user =$res['screen_name'];
		echo "user:".$user."</br>";
		
		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name='.$user.'&count=200';
		$feed = $twitter->get($url);
		foreach($feed as $item) 
		{
			$id = $item->id_str;
			$user = $item->user->id_str;
			//echo 'tweet id = '.$id.' user id = '.$user.'</br>';
			
			$created_at = $item->created_at;
			$created = date('Y-m-d H:i:s',strtotime($created_at));
			$valid = date('Y-m-d',strtotime($created_at."+1 day"));
			//echo 'created at = '.$created.' valid till = '.$valid.'</br>';
			
			$text = mysql_real_escape_string($item->text);
			//echo 'text = '.$text.'</br>';
			
			$retweet_count = $item->retweet_count;
			$fav_count=$item->favorite_count;
			//echo 'retweet count = '.$retweet_count.' fav count = '.$fav_count.'</br>';
			
			$query="INSERT INTO tweets(tweet_id,tweet_text,created_at,user_id,retweet_count,valid_till,fav_count) VALUES('$id', '$text', '$created', '$user', '$retweet_count', '$valid', '$fav_count')";
			$result= mysql_query($query);
			
			/*
			if($result)
				echo "success".'</br>';
			else
				echo "no success".'</br>';
			*/
			
		}
    }
} 
else 
    echo 'cannot read users';
include("filter_tweets.php");

$command = escapeshellcmd('sentiment_analysis.py');
$output = shell_exec($command);
echo $output;

//fclose($user_list);
?>
