<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/defines.inc');

//Connect To Database
$db = LocalDatabase::getWritable('master');

$corollarytable='ride_corollary';
$ridetable='ride_directory';
$rideName = $_POST['rideName'];
$rideLodge = $_POST['lodgeNum'];
$rideDate = $_POST['rideDate'];
$rideMiles = $_POST['rideMiles'];
$rideHours = $_POST['rideHours'];
$rideOrganizer = $_POST['knightNum'];
$rideParticipants = implode(",",$_POST['rideParticipants']);
$rideRoute = $_POST['routeDetails'];
$rideConditions = $_POST['conditions'];
$ridePerceptions = $_POST['perception'];
$rideSafety = $_POST['safetyIssues'];
$rideSummary = $_POST['rideSummary'];
$isCharity = $_POST['isCharity'];

if(($rideName == "") || ($rideDate == "") || ($rideMiles == "") || ($rideSafety == "") || ($rideSummary == "") || ($rideRoute == "")) {
  exit('Missing Data.  Please fill in all required fields.');
}

$query = 'INSERT INTO ' . $ridetable . ' (NAME, LODGE, DATE, MILES, HOURS, ORGANIZER, SAFETY_ISSUES, RIDE_SUMMARY, ROUTE, CONDITIONS, PERCEPTION, IS_CHARITY, RIDE_NUM)
  VALUES ("' . LocalDatabase::cleanentry($rideName) . '", "' . $rideLodge . '", "' . $rideDate . '", "' . $rideMiles . '", "' . $rideHours . '", "' . $rideOrganizer . '", "' . LocalDatabase::cleanentry($rideSafety) . '", "' . LocalDatabase::cleanentry($rideSummary) . '", "' . LocalDatabase::cleanentry($rideRoute) . '", "' . LocalDatabase::cleanentry($rideConditions) . '", "' . LocalDatabase::cleanentry($ridePerceptions) . '", "' . $isCharity . '", "")';

$rideresult = mysqli_query($db->connection, $query);

if($rideresult) {
  $query = 'SELECT * FROM ' . $ridetable . ' WHERE RIDE_NUM = (SELECT MAX(RIDE_NUM) FROM ' . $ridetable . ')';
  $result = mysqli_query($db->connection, $query);
  $row = mysqli_fetch_array($result);
  $rideNum = $row['RIDE_NUM'];

  if($result) {
    foreach(explode(",",$rideParticipants) as $value) {
      $query = 'INSERT INTO ' . $corollarytable . ' (RIDE_NUM, KNIGHT_NUM)
        VALUES ("' . $rideNum . '","' . $value . '")';
      $result = mysqli_query($db->connection, $query);
      if(!$result) {
          exit("Failed to update Corollary Table: " . mysqli_error());
      }
    }
    header("Location: http://member.templarknightsmc.com/knights/ride-tracker?success=1");
  }
}
else {
  echo 'Failed to insert Ride Data.';
}

mysqli_close($db->connection);

?>