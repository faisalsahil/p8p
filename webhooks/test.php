<?php
include 'conn.php';
echo mysql_ping();
	$query = "SELECT coupon_code FROM coupon WHERE used = '0' AND subscriber_email = '' LIMIT 1";
	$sql = mysql_query($query) or die(mysql_error());
	//echo $query;
   	$coupon = mysql_fetch_row($sql);
   	var_dump($coupon);
?>