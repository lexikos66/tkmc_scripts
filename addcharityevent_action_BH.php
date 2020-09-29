<?php
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
//Connect To Database
$db = LocalDatabase::getWritable('finance');
$ctable='fin_charity_events';
$eventName = $_POST['eventName'];
$eventYear = $_POST['year'];
$amountRaised = $_POST['amount'];
$sponsor = $_POST['sponsor'];
$reason = $_POST['reason'];
$sponsoringLodge = $_POST['lodgeNum'];

$query = 'INSERT INTO ' . $ctable . ' (EVENT, YEAR, AMOUNT_RAISED, SPONSOR, REASON, LODGE_NUM) 
	VALUES ("' . $eventName . '","' . $eventYear . '","' . $amountRaised . '","' . $sponsor . '","' . $reason . '","' . $sponsoringLodge . '")';

$result = mysql_query($query);
if($result) {
   header("Location: http://templarknightsmc.com/council-area/grand-lodge/financial/add-charity-event?success");
}
else {
  echo 'Failed to Create Record';
}
mysql_close($db->connection);
?>
