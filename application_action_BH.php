<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/plugins/fpdf/fpdf.php');
require_once(SCRIPTS_DIR.'/plugins/PHPMailer/PHPMailerAutoload.php');

class MyPDF extends FPDF {
	// Page Header
	function Header() {
		$this->SetFont('Times', 'B', 24);
		$this->SetFillColor(200);
		$this->Cell(0,12, 'Templar Knights Motorcycle Club', 1, 1, 'C', true);
	}
	
	function getBoolean($value, $ppdf) {
		$retval = '';
		$ppdf->SetFont('Times', 'B', 12);
		if ($value == '1') {
			$ppdf->Cell(15, 6, 'Yes', 0, 0);
		} else {
			$ppdf->Cell(15, 6, 'No', 0, 0);
		}
		$ppdf->SetFont('Times', '', 12);
	}
}

if ($_POST['rentalUnderstanding'] && $_POST['signature']) {
	//Connect To Database
	$db = LocalDatabase::getWritable('master');
	$atable='applicant_directory';

	// Compute fields that have values as well as true/false values
	if ($_POST['referred']) {
		$referredBy = $_POST['memberReferred'];
	} else {
		$referredBy = 'none'.$_POST['memberReferred'];
	}
	if ($_POST['validDL']) {
		$driversLicense = $_POST['driversLicense'];
	} else {
		$driversLicense = 'none'.$_POST['driversLicense'];
	}
	if ($_POST['validInsurance']) {
		$insuranceCompany = $_POST['insuranceCompany'];
	} else {
		$insuranceCompany = 'none'.$_POST['insuranceCompany'];
	}
	if ($_POST['otherAffiliation']) {
		$membershipsHeld = $_POST['membershipsHeld'];
	} else {
		$membershipsHeld = 'none'.$_POST['membershipsHeld'];
	}
	if ($_POST['motorcycleSize']) {
		$mcSize = $_POST['mcSize'];
	} else {
		$mcSize = 'none'.$_POST['mcSize'];
	}

	$query = 'INSERT INTO ' . $atable . ' (FNAME, LNAME, AGE, STREET_ADDRESS, CITY, STATE, ZIP_CODE, COUNTRY, HOME_TOWN, PHONE, EMAIL, REFERRED_BY, DRIVERS_LICENSE, ENDORSEMENT, M_MAKE, M_MODEL, M_SIZE, M_YEAR, YEARS_RIDING, INSURANCE, AFFILIATIONS, SKILLS, HOW_HEARD, FELONY, CRIMINAL, PAROLE, INVESTIGATION, COURT_ORDER, VIOLENCE, GANGS, DRUGS, DISHONOR, EXPLANATION, RENTAL, SIGNATURE, DATE_APPLIED, IP_ADDRESS) VALUES ("' . mysqli_real_escape_string($_POST['firstName']) . '","' . mysqli_real_escape_string($_POST['lastName']) . '","' . mysqli_real_escape_string($_POST['age']) . '","' . mysqli_real_escape_string($_POST['address']) . '","' . mysqli_real_escape_string($_POST['city']) . '","' . mysqli_real_escape_string($_POST['state']) . '","' . mysqli_real_escape_string($_POST['zipCode']) . '","' . mysqli_real_escape_string($_POST['country']) . '","' . mysqli_real_escape_string($_POST['homeTown']) . '","' . mysqli_real_escape_string($_POST['phoneNum']) . '","' . mysqli_real_escape_string($_POST['emailAddr']) . '","' . mysqli_real_escape_string($referredBy) . '","' . mysqli_real_escape_string($driversLicense) . '","' . mysqli_real_escape_string($_POST['currentEndorsement']) . '","' . mysqli_real_escape_string($_POST['mcMake']) . '","' . mysqli_real_escape_string($_POST['mcModel']) . '","' . mysqli_real_escape_string($mcSize) . '","' . mysqli_real_escape_string($_POST['mcYear']) . '","' . mysqli_real_escape_string($_POST['mcYearsRiding']) . '","' . mysqli_real_escape_string($insuranceCompany) . '","' . mysqli_real_escape_string($membershipsHeld) . '","' . mysqli_real_escape_string($_POST['skills']) . '","' . mysqli_real_escape_string($_POST['howHeard']) . '","'. mysqli_real_escape_string($_POST['felony']) . '","' . mysqli_real_escape_string($_POST['criminal']) . '","' . mysqli_real_escape_string($_POST['parole']) . '","' . mysqli_real_escape_string($_POST['investigation']) . '","' . mysqli_real_escape_string($_POST['courtOrder']) . '","' . mysqli_real_escape_string($_POST['violence']) . '","' . mysqli_real_escape_string($_POST['gangs']) . '","' . mysqli_real_escape_string($_POST['drugs']) . '","' . mysqli_real_escape_string($_POST['dishonorable']) . '","' . mysqli_real_escape_string($_POST['yesExplanations']) . '","' . mysqli_real_escape_string($_POST['rentalUnderstanding']) . '","' . mysqli_real_escape_string($_POST['signature']) . '","' . mysqli_real_escape_string($_POST['dateApplied']) . '","' . mysqli_real_escape_string($_POST['ipAddress']) . '")';

	$result = mysqli_query($db->connection, $query);
	if ($result) {
		make_pdf($db);
		mail_pdf($db);
		header("Location: http://templarknightsmc.com/become-a-knight/application?success");
	} else {
	  echo 'Failed to Create Record';
	}
	mysqli_close($db->connection);
} else {
	header("Location: http://templarknightsmc.com/become-a-knight/application?nocheck");
}

