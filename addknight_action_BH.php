<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getWritable('master');
$knighttable='knight_directory';
$lodgetable = 'lodge_directory';
$tkmcNum = $_POST['tkmcNum'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$birthDate = $_POST['birthDate'];
$nickName = $_POST['nickName'];
$rank = $_POST['rank'];
$street = $_POST['streetAddress'];
$city = $_POST['city'];
$state = $_POST['state'];
$zipCode = $_POST['zipCode'];
$country = $_POST['country'];
$phoneNum = $_POST['phoneNum'];
$emailAddr = $_POST['emailAddr'];
$lodgeNum = $_POST['lodgeNum'];
$lodgeName = '';
$referredBy = $_POST['referredBy'];
$mentor = $_POST['mentor'];
$dateKnighted = $_POST['dateKnighted'];
$dateJoined = $_POST['dateJoined'];
$dateApproved = $_POST['dateApproved'];
$dateWithdrew = $_POST['dateWithdrew'];
$notes = $_POST['notes'];
$lodgeEmail = '';

//Make sure we have something real
if($firstName != '') {
	//Increment rank by 1 because of differences in array starting num
	$rank = $rank + 1;
	$query = 'INSERT INTO ' . $knighttable . ' (TKMC_NUM, FNAME, LNAME, BIRTH_DATE, NICKNAME, STREET_ADDRESS, CITY, STATE, ZIP_CODE, COUNTRY, PHONE, EMAIL, LODGE, REFERRED_BY, MENTOR, RANK, DATE_KNIGHTED, DATE_APPROVED, DATE_JOINED, DATE_WITHDREW, NOTES, RECORD_NUM) VALUES ("' . $tkmcNum . '", "' . $firstName . '", "' . $lastName . '", "' . $birthDate . '", "' . $nickName . '", "' . $street . '", "' . $city . '", "' . $state . '", "' . $zipCode . '", "' . $country . '", "' . $phoneNum . '", "' . $emailAddr . '", "' . $lodgeNum . '", "' . $referredBy . '", "' . $mentor . '", "' . $rank . '", "' . $dateKnighted . '", "' . $dateApproved . '", "' . $dateJoined . '", "' . $dateWithdrew . '", "' . $notes . '", "")';

	$result = mysqli_query($db->connection, $query);
	if($result) {
		$query = 'SELECT * FROM ' . $lodgetable . ' WHERE LODGE_NUM = ' . $lodgeNum;
		$result = mysqli_query($db->connection, $query);
		if($result) {
			$row = mysqli_fetch_array($result);
			$lodgeName = $row['LODGE_NAME'];
			$lodgeEmail = $row['EMAIL_ADDR'];
		}
		$query = 'SELECT * FROM state_directory WHERE STATE_NUM = '. $state;
		$result = mysqli_query($db->connection, $query);
		if($result) {
			$row = mysqli_fetch_array($result);
			$stateName = $row['STATE_NAME'];
		}
		$query = 'SELECT * FROM country_directory WHERE COUNTRY_NUM = ' . $country;
		$result = mysqli_query($db->connection, $query);
		if($result) {
			$row = mysqli_fetch_array($result);
			$countryName = $row['COUNTRY_NAME'];
		}
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
		$headers .= 'From: TKMC Administrator <contact@templarknightsmc.com>' . "\r\n";
		$headers .= 'Reply-To: contact@templarknightsmc.com' . "\r\n";

		$message = '
		<html>
		<head>
        <title>New Provisional Knight Added to Database</title>
		</head>
		<body>
        <p>A new Provisional Knight was added to the database:</p><br>
        <table>
          <tr><td colspan="2" valign="center"><b>LODGE: ' . $lodgeName . '</b></td></tr>
          <tr><td>First Name: </td><td>' . $firstName . '</td></tr>
          <tr><td>Last Name: </td><td>' . $lastName . '</td></tr>
        </table><br>
        <table>
          <tr><td>Street: </td><td>' . $street . '</td></tr>
          <tr><td>City: </td><td>' . $city . '</td></tr>
          <tr><td>State: </td><td>' . $stateName . '</td></tr>
          <tr><td>Zip Code: </td><td>' . $zipCode . '</td></tr>
          <tr><td>Country: </td><td>' . $countryName . '</td></tr>
          <tr><td>Phone Number: </td><td>' . $phoneNum . '</td></tr>
          <tr><td>eMail Address: </td><td>' . $emailAddr . '</td></tr>
        </table><br>
		</body>
		</html>
		';
		$subject = $firstName . ' ' . $lastName . ' - Added to Database';
		mail('grand.council@templarknightsmc.com, ' . $lodgeEmail, $subject , $message, $headers);
		header("Location: http://member.templarknightsmc.com/knights/add-knight?success");
   
	}
	else {
	echo 'Failed to Create Record';
	}
}
mysqli_close($db->connection);
?>