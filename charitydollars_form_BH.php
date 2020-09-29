<?php

define('__ROOT__', dirname(dirname(__FILE__)));
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/sql_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getReadable('finance');
$ctable='fin_charity_events';
$odomurl='http://templarknightsmc.com/static/Odometer';

$query = 'SELECT * FROM ' . $ctable;
$result = mysql_query($query, $db->connection);
if($result) {
	$totaldollars = 0;
    while($row = mysql_fetch_array($result)){
		$totaldollars += $row['AMOUNT_RAISED'];
    }
    $dollarlen = strlen($totaldollars);
    $i = 0;
    echo '<table><tr><td><font color="black" size="6">$</font>';
    while($i < $dollarlen) {
		echo '<image src="' . $odomurl . substr($totaldollars, $i, 1) . '.png" width="20" height="30" />';
		$i++;
    }
    echo '</font></td></tr></table>';
  }
  mysql_close($db->connection);
?>