function mail_pdf($db) {
	$email = new PHPMailer();
	$email->From = 'contact@templarknightsmc.com';
	$email->FromName = 'TKMC Administrator';
	$email->Subject = 'Application Received - '.$_POST['firstName'].' '.$_POST['lastName'];
    $email->Body = '
      <html>
      <head>
        <title>New Application Received</title>
      </head>
      <body>
        <p>A new application has been received for: </p><br>
        <table>
          <tr><td>First Name: </td><td>' . $_POST['firstName'] . '</td></tr>
          <tr><td>Last Name: </td><td>' . $_POST['lastName'] . '</td></tr>
        </table><br>
        <table>
          <tr><td>Street: </td><td>' . $_POST['address'] . '</td></tr>
          <tr><td>City: </td><td>' . $_POST['city'] . '</td></tr>
          <tr><td>State: </td><td>' . getState($_POST['state'], $db) . '</td></tr>
          <tr><td>Zip Code: </td><td>' . $_POST['zipCode'] . '</td></tr>
          <tr><td>Country: </td><td>' . getCountry($_POST['country'], $db) . '</td></tr>
          <tr><td>Phone Number: </td><td>' . $_POST['phoneNum'] . '</td></tr>
          <tr><td>eMail Address: </td><td>' . $_POST['emailAddr'] . '</td></tr>
        </table><br>
      </body>
      </html>
      ';
	$email->AltBody = 'A new application has been received for: '.$_POST['firstName'].' '.$_POST['lastName'];
    $email->AddAddress('knights@templarknightsmc.com');
	$email->AddAttachment('/home1/templark/tmp/applications/Application_'.$_POST['firstName'].'_'.$_POST['lastName'].'.pdf');
	$email->isHTML(true);
	$email->addReplyTo($_POST['emailAddr'], $_POST['firstName'].' '.$_POST['lastName']);
	$email->Send();
}

