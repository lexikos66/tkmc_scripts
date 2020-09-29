<?php
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
	
//Connect To Database
$db = LocalDatabase::getWritable('master');
$knighttable='knight_directory';
$tkmcNum = $_POST['tkmcNum'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$birthDate = $_POST['birthDate'];
$nickName = $_POST['nickName'];
$rank = $_POST['rank'];
$streetAddress = $_POST['streetAddress'];
$city = $_POST['city'];
$state = $_POST['state'];
$zipCode = $_POST['zipCode'];
$country = $_POST['country'];
$phoneNum = $_POST['phoneNum'];
$emailAddr = $_POST['emailAddr'];
$lodgeNum = $_POST['lodgeNum'];
$referredBy = $_POST['referredBy'];
$mentor = $_POST['mentor'];
$dateApplied = $_POST['dateApplied'];
$dateApproved = $_POST['dateApproved'];
$dateKnighted = $_POST['dateKnighted'];
$dateWithdrew = $_POST['dateWithdrew'];
$notes = $_POST['notes'];
$recordNum = $_POST['recordNum'];
$grandCouncil = $_POST['grandCouncil'];

//Increment rank by 1 because of differences in array starting num
$rank = $rank + 1;
$query = "UPDATE " . $knighttable . " SET TKMC_NUM='" . $tkmcNum . "',FNAME='" . $firstName . "',LNAME='" . $lastName . "',BIRTH_DATE='" . $birthDate . "',NICKNAME='" . $nickName .
"',STREET_ADDRESS='" . $streetAddress . "',CITY='" . $city . "',STATE='" . $state . "',ZIP_CODE='" . $zipCode . "',COUNTRY='" . $country .
"',PHONE='" . $phoneNum . "',EMAIL='" . $emailAddr . "',LODGE='" . $lodgeNum . "',REFERRED_BY='" . $referredBy . "',MENTOR='" . $mentor . "',RANK=" . $rank . ",DATE_JOINED='" . $dateApplied .
"',DATE_APPROVED='" . $dateApproved . "',DATE_KNIGHTED='" . $dateKnighted . "',DATE_WITHDREW='" . $dateWithdrew . "',NOTES='" . $notes . "' WHERE RECORD_NUM='" . $recordNum . "'";

$result = mysqli_query($db->connection, $query);

if($result) {
	header("Location: http://member.templarknightsmc.com/knights/update-knight?success&knightNum=".$recordNum);
} else {
	echo 'Failed to update Record for ' . $firstName . ' ' . $lastName . ' ERROR: ' . mysqli_fetch_array($result) . '<br>Query: ' . $query;
}
mysqli_close($db->connection);
?>