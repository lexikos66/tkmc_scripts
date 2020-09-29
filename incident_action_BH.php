<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getWritable('master');
$corollarytable='discipline_corollary';
$table='discipline_reports';
$incidentDate = $_POST['incidentDate'];
$incidentTime = $_POST['incidentTime'];
$guests = $_POST['guests'];
$incidentType = $_POST['incidentType'];
$incidentDetails = $_POST['incidentDetails'];
$incidentActions = $_POST['incidentAction'];
$incidentNotes = $_POST['incidentNotes'];
$councilPresent = $_POST['councilPresent'];
$lodgeNum = $_POST["lodgeNum"];
$personsInvolved = implode(",",$_POST['involvedPersons']);
$personsReporting = implode(",",$_POST['reportingPersons']);

$incidentType = $incidentType + 1;
if ($personsInvolved != '') {
	$query = 'INSERT INTO ' . $table . ' (report_date, report_time, guests, incident_type, incident_details, incident_action, incident_notes, council_present)
	VALUES ("' . $incidentDate . '", "' . $incidentTime . '", "' . $guests . '", "' . $incidentType . '", "' . $incidentDetails . '", "' . $incidentActions . '", "' . $incidentNotes . '", "' . $councilPresent . '")';

	$incidentresult = mysql_query($query, $db->connection);
	if($incidentresult) {
		$query = 'SELECT * FROM ' . $table . ' WHERE incident_num = (SELECT MAX(incident_num) FROM ' . $table . ')';
		$result = mysql_query($query, $db->connection);
		$row = mysql_fetch_array($result);
		$incidentNum = $row['incident_num'];
		$involvedPeeps = '';
		$reportingPeeps = '';
  
		if($result) {
			foreach(explode(",",$personsInvolved) as $value) {
				$query = 'INSERT INTO ' . $corollarytable . ' (incident_num, knight_num, is_involved)
				VALUES ("' . $incidentNum . '","' . $value . '",1)';
				$result = mysql_query($query, $db->connection);
				if(!$result) {
					exit("Failed to update Corollary Table: " . mysql_error());
				}
				$knightquery = 'SELECT * FROM knight_directory WHERE RECORD_NUM = ' . $value;
				$knightresult = mysql_query($knightquery, $db->connection);
				if($knightresult) {
					$knightrow = mysql_fetch_array($knightresult);
					if($knightrow['NICKNAME'] == "") {
					$involvedPeeps = $involvedPeeps . $knightrow['FNAME'] . ' ' . $knightrow['LNAME'] . ', ';
					}
					else {
					$involvedPeeps = $involvedPeeps . $knightrow['NICKNAME'] . ', ';
					}
				}
			}
			foreach(explode(",",$personsReporting) as $value) {
				$query = 'INSERT INTO ' . $corollarytable . ' (incident_num, knight_num, is_involved)
				VALUES ("' . $incidentNum . '","' . $value . '",0)';
				$result = mysql_query($query, $db->connection);
				if(!$result) {
					exit("Failed to update Corollary Table: " . mysql_error());
				}
				$knightquery = 'SELECT * FROM knight_directory WHERE RECORD_NUM = ' . $value;
				$knightresult = mysql_query($knightquery, $db->connection);
				if($knightresult) {
					$knightrow = mysql_fetch_array($knightresult);
					if($knightrow['NICKNAME'] == "") {
						$reportingPeeps = $reportingPeeps . $knightrow['FNAME'] . ' ' . $knightrow['LNAME'] . ', ';
					} else {
						$reportingPeeps = $reportingPeeps . $knightrow['NICKNAME'] . ', ';
					}
				}
			}
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' .  "\r\n";
			$headers .= 'From: TKMC Discipline Action <contact@templarknightsmc.com>' . "\r\n";
			$headers .= 'Reply-To: contact@templarknightsmc.com' . "\r\n";

			$message = '
			<html>
			<head>
				<title>New Discipline Action Recorded</title>
			</head>
			<body>
				<p>Disciplinary Action was recorded:</p><br>
				<table>
				<tr><td colspan="2" valign="center"><b>Date Reported: ' . $incidentDate . ' @ ' . $incidentTime . '</b></td></tr>
				<tr><td>Person(s) Involved: </td><td>' . substr($involvedPeeps,0,-2) . '</td></tr>
				<tr><td>Guest(s) Involved: </td><td>' . $guests . '</td></tr>
				<tr><td>Reporting Person(s): </td><td>' . substr($reportingPeeps,0,-2) . '</td></tr>
				<tr><td colspan="2" valign="center"><b><hr></b></td></tr>
				<tr><td>Type of Incident: ' . $row['incident_type'] . '</td><td></td></tr>
				<tr><td colspan="2" valign="left"><b>Details of Incident:</b></td></tr>
				<tr><td colspan="2" valign="left">' . $incidentDetails . '</td></tr>
				<tr><td colspan="2" valign="center"></td></tr>
				<tr><td colspan="2" valign="left"><b>Actions Taken:</b></td></tr>
				<tr><td colspan="2" valign="left">' . $incidentActions . '</td></tr>
				<tr><td colspan="2" valign="center"></td></tr>
				<tr><td colspan="2" valign="left"><b>Other Notes:</b></td></tr>
				<tr><td colspan="2" valign="left">' . $incidentNotes . '</td></tr>
				<tr><td colspan="2" valign="center"><b><hr></b></td></tr>
				<tr><td>Council Members Present:</td><td>' . $councilPresent . '</td></tr>
				</table><br>
			</body>
			</html>
			';
			$subject = 'Incident #' . $incidentNum . ' Re: ' . substr($involvedPeeps,0,-2) . ' - ' . $incidentDate;
			$mail_to = 'hollywood@templarknightsmc.com, johnny@templarknightsmc.com, ghost@templarknightsmc.com';
			mail($mail_to, $subject , $message, $headers);
			header("Location: http://templarknightsmc.com/council-area/incident-entry?lodgeNum=".$lodgeNum);
		}
	} else {
		echo 'Failed to insert Incident Data.';
	}
}
mysql_close($db->connection);
?>