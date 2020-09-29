<?php

define('__ROOT__', dirname(dirname(__FILE__)));
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/sql_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getWritable('master');
$ktable='lodge_directory';

$query = 'SELECT LODGE_NUM, LODGE_NAME FROM lodge_directory WHERE DATE_DISBANDED="0000-00-00"
	ORDER BY LODGE_NAME';

$result = mysql_query($query);
if($result) {
	$file=fopen(__ROOT__."/data/tkmclodges.csv","w");
	while($row = mysql_fetch_array($result)){
		write_info($row, $file);
	}
	fclose($file);
}

mysql_close($db->connection);

function write_info($lodgedata, $fh) {
	$lodge = $lodgedata['LODGE_NUM'] . "," . $lodgedata['LODGE_NAME'] . "\n";
	fwrite($fh, $lodge);
}
?>
