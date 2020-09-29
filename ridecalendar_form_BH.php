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
	$result = mysqli_query($db->connection, $query);
	if($result) {
		$row = mysqli_fetch_array($result);
		$lodge = 'the <strong>'.$row['LODGE_NAME'];
	}
	view_lodge($lodge);
	echo '<form action="http://templarknightsmc.com/ride-calendar" method="get">';
	lodge_dropdown($db, $lodgetable);
	echo '<p><input style="position:relative;right:-250px;top:-25px;" type="submit" value="Select Lodge"></p>';
	echo '</form>';
} else {
	view_lodge($lodge);
	echo '<form action="http://templarknightsmc.com/ride-calendar" method="get">';
	lodge_dropdown($db, $lodgetable);
	echo '<p><input style="position:relative;right:-250px;top:-25px;" type="submit" value="Select Lodge"></p>';
	echo '</form>';
}
mysqli_close($db->connection);
echo '</div>';
switch ($lodge_num) {
	case 1://Grand Lodge
		$category = '2';
		break;
	case 4://Ohio171
		$category = '5';
		break;
	case 6://Jamtland
		$category = '15';
		break;
	case 7://Renfrew
		$category = '8';
		break;
	case 8://Indiana
		$category = '7';
		break;
	case 9://Ohio172
		$category = '6';
		break;
	case 13://Sweden West
		$category = '17';
		break;
	case 15://Stockholm
		$category = '13';
		break;
	default:
		$category = '2,5,6,7,8,13,15,17';
}

echo '[events_calendar long_events=1 full=1 category="'.$category.'"]';
echo '[events_list_grouped category="'.$category.'"]';

function view_lodge($lodge) {
	if(strpos($lodge, "Grand Lodge") || strpos($lodge, "Lodges")) {
		echo '<p style="text-align:center;font-size:20px">You are viewing events for '.$lodge.'</strong></p>';
	} else {
		echo '<p style="text-align:center;font-size:20px">You are viewing events for '.$lodge.' Lodge</strong></p>';
	}			
}
?>