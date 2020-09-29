<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

if (isset($_GET['nocheck'])) {
	echo '<p style="font-weight:bold;font-size:16px;">You did not check both the Rental Agreement and Signature boxes.  Please use the BACK button on your browser and select both of these boxes before submitting your application.</p>';
} else {
	if (isset($_GET['success'])) {
		echo '<p style="font-weight:bold;font-size:16px;">Thank You.  Your application has been submitted successfully.</p>';
	}

	//Connect To Database
	$db = LocalDatabase::getReadable('master');
	$stable = 'state_directory';
	$ctable = 'country_directory';

	$todaysDate = getdate();
	$year = $todaysDate['year'];
	$month = $todaysDate['month'];
	$day = formatDate($todaysDate['mday']);
	echo '<p style="font-size:12px">After I submit my application, <a href="#appProcess">what happens next</a>?</p>';
	echo '<p style="text-align:center;font-size:24px">Templar Knights Motorcycle Club</p>';
	echo '<p style="text-align:center;font-size:16px">Provisional Knighthood Application</p>';
	echo '<p><i>Before submitting this application, please ensure that you meet all of the qualifications as outlined on the <a href="http://www.templarknightsmc.com/become-a-knight/requirements/">requirements</a> page. Please also ensure that you have read and agree with the <a href="http://www.templarknightsmc.com/become-a-knight/code-of-conduct/">code of conduct</a> and are in compliance with the <a href="http://www.templarknightsmc.com/become-a-knight/bike-types/">style and size</a> of acceptable motorcycles.</i></p>';

	echo '<form action="http://templarknightsmc.com/scripts/application_action.php" method="post">';
	echo '<p>First Name:<input type="text" name="firstName" size="30" />&nbsp;&nbsp;&nbsp;&nbsp;Last Name:<input type="text" name="lastName" size="30" />&nbsp;&nbsp;&nbsp;&nbsp;Age:<input type="text" name="age" size="5" /></p>';
	echo '<p>Address:<input type="text" name="address" size="100" /></p>';
	echo '<p>City:<input type="text" name="city" size="30" />&nbsp;&nbsp;&nbsp;&nbsp;State:'.state_dropdown_only($db, $stable).'&nbsp;&nbsp;&nbsp;&nbsp;Zip:<input type="text" name="zipCode" size="20" /></p>';
	echo '<p>Country:'.country_dropdown_only($db, $ctable).'&nbsp;&nbsp;&nbsp;&nbsp;Hometown:<input type="text" name="homeTown" size="30" /></p>';
	echo '<p>Phone (No dashes or +):<input type="text" name="phoneNum" size="30" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;eMail:<input type="text" name="emailAddr" size="40" /><br>*For US applicants, please include area code, ie 8015555555<br>*For International applicants, please include country code, ie 46855555555</p>';
	echo '<hr>';
	echo '<p style="text-align:center;font-size:16px">Background Information</p>';
	echo '<p><input type="radio" name="validDL" value="1">Yes&nbsp;&nbsp;<input type="radio" name="validDL" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Do you have a valid Drivers License? #<input type="text" name="driversLicense" size="30" /><br><i>*Please submit a copy of your driver\'s license record to document identification and/or endorsements to <a href="mailto:knights@templarknightsmc.com">knights@templarknightsmc.com</a>.</i></p>';
	echo '<p><input type="radio" name="currentEndorsement" value="1">Yes&nbsp;&nbsp;<input type="radio" name="currentEndorsement" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Do you have a current motorcycle endorsement?</p>';
	echo '<p><input type="radio" name="motorcycleSize" value="1">Yes&nbsp;&nbsp;<input type="radio" name="motorcycleSize" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Do you currently own or have control of a motorcycle of more than 500cc?<br>Motorcycle Make<input type="text" name="mcMake" size="20" />&nbsp;&nbsp;Model:<input type="text" name="mcModel" size="20" />&nbsp;&nbsp;Size:<input type="text" name="mcSize" size="10" />&nbsp;&nbsp;Year:<input type="text" name="mcYear" size="10" />&nbsp;&nbsp;Years riding:<input type="text" name="mcYearsRiding" size="10" /></p>';
	echo '<p><input type="radio" name="validInsurance" value="1">Yes&nbsp;&nbsp;<input type="radio" name="validInsurance" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Is your motorcycle currently insured? Insurance Company:<input type="text" name="insuranceCompany" size="30" /></p>';
	echo '<p><input type="radio" name="otherAffiliation" value="1">Yes&nbsp;&nbsp;<input type="radio" name="otherAffiliation" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Are you a member or affiliated with any other motorcycle clubs?<br>Other motorcycle club membership(s) held:<input type="text" name="membershipsHeld" size="60" /></p>';
	echo '<p><input type="radio" name="referred" value="1">Yes&nbsp;&nbsp;<input type="radio" name="referred" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Were you referred by another Templar Knight member?<br>Name of member:<input type="text" name="memberReferred" size="60" /></p>';
	echo '<p>List any additional professions or skills that may be relevant to the Club goals or event sponsors:<br><textarea name="skills" cols="100" rows="3"></textarea></p>';
	echo '<p>How did you hear about us?<br><textarea name="howHeard" cols="100" rows="3"></textarea></p>';
	echo '<p style="text-align:center;font-size:16px;font-weight:bold;">List explanations to all yes answers below</p>';
	echo '<p><input type="radio" name="felony" value="1">Yes&nbsp;&nbsp;<input type="radio" name="felony" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Have you ever been convicted of any felony?</p>';
	echo '<p><input type="radio" name="criminal" value="1">Yes&nbsp;&nbsp;<input type="radio" name="criminal" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Have you been convicted of any criminal act within the last 5 years?</p>';
	echo '<p><input type="radio" name="parole" value="1">Yes&nbsp;&nbsp;<input type="radio" name="parole" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Are you currently on parole or probation?</p>';
	echo '<p><input type="radio" name="investigation" value="1">Yes&nbsp;&nbsp;<input type="radio" name="investigation" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Are you currently under investigation by any government agency?</p>';
	echo '<p><input type="radio" name="courtOrder" value="1">Yes&nbsp;&nbsp;<input type="radio" name="courtOrder" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Are you under any court orders or protective orders as a respondent?</p>';
	echo '<p><input type="radio" name="violence" value="1">Yes&nbsp;&nbsp;<input type="radio" name="violence" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Have you been convicted of any domestic violence charges?</p>';
	echo '<p><input type="radio" name="gangs" value="1">Yes&nbsp;&nbsp;<input type="radio" name="gangs" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Are you currently a member or affiliated with any gangs or hate groups?</p>';
	echo '<p><input type="radio" name="drugs" value="1">Yes&nbsp;&nbsp;<input type="radio" name="drugs" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Have you engaged in illegal use, possession, sale of narcotics or illicit drugs, during the last 5 years?</p>';
	echo '<p><input type="radio" name="dishonorable" value="1">Yes&nbsp;&nbsp;<input type="radio" name="dishonorable" value="0">No&nbsp;&nbsp;&nbsp;&nbsp;Dishonorably discharged or forced to resign from any branch of the US government or country of origin?</p>';
	echo '<p>If you answered YES to any question above, please explain:<br><textarea name="yesExplanations" cols="100" rows="5"></textarea></p>';
	echo '<hr>';
	echo '<p>I would like to submit application of membership to become an active member of the Templar Knights Motorcycle Club.  I hereby make application for membership.  As a prospective member, I will respect my fellow club members as well as represent the club in a positive way.  Whether I am with the group or alone, I will carry myself with the utmost respect being that I am an extension of this organization.</p>';
	echo '<p><b>Indemnification:</b> I agree to indemnify and hold harmless the Templar Knights LLC/Motorcycle Club, its members, agents and assigns, individually and/or collectively, from all lawsuits, claims, damages, costs and attorneys\' fees that arise out of my presence or conduct at an Event and/or my violation or my representative\'s violations of any provision of the Application.  This provision will apply regardless of whether or not the lawsuit, claim, damages, costs and/or attorneys\' fees arise out of the negligence, in any form, of any of the Released Parties.  As I am releasing any claim my family, guardian, representative and/or estate might wish to make by reason of my injury or death, this indemnity provision shall specifically apply to such actions on my behalf.</p>';
	echo '<p><b>Insurance Responsibility:</b> I understand that the Templar Knights LLC/Motorcycle Club provides neither health nor life insurance.  I assume all responsibility for my doctor and/or hospital expenses and any loss or injury to personal property or myself in which I may become involved in by reason of participating in any event or related activity.</p>';
	echo '<br><p style=font-weight:bold><input type="checkbox" name="rentalUnderstanding" value="1" />I understand that Colors and/or patches are owned by Templar Knights LLC. Any and all fees paid are for the use and rental of said patches.</p>';
	echo '<p><input type="checkbox" name="signature" value="1" />By checking this box, I agree that this constitutes a legal digital signature and maintain that I have read the above application and am in agreement with all said provisions.</p><p>Date of Digital Signature: <u>'.$day.' '.$month.' '.$year.'</u></p>';
	echo '<input type="hidden" name="ipAddress" value="'.$_SERVER['REMOTE_ADDR'].'" />';
	echo '<input type="hidden" name="dateApplied" value="'.date("Y-m-d H:i:s").'" />';
	echo '<p><input type="submit" value="Submit Application"></p>';

	echo '<p style="font-weight:200;font-size:10px">';
	echo 'Remote Server: '.$_SERVER['REMOTE_ADDR'].'<br>';
	echo 'Proxy: '.$_SERVER['HTTP_X_FORWARDED_FOR'].'<br>';
	echo 'Time: '.date("Y-m-d H:i:s").' GMT<br></p>';
	echo '<div><br><hr><a name="appProcess" />
	<blockquote><p>Application Process</p><p>Once your application has been received, you will be:<br><ul><li>contacted by a member of the Council</li><li>granted the title of Provisional Knight</li><li>assigned to a Mentor and given the Club Articles</li><li>required to sign the Release of Liability Agreement</li></ul></p><p>You will work to complete your Provisional Knight Tasks as outlined in the Articles. This process will take anywhere between 3 months to a year to complete.</p><p>Once you have completed your Provisional Knight stage, your Mentor will present your name to the Council for final approval to Full Knighthood.</p><p>When Full Knighthood is obtained, you will be expected to:<br><ul><li>pay club dues and patch rental fees as outlined in the Articles</li><li>wear the Club patches</li><li>continue to be an active member of the Lodge and continue in keeping the standards of the TKMC</li></ul></p><p>Recruiting, membership and promoting the Club will be carried out without regard to race, creed, color, religion, national origin, age, marital status, political affiliation or ethnic heritage.</p></blockquote></div>';

	mysqli_close($db->connection);
}
	
function formatDate($element) {
	if($element < 10) {
		return '0' . $element;
	} else {
		return $element;
	}
}

?>