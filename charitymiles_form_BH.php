<?php

define('__ROOT__', dirname(dirname(__FILE__)));
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/sql_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getReadable('master');
$rtable='ride_directory';
$ctable='ride_corollary';
$odomurl='http://templarknightsmc.com/static/Odometer';

$query = 'SELECT * FROM ' . $rtable . ' WHERE IS_CHARITY = 1';
$result = mysql_query($query, $db->connection);
if($result) {
	$totalmiles = 0;
    while($row = mysql_fetch_array($result)){
		$ridename = '<table><tr><td>' . $row['NAME'] . '</td></tr></table>';
		$cquery = 'SELECT COUNT(*) AS numriders FROM ' . $ctable . ' WHERE RIDE_NUM = ' . $row['RIDE_NUM'];
		$cresult = mysql_query($cquery, $db->connection);
		$count = mysql_result($cresult, 0);
		$ridemiles = $count * $row['MILES'];
		$totalmiles = $totalmiles + $ridemiles;
    }
    $milelen = strlen($totalmiles);

    $i = 0;
    echo '<table><tr><td>';
    while($i < $milelen) {
		echo '<image src="' . $odomurl . substr($totalmiles, $i, 1) . '.png" width="20" height="30" />';
		$i++;
    }
    echo '</font></td></tr></table>';
  }
  mysql_close($db->connection);
?>