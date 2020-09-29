<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getWritable('master');
$atable='applicant_directory';
$ktable='knight_directory';
$ltable='lodge_directory';
$button_action = 0;
$cruiser_check = 0;
$affiliations_check = 0;
$fb_check = 0;
$spokeo_check = 0;
$military_check = 0;
$felony_check = 0;
$appfee_check = 0;
$driving_check = 0;
$insurance_check = 0;
$processing_check = 0;
$processing_by = '';

if (isset($_POST['processing_check'])) {
	$processing_check = 1;
	$processing_by = $_POST['processing_by'];
}
if (isset($_POST['cruiser_check'])) {
	$cruiser_check = 1;
}
if (isset($_POST['affiliations_check'])) {
	$affiliations_check = 1;
}
if (isset($_POST['fb_check'])) {
	$fb_check = 1;
}
if (isset($_POST['spokeo_check'])) {
	$spokeo_check = 1;
}
if (isset($_POST['military_check'])) {
	$military_check = 1;
}
if (isset($_POST['felony_check'])) {
	$felony_check = 1;
}

if (isset($_POST['appfee_check'])) {
	$appfee_check = 1;
}

if (isset($_POST['driving_check'])) {
	$driving_check = 1;
}

if (isset($_POST['insurance_check'])) {
	$insurance_check = 1;
}

// Find which button was pushed and act accordingly
// REJECT APPLICATION
if ($_POST['button_type'] == 'Reject Application') {
	$query = 'UPDATE '.$atable.' SET REJECTED = 1, NOTES = "'.$_POST['notes'].'", PROCESSING_CHECK = '.$processing_check.', PROCESSING_BY = "'.$processing_by.'" WHERE RECORD_NUM = '.$_POST['record_num'];
	$result = mysqli_query($db->connection, $query);
	if ($result) {
		$button_action = 1;
		$query = 'SELECT FNAME, LNAME, NOTES FROM '.$atable.' WHERE RECORD_NUM = '.$_POST['record_num'];
		$result = mysqli_query($db->connection, $query);
		if ($result) {
			$todaysDate = getDate();
			$row = mysqli_fetch_array($result);
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
			$headers .= 'From: TKMC Administrator <contact@templarknightsmc.com>' . "\r\n";
			$headers .= 'Reply-To: contact@templarknightsmc.com' . "\r\n";

			$message = '
			<html>
			<head>
			<title>Application Rejected</title>
			</head>
			<body>
			<p>The application for '.$row['FNAME'].' '.$row['LNAME'].' was rejected on '.$todaysDate['mday'].' '.$todaysDate['month'].' '.$todaysDate['year'].'</p>
			<p>Notes:<br>'.$row['NOTES']. '</p><br>
			</body>
			</html>
			';
			$subject = $row['FNAME'] . ' ' . $row['LNAME'] . ' - Application Rejected';
			mail('knights@templarknightsmc.com', $subject , $message, $headers);
		}

	}
}

// UPDATE APPLICATION
if ($_POST['button_type'] == 'Update Application') {
	$query = 'UPDATE '.$atable.' SET CRUISER_CHECK = '.$cruiser_check.', AFFILIATIONS_CHECK = '.$affiliations_check.', FB_CHECK = '.$fb_check.', SPOKEO_CHECK = '.$spokeo_check.', MILITARY_CHECK = '.$military_check.', FELONY_CHECK = '.$felony_check.', APPFEE_CHECK = '.$appfee_check.', DRIVING_CHECK = '.$driving_check.', INSURANCE_CHECK = '.$insurance_check.', LODGE = '.$_POST['lodgeNum'].', MENTOR = '.$_POST['mentor'].', NOTES = "'.$_POST['notes'].'", PROCESSING_CHECK = '.$processing_check.', PROCESSING_BY = "'.$processing_by.'" WHERE RECORD_NUM = '.$_POST['record_num']; 
	$result = mysqli_query($db->connection, $query);
	if ($result) {
		$button_action = 2;
	}
}

