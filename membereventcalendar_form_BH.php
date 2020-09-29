<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');

//Connect to Database
$db = LocalDatabase::getReadable('master');
$lodgetable='lodge_directory';
$lodge_num=0;
$lodge='<strong>all Lodges';
$category=0;

echo '<div>';
if(isset($_GET["lodgeNum"])) {
	$lodge_num = $_GET["lodgeNum"];
	$query = 'SELECT * from '.$lodgetable.' WHERE LODGE_NUM='.$lodge_num;
	$result = mysql_query($query, $db->connection);
	if($result) {
		$row = mysql_fetch_array($result);
		$lodge = 'the <strong>'.$row['LODGE_NAME'];
	}
	view_lodge($lodge);
	echo '<form action="http://templarknightsmc.com/member-area/events-calendar" method="get">';
	lodge_dropdown($db, $lodgetable);
	echo '<p><input style="position:relative;right:-250px;top:-25px;" type="submit" value="Select Lodge"></p>';
	echo '</form>';
} else {
	view_lodge($lodge);
	echo '<form action="http://templarknightsmc.com/member-area/events-calendar" method="get">';
	lodge_dropdown($db, $lodgetable);
	echo '<p><input style="position:relative;right:-250px;top:-25px;" type="submit" value="Select Lodge"></p>';
	echo '</form>';
}
mysql_close($db->connection);
echo '</div>';
switch ($lodge_num) {
	case 1://Grand Lodge
		$category = '2,3,4';
		break;
	case 4://Ohio171
		$category = '5,9';
		break;
	case 6://Jamtland
		$category = '15,16';
		break;
	case 7://Renfrew
		$category = '8,12';
		break;
	case 8://Indiana
		$category = '7,10';
		break;
	case 9://Ohio172
		$category = '6,11';
		break;
	case 13://Sweden West
		$category = '17,18';
		break;
	case 15://Stockholm
		$category = '13,14';
		break;
	default:
		$category = '2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18';
}

echo '[events_calendar long_events=1 full=1 category="'.$category.'"]';
echo '[events_list_grouped category="'.$category.'"]';

function view_lodge($lodge) {
	if(strpos($lodge, "Grand Lodge") || strpos($lodge, "Lodges")) {
		echo '<p style="text-align:center;font-size:20px">You are viewing member events for '.$lodge.'</strong></p>';
	} else {
		echo '<p style="text-align:center;font-size:20px">You are viewing member events for '.$lodge.' Lodge</strong></p>';
	}			
}
?>