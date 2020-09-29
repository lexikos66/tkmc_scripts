<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/defines.inc');

//Connect To Database
$db = LocalDatabase::getWritable('master'); 
$table_name='provisional_progress';
$query="";

if ($_POST['doUpdate'] == 1) {
	$query = "UPDATE " . $table_name . " SET  app_fee_date='" . $_POST['app_fee_date'] . "', patches_paid_date='" . $_POST['patches_paid_date'] . "', notes='" . $_POST['notes'] . "', nickname='". $_POST['nickname'] . "' WHERE knight_num=" . $_POST['knight_num'];
	$result = mysql_query($query, $db->connection);
	if(!$result) {
		exit("Failed to update record for " . $_POST['knight_name'] . "-" . mysql_error());
	}
} else {
	$query = "INSERT into " . $table_name . " (knight_num, app_fee_date, patches_paid_date, notes, nickname) VALUES (" . $_POST['knight_num'] . ", '" . $_POST['app_fee_date'] . "', '" . $_POST['patches_paid_date'] . "', '" . $_POST['notes'] . "', '" . $_POST['nickname'] . "')";
	$result = mysql_query($query, $db->connection);
	if (!$result) {
		exit("Failed to insert record for " . $_POST['knight_name'] . "-" . mysql_error());
	}
}

header("Location: http://templarknightsmc.com/council-area/grand-council/apppatch-fees?knightNum=".$_POST['knight_num']."&lodgeNum=".$_POST['lodgeNum']);
mysql_close($db->connection);

?>