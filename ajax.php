<?php
session_start();
define("QUICK_CACHE_ALLOWED", false);
define("DONOTCACHEPAGE", true);
$_SERVER["QUICK_CACHE_ALLOWED"] = false;
include_once('./../../../wp-config.php');
$nid = $_GET['nid'];
$persistance_reset_nature = $_GET['prn'];
$persistance_reset_rule = $_GET['prr'];
$wordpress_date_time = date('Y-m-d');
setcookie('advnote_closed', '1',time()+3600,'/');
$_SESSION['advnote_closed']=1;
$visitor_ip = $_SERVER['REMOTE_ADDR'];	

$query = "SELECT * FROM {$table_prefix}advnote_ip WHERE ip = '$visitor_ip' AND notification_id='$nid'";
$result = mysql_query($query);
if (!$result){ echo $query; echo mysql_error(); exit;}
$count = mysql_num_rows($result);

if ($count<1)
{
	$q = "INSERT {$table_prefix}advnote_ip ( ip, notification_id, count, rules ) VALUES ('$visitor_ip','$nid','0','$wordpress_date_time')";
	$r = mysql_query($q);
	if (!$r){ echo $q; echo mysql_error(); exit;}
}
else
{

}

echo $nid;

?>