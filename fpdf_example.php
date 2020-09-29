<?php
require('/home/luther/tools/fpdf/fpdf.php');

$_POST = array();
$_POST['firstName'] = 'Michael';
$_POST['lastName'] = 'Luther';
$_POST['age'] = '48';
$_POST['address'] = '351 N 525 E';
$_POST['city'] = 'Springville';
$_POST['state'] = 'Utah';
$_POST['zipCode'] = '84663';
$_POST['country'] = 'United States';
$_POST['homeTown'] = 'Pasadena';
$_POST['phoneNum'] = '801-319-5805';
$_POST['emailAddr'] = 'hollywood@templarknightsmc.com';
$_POST['validDL'] = '1';
$_POST['driversLicense'] = '12345678932';
$_POST['currentEndorsement'] = '0';
$_POST['mcMake'] = 'Kawasaki';
$_POST['mcModel'] = 'Vulcan Classic';
$_POST['motorcycleSize'] = '1';
$_POST['mcSize'] = '1500';
$_POST['mcYear'] = '2008';
$_POST['mcYearsRiding'] = '4';
$_POST['validInsurance'] = '0';
$_POST['insuranceCompany'] = 'Geico/Farmers Insurance';
$_POST['otherAffiliation'] = '1';
$_POST['membershipsHeld'] = 'Bandidos, Honda Riding Club';
$_POST['referred'] = '1';
$_POST['memberReferred'] = 'Hollywood';
$_POST['skills'] = 'I have quite a few skills, not to mention the ability to talk a lot and probably more than you want me to.';
$_POST['howHeard'] = 'I heard about you through numerous television and radio adds.  I was on a ride and saw a member there.  I was impressed and thought I\'d look into it.';
$_POST['felony'] = '1';
$_POST['criminal'] = '0';
$_POST['parole'] = '0';
$_POST['investigation'] = '0';
$_POST['courtOrder'] = '0';
$_POST['violence'] = '0';
$_POST['gangs'] = '0';
$_POST['drugs'] = '0';
$_POST['dishonorable'] = '0';
$_POST['yesExplanations'] = 'I was on parole, but it was only a felony and didn\'t count for much.  I was only there.  I did not attack that woman.';
$_POST['rentalUnderstanding'] = '1';
$_POST['signature'] = '1';
$_POST['dateApplied'] = '2014-09-03 10:12:34';
$_POST['ipAddress'] = '10.2.45.64';


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
$pdf->Cell(15, 6, 'State:', 0, 0); $pdf->Cell(50, 6, $_POST['state'], 'B', 0);
$pdf->Cell(10, 6, 'Zip:', 0, 0); $pdf->Cell(20, 6, $_POST['zipCode'], 'B', 1);
$pdf->Ln(3); 
$pdf->Cell(20, 6, 'Country:', 0, 0); $pdf->Cell(70, 6, $_POST['country'], 'B', 0);
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

$pdf->Output('Application_'.$_POST['firstName'].'_'.$_POST['lastName'].'.pdf', 'F');
?>
