<?php
set_time_limit ( 5000 );
$host="localhost"; // Host name 
$username="root"; // Mysql username 
$password=""; // Mysql password 
$db_name="market_analysis"; // Database name 

mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$r= mysql_query("Select tweet_id, tweet_text from tweets where is_filtered=0");
$s= mysql_query("Select company_id, company_name from company_names");
$t= mysql_query("Select company_id, tag from company_tags");

if($r && $s && $t) 
{   
	$stock_array=array();
	while ($stock = mysql_fetch_assoc($s)) 
	{
			array_push($stock_array, array($stock['company_name'], $stock['company_id']));
			//array_push($stock_array, $stock['company_name']);
			//echo $stock['company_name']."</br>";				
	}
	while ($stock = mysql_fetch_assoc($t)) 
	{
			array_push($stock_array, array($stock['tag'], $stock['company_id']));
			//array_push($stock_array, $stock['tag']);
			//echo $stock['tag']."</br>";				
	}
	//echo "</br>";
		
	while (($res = mysql_fetch_assoc($r))) 
	{		
		$text =$res['tweet_text'];
		$tweet_id = $res['tweet_id'];
		//Convert to lower case
		$tweet = strtolower($text);
		
		//Replace #word with word
		$tweet = str_replace('#' , '', $tweet); 
		
		//Strip punctuation
		$tweet = str_replace(array(',' , ':', ';', '.', '?', '!', '\n', '-', '(', ')', '[', ']', '\''), " ", $tweet);
		
		$tweet = ' '.$tweet.' ';
		echo $tweet_id.': '.$tweet.'</br>';

		$valid = 0;
		foreach($stock_array as $stock)
		{
			$stock_name = ' '.$stock[0].' ';
			$pos = strpos($tweet, $stock_name);
			if ($pos !== false) 
			{
				$valid = 1;
				echo "Found keyword: ".$stock[0]." with id: ".$stock[1]."</br>";	
				mysql_query("insert into tweet_tags(tweet_id, tag_id,keyword) values('$tweet_id', '$stock[1]', '$stock[0]')");
			
			}
		}
		
		if($valid == 0)
		{
			//echo $tweet_id.': '.$tweet.'</br>';
			//echo "not valid</br>";
			mysql_query("delete from tweets where tweet_id=".$tweet_id);
		}
		else
		{
			mysql_query("update tweets set is_filtered=1 where tweet_id=".$tweet_id);
			echo "valid</br>";
		}
		echo "</br>";
		
	}
}
else 
    echo 'cannot read tweets and companies';	
?>