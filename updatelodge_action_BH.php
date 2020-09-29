<?php
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}

require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getWritable('master');
$lodgetable='lodge_directory';
$lodgeNum = $_POST['lodgeNum'];
$lodgeName = $_POST['lodgeName'];
$lodgeCode = $_POST['lodgeCode'];
$city = $_POST['city'];
$state = $_POST['state'];
$country = $_POST['country'];
$emailAddr = $_POST['emailAddr'];
$webAddr = $_POST['webAddr'];
$fbAddr = $_POST['fbAddr'];
$twitterAddr = $_POST['twitterAddr'];
$youtubeAddr = $_POST['youtubeAddr'];
$duesMonth = $_POST['duesMonth'];
$dateCreation = $_POST['dateCreation'];
$dateDisbanded = $_POST['dateDisbanded'];

//Increment rank by 1 because of differences in array starting num
$duesMonth = $duesMonth + 1;
$query = "UPDATE " . $lodgetable . " SET LODGE_CODE='" . $lodgeCode . "',LODGE_NAME='" . $lodgeName . "',CITY='" . $city . "',STATE='" . $state .
"',COUNTRY='" . $country . "',EMAIL_ADDR='" . $emailAddr . "',WEB_ADDR='" . $webAddr . "',FB_ADDR='" . $fbAddr . "',TWITTER_ADDR='" . $twitterAddr . "',YOUTUBE_ADDR='" . $youtubeAddr . "',DUES_MONTH='" . $duesMonth . "',DATE_CREATION='" . $dateCreation . 
"',DATE_DISBANDED='" . $dateDisbanded . "' WHERE LODGE_NUM='" . $lodgeNum . "'";

$result = mysqli_query($db->connection, $query);

if($result) {
  header("Location: http://member.templarknightsmc.com/lodge/update-lodge?success");
}
else {
  echo 'Failed to update Record for ' . $lodgeName . ' ERROR: ' . mysqli_fetch_array($result) . '<br>Query: ' . $query;
}
mysqli_close($db->connection);
?>
