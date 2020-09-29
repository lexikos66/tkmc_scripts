<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect To Database
$db = LocalDatabase::getWritable('master');
$atable = 'applicant_directory';
$ktable = 'knight_directory';
$stable = 'state_directory';
$ctable = 'country_directory';
$ltable = 'lodge_directory';
$lodgeName = '';
$isEnabled = '';

// If applicant_ID is not given, then show all active applicants
if (!isset($_GET['applicant_id'])) {

	if (isset($_GET['approved'])) {
		echo '<p style="font-weight:bold;font-size:16px;">The new Provisional has been recorded in the Master Database successfully.</p>';
	}

	$query = 'SELECT FNAME, LNAME, LODGE, RECORD_NUM, DATE_APPLIED, PROCESSING_CHECK, PROCESSING_BY FROM '.$atable.' WHERE APPROVED = 0 && REJECTED = 0 ORDER BY LODGE, FNAME';
	$result = mysqli_query($db->connection, $query);
	if ($result) {
		while($row = mysqli_fetch_array($result)){
			if ($row['LODGE']) {
				$lquery = 'SELECT LODGE_NAME FROM '.$ltable.' WHERE LODGE_NUM = '.$row['LODGE'].' LIMIT 1';
				$lresult = mysqli_query($db->connection, $lquery);
				if ($lresult) {
					$lrow = mysqli_fetch_array($lresult);
					$lodgeName = $lrow['LODGE_NAME'];
				}
			} else {
				$lodgeName = 'No Lodge Assigned';
			}
			echo '<p><a href="http://member.templarknightsmc.com/provisional-progress/application-status?applicant_id='.$row['RECORD_NUM'].'">'.$row['FNAME'].' '.$row['LNAME'].'</a> - '.$lodgeName.' (Date Applied: '.$row['DATE_APPLIED'].')';
			if ($row['PROCESSING_CHECK']) {
				echo ' Status: PROCESSING initiated by '.ucfirst($row['PROCESSING_BY']).'</p>';
			} else {
				echo ' Status: NEW</p>';
			}
		}
	}
} else {
	echo '<p><a href="http://member.templarknightsmc.com/provisional-progress/application-status">Back</a></p>';
	echo '<p><i><b>Ensure you UPDATE each change before approving, otherwise, information may be lost.</b></i></p>';

	$todaysDate = getdate();
	$year = $todaysDate['year'];
	$month = $todaysDate['month'];	
	$day = formatDate($todaysDate['mday']);
	$query = 'SELECT * FROM '.$atable.' WHERE RECORD_NUM = '.$_GET['applicant_id'];
	$result = mysqli_query($db->connection, $query);
	if ($result) {
		$row = mysqli_fetch_array($result);
		echo '<p style="text-align:center;font-size:24px">'.$row['FNAME'].' '.$row['LNAME'].'</p>';
		echo '<hr>';
		echo '<p><i>Please ensure all checks have been made, a Lodge has been assigned and a mentor has been assigned (if appropriate) before approving any applicant submission.</i>';

		echo '<form action="http://templarknightsmc.com/scripts/application_status_action.php" method="post">';
		echo '<input type="hidden" name="record_num" value="'.$_GET['applicant_id'].'"></p>';
		// Marshals verification area
		$current_user = wp_get_current_user();
		if (!in_array("grand_council", $current_user->roles)) { $isEnabled = 'disabled'; }
		echo '<div style="display:none;"><input type="hidden" name="processing_by" value="'.$current_user->display_name.'"></div>';
		echo '<p style="text-align:center;">';
		$checkboxString = '<input type="checkbox" name="processing_check" value="1" ' . $isEnabled . ' ';
		if ($row['PROCESSING_CHECK']) {
			echo $checkboxString . 'checked />Application is being processed by '.ucfirst($row['PROCESSING_BY']).'<br>';
		} else {
			echo $checkboxString . '/>Processing Application.<br>';
		}
		echo '</p>';
		echo '<table>';
		$checkboxString = '<tr><td><input type="checkbox" name="cruiser_check" value="1" ' . $isEnabled . ' ';
		if ($row['CRUISER_CHECK']) {
			echo $checkboxString . 'checked />Motorcycle meets style and size regulations.</td>';
		} else {
			echo $checkboxString . ' />Motorcycle meets style and size regulations.</td>';
		}
		$checkboxString = '<td><input type="checkbox" name="affiliations_check" value="1" ' . $isEnabled . ' ';
		if ($row['AFFILIATIONS_CHECK']) {
			echo $checkboxString . 'checked />Other club memberships and affiliations have been verified.</td></tr>';
		} else {
			echo $checkboxString . '/>Other club memberships and affiliations have been verified.</td></tr>';
		}
		$checkboxString = '<tr><td><input type="checkbox" name="fb_check" value="1" ' . $isEnabled . ' ';
		if ($row['FB_CHECK']) {
			echo $checkboxString . 'checked/>Facebook and other social media have been verified.</td>';
		} else {
			echo $checkboxString . '/>Facebook and other social media have been verified.</td>';
		}
		$checkboxString = '<td><input type="checkbox" name="spokeo_check" value="1" ' . $isEnabled . ' ';
		if ($row['SPOKEO_CHECK']) {
			echo $checkboxString . 'checked/>Spokeo and other sites have been checked.</td></tr>';
		} else {
			echo $checkboxString . '/>Spokeo and other sites have been checked.</td></tr>';
		}
		$checkboxString = '<tr><td><input type="checkbox" name="military_check" value="1" ' . $isEnabled . ' ';
		if ($row['MILITARY_CHECK']) {
			echo $checkboxString . 'checked />Military and DOD records have been verified.</td>';
		} else {
			echo $checkboxString . '/>Military and DOD records have been verified.</td>';
		}
		$checkboxString = '<td><input type="checkbox" name="felony_check" value="1" ' . $isEnabled . ' ';
		if ($row['FELONY_CHECK']) {	
			echo $checkboxString . 'checked/>Felony background information has been verified.</td></tr>';
		} else {
			echo $checkboxString . '/>Felony background information has been verified.</td></tr>';
		}
		$checkboxString = '<tr><td><input type="checkbox" name="driving_check" value="1" ' . $isEnabled . ' ';
		if ($row['DRIVING_CHECK']) {
			echo $checkboxString . 'checked />Driving record verified.</td>';
		} else {
			echo $checkboxString . '/>Driving record verified.</td>';
		}
		$checkboxString = '<td><input type="checkbox" name="insurance_check" value="1" ' . $isEnabled . ' ';
		if ($row['INSURANCE_CHECK']) {	
			echo $checkboxString . 'checked/>Insurance verified.</td></tr>';
		} else {
			echo $checkboxString . '/>Insurance verified.</td></tr>';
		}
		$checkboxString = '<tr><td><input type="checkbox" name="appfee_check" value="1" ' . $isEnabled . ' ';
		if ($row['APPFEE_CHECK']) {	
			echo $checkboxString . 'checked/>Application Fee received.</td>';
		} else {
			echo $checkboxString . '/>Application Fee received.</td>';
		}
		echo '<td></td></tr>';

		// Get Assigned Lodge
		echo '<tr><td>Lodge Assigned: <select name="lodgeNum">';
		$query = 'SELECT * FROM ' . $ltable . ' WHERE date_disbanded = "0000-00-00"';
		$lodgeresult = mysqli_query($db->connection, $query);
		if($lodgeresult) {
			if ($row['LODGE'] == 0) {
				echo '<option selected="yes" value="0">No Lodge Assigned</option>';
			}
			while($lodgerow = mysqli_fetch_array($lodgeresult)){
				if($lodgerow['LODGE_NUM'] == $row['LODGE']) {
					echo '<option selected="yes" value="' . $lodgerow['LODGE_NUM'] . '">' . $lodgerow['LODGE_NAME'] . '</option>';
				} else {
					echo '<option value="' . $lodgerow['LODGE_NUM'] . '">' . $lodgerow['LODGE_NAME'] . '</option>';
				}
			}
			echo '</select></td>';
		}
		
		// Get Assigned Mentor
		echo '<td>Mentor Assigned: <select name="mentor">';
		if ($row['MENTOR'] == 0) {
			echo '<option selected="yes" value="0">No Mentor Assigned</option>';
		}
		$query = 'SELECT * FROM ' . $ktable . ' WHERE DATE_WITHDREW = "0000-00-00" AND LODGE = "'.$row['LODGE'].'" ORDER BY LODGE, NICKNAME';
		$mentorresult = mysqli_query($db->connection, $query);
		if($mentorresult) {
			while($mentorrow = mysqli_fetch_array($mentorresult)) {
				if($mentorrow['RANK'] != "Provisional" && $mentorrow['RANK'] != "Honorary") {
					if($mentorrow['RECORD_NUM'] == $row['MENTOR']) {
						echo '<option selected="yes" value="' . $mentorrow['RECORD_NUM'] . '">' . $mentorrow['FNAME'] . ' "' . $mentorrow['NICKNAME'] . '" ' . $mentorrow['LNAME'] . '</option>';
					} else {
						echo '<option value="' . $mentorrow['RECORD_NUM'] . '">' . $mentorrow['FNAME'] . ' "' . $mentorrow['NICKNAME'] . '" ' . $mentorrow['LNAME'] . '</option>';
					}
				}
			}			
		}
		echo '</select></td></tr></table>';
		echo '<p>Notes:<br><textarea name="notes" cols="100" rows="5" ' . $isEnabled . '>'.$row['NOTES'].'</textarea></p>';
		echo '<p><input type="submit" name="button_type" value="Update Application" ' . $isEnabled . '>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="button_type" value="Approve Application" ' . $isEnabled . '>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="button_type" value="Reject Application" ' . $isEnabled . '></p>';
		echo '<hr><hr>';
		// Form information
		echo '<p>First Name:<input type="text" name="firstName" size="30" value="'.$row['FNAME'].'" disabled />&nbsp;&nbsp;&nbsp;&nbsp;Last Name:<input type="text" name="lastName" size="30" value="'.$row['LNAME'].'" disabled />&nbsp;&nbsp;&nbsp;&nbsp;Age:<input type="text" name="age" size="5"  value="'.$row['AGE'].'" disabled /></p>';
		echo '<p>Address:<input type="text" name="address" size="100"  value="'.$row['STREET_ADDRESS'].'" disabled/></p>';
		echo '<p>City:<input type="text" name="city" size="30"  value="'.$row['CITY'].'" disabled />&nbsp;&nbsp;&nbsp;&nbsp;State:<input type="text" name="state" size="20" value="'.getState($row['STATE'], $db).'" disabled />&nbsp;&nbsp;&nbsp;&nbsp;Zip:<input type="text" name="zipCode" size="20"  value="'.$row['ZIP_CODE'].'" disabled /></p>';
		echo '<p>Country:<input type="text" name="country" size="20" value="'.getCountry($row['COUNTRY'], $db).'" disabled />&nbsp;&nbsp;&nbsp;&nbsp;Hometown:<input type="text" name="homeTown" size="30"  value="'.$row['HOME_TOWN'].'" disabled /></p>';
		echo '<p>Phone (No dashes or +):<input type="text" name="phoneNum" size="30" value="'.$row['PHONE'].'" disabled />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;eMail:<input type="text" name="emailAddr" size="40" value="'.$row['EMAIL'].'" disabled /><br>*For US applicants, please include area code, ie 8015555555<br>*For International applicants, please include country code, ie 46855555555</p>';
		echo '<hr>';
		echo '<p style="text-align:center;font-size:16px">Background Information</p>';
		if (strpos($row['DRIVERS_LICENSE'], 'none') === 0) {
			echo '<p style="color:red;"><input type="radio" name="validDL" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="validDL" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Do you have a valid Drivers License? #<input type="text" name="driversLicense" size="30" value="'.substr($row['DRIVERS_LICENSE'],4).'" disabled><br><i>*Please submit a copy of your driver\'s license record to document identification and/or endorsements.</i></p>';
		} else {
			echo '<p><input type="radio" name="validDL" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="validDL" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Do you have a valid Drivers License? #<input type="text" name="driversLicense" size="30" value="'.$row['DRIVERS_LICENSE'].'" disabled><br><i>*Please submit a copy of your driver\'s license record to document identification and/or endorsements.</i></p>';
		}
		if ($row['ENDORSEMENT']) {
			echo '<p><input type="radio" name="currentEndorsement" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="currentEndorsement" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Do you have a current motorcycle endorsement?</p>';
		} else {
			echo '<p style="color:red;"><input type="radio" name="currentEndorsement" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="currentEndorsement" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Do you have a current motorcycle endorsement?</p>';
		}
		if (strpos($row['M_SIZE'], 'none') === 0) {
			echo '<p style="color:red;"><input type="radio" name="motorcycleSize" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="motorcycleSize" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Do you currently own or have control of a motorcycle of more than 500cc?<br>Motorcycle Make<input type="text" name="mcMake" size="20" value="'.$row['M_MAKE'].'" disabled />&nbsp;&nbsp;Model:<input type="text" name="mcModel" size="20" value="'.$row['M_MODEL'].'" disabled />&nbsp;&nbsp;Size:<input type="text" name="mcSize" size="10"  value="'.substr($row['M_SIZE'],4).'" disabled/>&nbsp;&nbsp;Year:<input type="text" name="mcYear" size="10" value="'.$row['M_YEAR'].'" disabled />&nbsp;&nbsp;Years riding:<input type="text" name="mcYearsRiding" size="10" value="'.$row['YEARS_RIDING'].'" disabled /></p>';
		} else {
			echo '<p><input type="radio" name="motorcycleSize" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="motorcycleSize" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Do you currently own or have control of a motorcycle of more than 500cc?<br>Motorcycle Make<input type="text" name="mcMake" size="20" value="'.$row['M_MAKE'].'" disabled />&nbsp;&nbsp;Model:<input type="text" name="mcModel" size="20" value="'.$row['M_MODEL'].'" disabled />&nbsp;&nbsp;Size:<input type="text" name="mcSize" size="10"  value="'.$row['M_SIZE'].'" disabled/>&nbsp;&nbsp;Year:<input type="text" name="mcYear" size="10" value="'.$row['M_YEAR'].'" disabled />&nbsp;&nbsp;Years riding:<input type="text" name="mcYearsRiding" size="10" value="'.$row['YEARS_RIDING'].'" disabled /></p>';
		}
		if (strpos($row['INSURANCE'], 'none') === 0) {
			echo '<p style="color:red;"><input type="radio" name="validInsurance" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="validInsurance" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Is your motorcycle currently insured? Insurance Company:<input type="text" name="insuranceCompany" size="30" value="'.substr($row['INSURANCE'],4).'" disabled/></p>';
		} else {
			echo '<p><input type="radio" name="validInsurance" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="validInsurance" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Is your motorcycle currently insured? Insurance Company:<input type="text" name="insuranceCompany" size="30" value="'.$row['INSURANCE'].'" disabled/></p>';
		}
		if (strpos($row['AFFILIATIONS'], 'none') === 0) {
			echo '<p><input type="radio" name="otherAffiliation" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="otherAffiliation" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you a member or affiliated with any other motorcycle clubs?<br>Other motorcycle club membership(s) held:<input type="text" name="membershipsHeld" size="60" value="'.substr($row['AFFILIATIONS'],4).'" disabled/></p>';
		} else {
			echo '<p style="color:red;"><input type="radio" name="otherAffiliation" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="otherAffiliation" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you a member or affiliated with any other motorcycle clubs?<br>Other motorcycle club membership(s) held:<input type="text" name="membershipsHeld" size="60" value="'.$row['AFFILIATIONS'].'" disabled/></p>';
		}

		if (strpos($row['REFERRED_BY'], 'none') === 0) {
			echo '<p><input type="radio" name="referred" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="referred" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Were you referred by another Templar Knight member?<br>Name of member:<input type="text" name="memberReferred" size="60" value="'.substr($row['REFERRED_BY'],4).'" disabled/></p>';
		} else {
			echo '<p><input type="radio" name="referred" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="referred" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Were you referred by another Templar Knight member?<br>Name of member:<input type="text" name="memberReferred" size="60" value="'.$row['REFERRED_BY'].'" disabled/></p>';
		}
		echo '<p>List any additional professions or skills that may be relevant to the Club goals or event sponsors:<br><textarea name="skills" cols="100" rows="3" disabled>'.$row['SKILLS'].'</textarea></p>';
		echo '<p>How did you hear about us?<br><textarea name="howHeard" cols="100" rows="3" disabled>'.$row['HOW_HEARD'].'</textarea></p>';
		echo '<p style="text-align:center;font-size:16px;font-weight:bold;">List explanations to all yes answers below</p>';

		if ($row['FELONY']) {
			echo '<p style="color:red;"><input type="radio" name="felony" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="felony" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Have you ever been convicted of any felony?</p>';
		} else {
			echo '<p><input type="radio" name="felony" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="felony" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Have you ever been convicted of any felony?</p>';
		}
		
		if ($row['CRIMINAL']) {
			echo '<p style="color:red;"><input type="radio" name="criminal" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="criminal" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Have you been convicted of any criminal act within the last 5 years?</p>';
		} else {
			echo '<p><input type="radio" name="criminal" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="criminal" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Have you been convicted of any criminal act within the last 5 years?</p>';
		}

		if ($row['PAROLE']) {
			echo '<p style="color:red;"><input type="radio" name="parole" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="parole" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you currently on parole or probation?</p>';
		} else {
			echo '<p><input type="radio" name="parole" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="parole" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you currently on parole or probation?</p>';
		}

		if ($row['INVESTIGATION']) {
			echo '<p style="color:red;"><input type="radio" name="investigation" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="investigation" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you currently under investigation by any government agency?</p>'; 
		} else {
			echo '<p><input type="radio" name="investigation" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="investigation" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you currently under investigation by any government agency?</p>'; 
		}

		if ($row['COURT_ORDER']) {
			echo '<p style="color:red;"><input type="radio" name="courtOrder" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="courtOrder" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you under any court orders or protective orders as a respondent?</p>';
		} else {
			echo '<p><input type="radio" name="courtOrder" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="courtOrder" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you under any court orders or protective orders as a respondent?</p>';
		}

		if ($row['VIOLENCE']) {
			echo '<p style="color:red;"><input type="radio" name="violence" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="violence" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Have you been convicted of any domestic violence charges?</p>';
		} else {
			echo '<p><input type="radio" name="violence" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="violence" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Have you been convicted of any domestic violence charges?</p>';
		}

		if ($row['GANGS']) {
			echo '<p style="color:red;"><input type="radio" name="gangs" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="gangs" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you currently a member or affiliated with any gangs or hate groups?</p>';
		} else {
			echo '<p><input type="radio" name="gangs" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="gangs" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Are you currently a member or affiliated with any gangs or hate groups?</p>';
		}

		if ($row['DRUGS']) {
			echo '<p style="color:red;"><input type="radio" name="drugs" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="drugs" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Have you engaged in illegal use, possession, sale of narcotics or illicit drugs, during the last 5 years?</p>';
		} else {
			echo '<p><input type="radio" name="drugs" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="drugs" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Have you engaged in illegal use, possession, sale of narcotics or illicit drugs, during the last 5 years?</p>';
		}

		if ($row['DISHONOR']) {
			echo '<p style="color:red;"><input type="radio" name="dishonorable" value="1" checked disabled>Yes&nbsp;&nbsp;<input type="radio" name="dishonorable" value="0" disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Dishonorably discharged or forced to resign from any branch of the US government or country of origin?</p>';
		} else {
			echo '<p><input type="radio" name="dishonorable" value="1" disabled>Yes&nbsp;&nbsp;<input type="radio" name="dishonorable" value="0" checked disabled>No&nbsp;&nbsp;&nbsp;&nbsp;Dishonorably discharged or forced to resign from any branch of the US government or country of origin?</p>';
		}

		echo '<p>If you answered YES to any question above, please explain:<br><textarea name="yesExplanations" cols="100" rows="5"  disabled>'.$row['EXPLANATION'].'</textarea></p>';
		echo '<hr>';
		echo '<p>I would like to submit application of membership to become an active member of the Templar Knights Motorcycle Club.  I hereby make application for membership.  As a prospective member, I will respect my fellow club members as well as represent the club in a positive way.  Whether I am with the group or alone, I will carry myself with the utmost respect being that I am an extension of this organization.</p>';
		echo '<p><b>Indemnification:</b> I agree to indemnify and hold harmless the Templar Knights LLC/Motorcycle Club, its members, agents and assigns, individually and/or collectively, from all lawsuits, claims, damages, costs and attorneys\' fees that arise out of my presence or conduct at an Event and/or my violation or my representative\'s violations of any provision of the Application.  This provision will apply regardless of whether or not the lawsuit, claim, damages, costs and/or attorneys\' fees arise out of the negligence, in any form, of any of the Released Parties.  As I am releasing any claim my family, guardian, representative and/or estate might wish to make by reason of my injury or death, this indemnity provision shall specifically apply to such actions on my behalf.</p>';
		echo '<p><b>Insurance Responsibility:</b> I understand that the Templar Knights LLC/Motorcycle Club provides neither health nor life insurance.  I assume all responsibility for my doctor and/or hospital expenses and any loss or injury to personal property or myself in which I may become involved in by reason of participating in any event or related activity.</p>';
		
		if ($row['RENTAL']) {
			echo '<br><p style="font-weight:bold;"><input type="checkbox" name="rentalUnderstanding" value="1" checked disabled />I understand that Colors and/or patches are owned by Templar Knights LLC. Any and all fees paid are for the use and rental of said patches.</p>';
		} else {
			echo '<br><p style="font-weight:bold;color:red;"><input type="checkbox" name="rentalUnderstanding" value="1" disabled />I understand that Colors and/or patches are owned by Templar Knights LLC. Any and all fees paid are for the use and rental of said patches.</p>';
		}
		
		if ($row['SIGNATURE']) {
			echo '<p><input type="checkbox" name="signature" value="1" checked disabled/>By checking this box, I agree that this constitutes a legal digital signature and maintain that I have read the above application and am in agreement with all said provisions.</p><p>Date of Digital Signature: <u>'.$row['DATE_APPLIED'].'</u></p>';
		} else {
			echo '<p style="color:red;"><input type="checkbox" name="signature" value="1" disabled/>By checking this box, I agree that this constitutes a legal digital signature and maintain that I have read the above application and am in agreement with all said provisions.</p><p>Date of Digital Signature: <u>'.$row['DATE_APPLIED'].'</u></p>';
		}
		
		echo '<p style="font-weight:200;font-size:10px">';
		echo 'Remote Server: '.$row['IP_ADDRESS'].'<br>';
	}
}

mysqli_close($db->connection);
	
function formatDate($element) {
	if($element < 10) {
		return '0' . $element;
	} else {
		return $element;
	}
}

function getState($state_num, $db) {
	$query = 'SELECT STATE_NAME FROM state_directory WHERE STATE_NUM = '.$state_num;
	$result = mysqli_query($db->connection, $query);
	$row = mysqli_fetch_array($result);
	return $row['STATE_NAME'];
}

function getCountry($country_num, $db) {
	$query = 'SELECT COUNTRY_NAME FROM country_directory WHERE COUNTRY_NUM = '.$country_num;
	$result = mysqli_query($db->connection, $query);
	$row = mysqli_fetch_array($result);
	return $row['COUNTRY_NAME'];
}

?>