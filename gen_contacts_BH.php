<?php

define('__ROOT__', dirname(dirname(__FILE__)));
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR','/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/sql_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getWritable('master');
$ktable='knight_directory';
$apptable='app_info';

//Get table update time to see if we need to generate a new dataset
$update_time = sql_get_table_update_time($db, $ktable);
if(strtotime($update_time) < strtotime('-7 days')) {
	exit(0);
}

//Since the table has been updated within the past 7 days - grab new dataset and change version number
$data_version = sql_get_app_version($db, 'TKMC Contacts', 'Data');
sql_set_app_version($db, 'TKMC Contacts', 'Data', $data_version + 1);

$query = 'SELECT 
	knight_directory.FNAME, knight_directory.LNAME, knight_directory.NICKNAME,
	knight_directory.RANK, knight_directory.PHONE, knight_directory.EMAIL,
	lodge_directory.LODGE_NAME, knight_directory.RECORD_NUM FROM knight_directory JOIN lodge_directory ON 
	knight_directory.LODGE = lodge_directory.LODGE_NUM WHERE knight_directory.DATE_WITHDREW="0000-00-00" 
	ORDER BY knight_directory.LNAME';

$result = mysql_query($query, $db->connection);
if($result) {
	$file=fopen(__ROOT__."/data/tkmccontacts.csv","w");
	while($row = mysql_fetch_array($result)){
		write_info($row, $file);
	}
	fclose($file);
}

// Write Data Version
$file=fopen(__ROOT__."/data/tkmccdv.csv","w");
fwrite($file, $data_version + 1);
fclose($file);
mysql_close($db->connection);

function write_info($knightdata, $fh) {
	$contact = $knightdata['FNAME'] . "," . $knightdata['LNAME'] . "," . $knightdata['NICKNAME'] . "," . 
		$knightdata['RANK'] . "," . format_phone($knightdata['PHONE']) . "," . $knightdata['EMAIL'] . "," .
		$knightdata['LODGE_NAME'] . "," . $knightdata['RECORD_NUM'] . "\n";
	fwrite($fh, $contact);
}

function format_phone($phone) {
	if($phone == "") {
		return $phone;
	}
	$newphone = substr($phone,0,3) . '-' . substr($phone,3,3) . '-' . substr($phone,-4);
	return $newphone;
}

?>
