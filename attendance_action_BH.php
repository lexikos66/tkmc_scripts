<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getWritable('master');
$corollarytable='attendance_corollary';
$eventId = $_POST['eventId'];
$lodgeNum = $_POST['lodgeNum'];
$mtgParticipants = implode(",",$_POST['mtgParticipants']);

foreach(explode(",",$mtgParticipants) as $value) {
  $query = 'INSERT INTO ' . $corollarytable . ' (event_id, knight_num, lodge_num) VALUES ("' . $eventId . '", "' . $value . '", "' . $lodgeNum . '")';
  $result = mysql_query($query, $db->connection);
  if(!$result) {
    exit("Failed to update Corollary Table: " . mysql_error());
  }
  header("Location: http://templarknightsmc.com/council-area/grand-lodge/attendance-entry");
}
mysql_close($db->connection);

$wpdb = LocalDatabase::getWritable('wor7320');
$query = 'UPDATE wp_hiqx_em_events SET event_spaces = 1 WHERE event_id = ' . $eventId;
$result = mysql_query($query, $wpdb->connection);
if(!$result) {
	exit("Failed to update event_spaces in wp_em_events table" . mysql_error());
}
mysql_close($wpdb->connection);

?>