<?php
$host="localhost"; // Host name 
$username="root"; // Mysql username 
$password=""; // Mysql password 
$db_name="market_analysis"; // Database name 
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$c_list = fopen("data/companies.txt","r");



if ($c_list) 
{
    while (($c = fgets($c_list)) !== false) 
	{		
		$c = trim($c);
		
		$query="INSERT INTO company_names(company_name) VALUES('$c')";
		$result= mysql_query($query);
    }
} 
else {
    echo 'cannot read list';
}


fclose($c_list);
?>
