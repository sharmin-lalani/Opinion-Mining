<?php
$host="localhost"; // Host name 
$username="root"; // Mysql username 
$password=""; // Mysql password 
$db_name="market_analysis"; // Database name 
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");



$c_list = fopen("data/feature_list.txt","r");
$feature=array();
if ($c_list) 
{
    while (($c = fgets($c_list)) !== '\n') 
	{		
		$c = trim($c);
		$feature=explode(' ',$c);
		
		echo '<br/>';
		
		$query="INSERT INTO unigrams(feature,pos,neg) VALUES('".$feature[0]."',".$feature[1].",".$feature[2].")";
		$result= mysql_query($query);
    }
} 
else {
    echo 'cannot read list';
}

fclose($c_list);
?>