function make_pdf($db) {
	$pdf = new MyPDF('P', 'mm', 'Letter');
	$pdf->AddPage();
	$pdf->Rect(10, 45, 196, 225);
	$pdf->SetFont('Times', 'B', 16);
	$pdf->Cell(0, 10, 'Provisional Knighthood Application', 0, 1, 'C');
	$pdf->SetFont('Times', 'I', 10);
	$pdf->MultiCell(0, 4, 'Before submitting this application, please ensure that you meet all of the qualifications as outlined on the requirements page.  Please also ensure that you have read and agree with the code of conduct and are in compliance with the style and size of acceptable motorcycles.', 0, 'C');
	$pdf->Ln();

	$pdf->SetRightMargin(15);
	$pdf->SetLeftMargin(15);
	$pdf->SetFont('Times', '', 12);
	$pdf->SetFillColor(240);

	$pdf->Cell(25, 6, 'First Name:', 0, 0); $pdf->Cell(55, 6, $_POST['firstName'], 'B', 0);
	$pdf->Cell(25, 6, 'Last Name:', 0, 0); $pdf->Cell(55, 6, $_POST['lastName'], 'B', 0);
	$pdf->Cell(10, 6, 'Age:', 0, 0); $pdf->Cell(10, 6, $_POST['age'], 'B', 1);
	$pdf->Ln(3);
	$pdf->Cell(20, 6, 'Address:', 0, 0); $pdf->Cell(0, 6, $_POST['address'], 'B', 1);
	$pdf->Ln(3);
	$pdf->Cell(15, 6, 'City:', 0, 0); $pdf->Cell(70, 6, $_POST['city'], 'B', 0);
	$pdf->Cell(15, 6, 'State:', 0, 0); $pdf->Cell(50, 6, getState($_POST['state'], $db), 'B', 0);
	$pdf->Cell(10, 6, 'Zip:', 0, 0); $pdf->Cell(20, 6, $_POST['zipCode'], 'B', 1);
	$pdf->Ln(3); 
	$pdf->Cell(20, 6, 'Country:', 0, 0); $pdf->Cell(70, 6, getCountry($_POST['country'], $db), 'B', 0);
	$pdf->Cell(25, 6, 'Hometown:', 0, 0); $pdf->Cell(70, 6, $_POST['homeTown'], 'B', 1);
	$pdf->Ln(3);
	$pdf->Cell(15, 6, 'Phone:', 0, 0); $pdf->Cell(60, 6, $_POST['phoneNum'], 'B', 0);
	$pdf->Cell(15, 6, 'Email:', 0, 0); $pdf->Cell(90, 6, $_POST['emailAddr'], 'B', 1);
	$pdf->Ln(10);

	// Background Information
	$pdf->Line(10, 96, 206, 96);
	$pdf->SetFont('Times', 'BU', 12);
	$pdf->Cell(0, 6, 'Background Information', 0, 1, 'C');
	$pdf->Ln(3);
	$pdf->getBoolean($_POST['validDL'], $pdf);
	$pdf->Cell(70, 6, 'Do you have a valid Drivers License? #', 0, 0);
	$pdf->Cell(70, 6, $_POST['driversLicense'], 'B', 1);
	$pdf->Cell(15, 6, '', 0, 0);
	$pdf->SetFont('Times', 'I', 9);
	$pdf->Cell(0, 4, '*Please submit a copy of your driver\'s license record to document identification and/or endorsements.', 0, 1);
	$pdf->Ln(3);
	$pdf->getBoolean($_POST['currentEndorsement'], $pdf);
	$pdf->Cell(60, 6, 'Do you have a current motorcycle endorsement?', 0, 1);
	$pdf->getBoolean($_POST['motorcycleSize'], $pdf);
	$pdf->Cell(0, 6, 'Do you currently own or have control of a motorcycle of more than 500cc?', 0, 1);
	$pdf->Cell(35, 6, 'Motorcycle Make:', 0, 0); $pdf->Cell(50, 6, $_POST['mcMake'], 'B', 0);
	$pdf->Cell(15, 6, 'Model:', 0, 0); $pdf->Cell(50, 6, $_POST['mcModel'], 'B', 0);
	$pdf->Cell(10, 6, 'Size:', 0, 0); $pdf->Cell(15, 6, $_POST['mcSize'], 'B', 1);
	$pdf->Cell(15, 6, 'Year:', 0, 0); $pdf->Cell(15, 6, $_POST['mcYear'], 'B', 0);
	$pdf->Cell(25, 6, 'Years Riding:', 0, 0); $pdf->Cell(15, 6, $_POST['mcYearsRiding'], 'B', 0);
	$pdf->Ln(10);
	$pdf->getBoolean($_POST['validInsurance'], $pdf);
	$pdf->Cell(80, 6, 'Is your motorcycle currently insured? Ins. Co.', 0, 0); $pdf->Cell(85, 6, $_POST['insuranceCompany'], 'B', 1);
	$pdf->Ln(3);
	$pdf->getBoolean($_POST['otherAffiliation'], $pdf);
	$pdf->Cell(0, 6, 'Are you a member or affliliated with any other motorcycle clubs?', 0, 1);
	$pdf->Cell(80, 6, 'Other motorcycle club membership(s) held:', 0, 0); $pdf->Cell(100, 6, $_POST['membershipsHeld'], 'B', 1);
	$pdf->Ln(3);
	$pdf->getBoolean($_POST['referred'], $pdf);
	$pdf->Cell(0, 6, 'Were you referred by another Templar Knight member?', 0, 1);
	$pdf->Cell(35, 6, 'Name of member:', 0, 0); $pdf->Cell(80, 6, $_POST['memberReferred'], 'B', 1);
	$pdf->Ln(6);
	$pdf->Cell(0, 6, 'List any additional professions or skills that may be relevant to the club goals or event sponsors:', 0, 1);
	$pdf->SetFont('Times', 'U', 12);
	$pdf->MultiCell(0, 6, $_POST['skills']);
	$pdf->Ln(6);
	$pdf->SetFont('Times', '', 12);
	$pdf->Cell(0, 6, 'How did you hear about us?', 0, 1);
	$pdf->SetFont('Times', 'U', 12);
	$pdf->MultiCell(0, 6, $_POST['howHeard']);
	$pdf->Ln(3);
	$pdf->SetFont('Times', '', 12);
	$pdf->SetRightMargin(10);
	$pdf->SetLeftMargin(10);

	// Second Page
	$pdf->AddPage();
	$pdf->Ln(10);
	$pdf->Rect(10, 30, 196, 125);
	$pdf->SetRightMargin(20);
	$pdf->SetLeftMargin(20);
	$pdf->SetFont('Times', 'B', 12);
	$pdf->Cell(0, 6, 'List explanations to all yes answers below', 0, 1, 'C');
	$pdf->SetFont('Times', '', 12);
	$pdf->Ln(3);
	$pdf->getBoolean($_POST['felony'], $pdf); $pdf->Cell(0, 6, 'Have you ever been convicted of any felony?', 0, 1);
	$pdf->getBoolean($_POST['criminal'], $pdf); $pdf->Cell(0, 6, 'Have you been convicted of any criminal act within the last 5 years?', 0, 1);
	$pdf->getBoolean($_POST['parole'], $pdf); $pdf->Cell(0, 6, 'Are you currently on parole or probation?', 0, 1);
	$pdf->getBoolean($_POST['investigation'], $pdf); $pdf->Cell(0, 6, 'Are you currently under investigation by any government agency?', 0, 1);
	$pdf->getBoolean($_POST['courtOrder'], $pdf); $pdf->Cell(0, 6, 'Are you under any court orders or protective orders as a respondent?', 0, 1);
	$pdf->getBoolean($_POST['violence'], $pdf); $pdf->Cell(0, 6, 'Have you been convicted of any domestic violence charges?', 0, 1);
	$pdf->getBoolean($_POST['gangs'], $pdf); $pdf->Cell(0, 6, 'Are you currently a member or affiliated with any gangs or hate groups?', 0, 1);
	$pdf->getBoolean($_POST['drugs'], $pdf); $pdf->MultiCell(0, 6, 'Have you engaged in illegal use, possession, sale of narcotics or illicit drugs, during the last 5 years?');
	$pdf->getBoolean($_POST['dishonorable'], $pdf); $pdf->MultiCell(0, 6, 'Dishonorably discharged or forced to resign from any branch of the US government or country of origin?');
	$pdf->Ln(10);
	$pdf->Cell(0, 6, 'Yes explanations:', 0, 1);
	$pdf->SetFont('Times', 'U', 12);
	$pdf->MultiCell(0, 6, $_POST['yesExplanations']);
	$pdf->SetRightMargin(10);
	$pdf->SetLeftMargin(10);
	$pdf->SetY(160);
	$pdf->SetFont('Times', '', 11);
	$pdf->MultiCell(0, 4, 'I, '.$_POST['firstName'].' '.$_POST['lastName'].', would like to submit application of membership to become an active member of the Templar Knights Motorcycle Club. I hereby make application for membership. As a prospective member, I will respect my fellow club members as well as represent the club in a positive way. Whether I am with the group or alone, I will carry myself with the utmost respect being that I am an extension of this organization.');
	$pdf->Ln(3);
	$pdf->SetFont('Times', 'B', 11);
	$pdf->Cell(0, 4, 'Indemnification:', 0, 1);
	$pdf->SetFont('Times', '', 11);
	$pdf->MultiCell(0, 4, 'I agree to indemnify and hold harmless the Templar Knights LLC/Motorcycle Club, its members, agents and assigns, individually and/or collectively, from all lawsuits, claims, damages, costs and attorneys\' fees that arise out of my presence or conduct at an Event and/or my violation or my representative\'s violations of any provision of the Application. This provision will apply regardless of whether or not the lawsuit, claim, damages, costs and/or attorneys\' fees arise out of the negligence, in any form, of any of the Released Parties. As I am releasing any claim my family, guardian, representative and/or estate might wish to make by reason of my injury or death, this indemnity provision shall specifically apply to such actions on my behalf.');
	$pdf->SetFont('Times', 'B', 11);
	$pdf->Ln(3);
	$pdf->Cell(0, 4, 'Insurance Responsibility:', 0, 1);
	$pdf->SetFont('Times', '', 11); 
	$pdf->MultiCell(0, 4, 'I understand that the Templar Knights LLC/Motorcycle Club provides neither health nor life insurance. I assume all responsibility for my doctor and/or hospital expenses and any loss or injury to personal property or myself in which I may become involved in by reason of participating in any event or related activity.');
	$pdf->Ln(2);
	$pdf->getBoolean($_POST['rentalUnderstanding'], $pdf); $pdf->SetFont('Times', 'B', 11);
	$pdf->MultiCell(0, 4, 'I understand that Colors and/or patches are owned by Templar Knights LLC. Any and all fees paid are for the use and rental of said patches.');
	$pdf->Ln(2);
	$pdf->getBoolean($_POST['signature'], $pdf); $pdf->SetFont('Times', '', 11); $pdf->MultiCell(0, 4, 'By checking this box, I agree that this constitutes a legal digital signature and maintain that I have read the above application and am in agreement with all said provisions.');

	$pdf->Ln(1);
	$pdf->SetFont('Times', 'I', 10);
	$pdf->Cell(0, 4, 'Date of Digital Signature: '.$_POST['dateApplied'], 0, 1, 'R');
	$pdf->Cell(0, 4, 'IP Address: '.$_POST['ipAddress'], 0, 1, 'R');

	$pdf->Output('/home1/templark/tmp/applications/Application_'.$_POST['firstName'].'_'.$_POST['lastName'].'.pdf', 'F');
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