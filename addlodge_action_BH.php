<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
//Connect To Database
$db = LocalDatabase::getWritable('master');
$lodgetable='lodge_directory';
$lodgeCode = $_POST['lodgeCode'];
$lodgeName = $_POST['lodgeName'];
$city = $_POST['city'];
$state = $_POST['state'];
$country = $_POST['country'];
$emailAddr = $_POST['emailAddr'];
$webAddr = $_POST['webAddr'];
$fbAddr = $_POST['fbAddr'];
$twitterAddr = $_POST['twitterAddr'];
$youtubeAddr = $_POST['youtubeAddr'];
$duesMonth = $_POST['duesMonth'];

//Make sure we have something real
if($lodgeName != '') {
	//Increment month by 1 because of differences in array starting num
	$duesMonth = $duesMonth + 1;
	
	$query = 'INSERT INTO ' . $lodgetable . ' (LODGE_CODE, LODGE_NAME, CITY, STATE, COUNTRY, EMAIL_ADDR, WEB_ADDR, FB_ADDR, TWITTER_ADDR, YOUTUBE_ADDR, DUES_MONTH) VALUES ("' . $lodgeCode . '", "' . $lodgeName . '", "' . $city . '", "' . $state . '", "' . $country . '", "' . $emailAddr . '", "' . $webAddr . '", "' . $fbAddr . '", "' . $twitterAddr . '", "'. $youtubeAddr . '", "' . $duesMonth . '")';

	$result = mysqli_query($db->connection, $query);
	if($result) {
		header("Location: http://member.templarknightsmc.com/lodge/add-lodge?success");
	}
	else {
		echo 'Failed to Create Record';
	}
}
mysqli_close($db->connection);
?>