// APPROVE APPLICATION
if ($_POST['button_type'] == 'Approve Application') {
	date_default_timezone_set("America/Denver");
	
	// Grab record from database
	$query = 'SELECT * FROM '.$atable.' WHERE RECORD_NUM = '.$_POST['record_num'];
	$result = mysqli_query($db->connection, $query);
	if ($result) {
		$arow = mysqli_fetch_array($result);
		$notes = $arow['NOTES'].' '.$_POST['notes'];

		// Insert record into knight_directory
		$query = 'INSERT INTO ' . $ktable . ' (FNAME, LNAME, STREET_ADDRESS, CITY, STATE, ZIP_CODE, COUNTRY, PHONE, EMAIL, LODGE, REFERRED_BY, MENTOR, DATE_APPROVED, DATE_JOINED, NOTES) VALUES ("' . $arow['FNAME'] . '", "' . $arow['LNAME'] . '", "' . $arow['STREET_ADDRESS'] . '", "' . $arow['CITY'] . '", "' . $arow['STATE'] . '", "' . $arow['ZIP_CODE'] . '", "' . $arow['COUNTRY'] . '", "' . $arow['PHONE'] . '", "' . $arow['EMAIL'] . '", "' . $arow['LODGE'] . '", "' . mysqli_real_escape_string($arow['REFERRED_BY']) . '", "' . mysqli_real_escape_string($arow['MENTOR']) . '", "' . date("Y-m-d") . '", "' . $arow['DATE_APPLIED'] . '", "' . $notes . '")';
		$result = mysqli_query($db->connection, $query);
		if ($result) {
			$button_action = 3;
			$query = 'SELECT * FROM ' . $ltable . ' WHERE LODGE_NUM = ' . $arow['LODGE'];
			$result = mysqli_query($db->connection, $query);
			if($result) {
				$row = mysqli_fetch_array($result);
				$lodgeName = $row['LODGE_NAME'];
				$lodgeEmail = $row['EMAIL_ADDR'];
			}
			$query = 'SELECT * FROM state_directory WHERE STATE_NUM = '. $arow['STATE'];
			$result = mysqli_query($db->connection, $query);
			if($result) {
				$row = mysqli_fetch_array($result);
				$stateName = $row['STATE_NAME'];
			}
			$query = 'SELECT * FROM country_directory WHERE COUNTRY_NUM = ' . $arow['COUNTRY'];
			$result = mysqli_query($db->connection, $query);
			if($result) {
				$row = mysqli_fetch_array($result);
				$countryName = $row['COUNTRY_NAME'];
			}
			$mentor = 'No Mentor Assigned';
			$mentorEmail = 'No Mentor Assigned';
			if ($arow['MENTOR'] != 0) {
				$query = 'SELECT NICKNAME, EMAIL FROM '. $ktable .' WHERE RECORD_NUM = '. $arow['MENTOR'];
				$result = mysqli_query($db->connection, $query);
				if($result) {
					$row = mysqli_fetch_array($result);
					$mentor = $row['NICKNAME'];
					$mentorEmail = $row['EMAIL'];
				}
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
				  <tr><td>Assigned Mentor: </td><td>'. $mentor . '</td></tr>
				  <tr><td>Mentor eMail: </td><td>'. $mentorEmail . '</td></tr>
				  <tr><td colspan="2"> </td><tr>
				  <tr><td>First Name: </td><td>' . $arow['FNAME'] . '</td></tr>
				  <tr><td>Last Name: </td><td>' . $arow['LNAME'] . '</td></tr>
				</table><br>
				<table>
				  <tr><td>Street: </td><td>' . $arow['STREET_ADDRESS'] . '</td></tr>
				  <tr><td>City: </td><td>' . $arow['CITY'] . '</td></tr>
				  <tr><td>State: </td><td>' . $stateName . '</td></tr>
				  <tr><td>Zip Code: </td><td>' . $arow['ZIP_CODE'] . '</td></tr>
				  <tr><td>Country: </td><td>' . $countryName . '</td></tr>
				  <tr><td>Phone Number: </td><td>' . $arow['PHONE'] . '</td></tr>
				  <tr><td>eMail Address: </td><td>' . $arow['EMAIL'] . '</td></tr>
				</table><br>
				<p><b>Next Steps:</b><br>
				* Commander sends welcome email<br>
				* Send Provisional contact to mentor<br>
				* Send Provisional Checklist to mentor<br></p>
			  </body>
			  </html>
			  ';
			$subject = $arow['FNAME'] . ' ' . $arow['LNAME'] . ' - Added to Database';
			mail('grand.council@templarknightsmc.com, ' . $lodgeEmail, $subject , $message, $headers);
			
			// Update the approved flag in the database
			$query = 'UPDATE '.$atable.' SET APPROVED = 1, PROCESSING_CHECK = '.$processing_check.', PROCESSING_BY = "'.$processing_by.'" WHERE RECORD_NUM = '.$_POST['record_num'];
			$result = mysqli_query($db->connection, $query);
			if (!$result) {
				echo 'Updating the approved flag failed';
			}
		}
	}
}

switch ($button_action) {
	case 1:
		header("Location: http://member.templarknightsmc.com/provisional-progress/application-status?reject");
		break;
	case 2:
		header("Location: http://member.templarknightsmc.com/provisional-progress/application-status?update&applicant_id=".$_POST['record_num']);
		break;
	case 3:
		header("Location: http://member.templarknightsmc.com/provisional-progress/application-status?approved");
		break;
}

mysqli_close($db->connection);
	
?>