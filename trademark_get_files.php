<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
define('__ROOT__', dirname(dirname(__FILE__)));
require_once(SCRIPTS_DIR.'/plugins/PHPMailer/PHPMailerAutoload.php');

date_default_timezone_set("America/Denver");
$tmDir = __ROOT__.'/data/tm-files/';

// Search Terms
$searchArray = array("decker", "templar", "knight", "interests of motorcyclists", "motorcycle club", " mc ", "cross", "shield", "wings");

// Get todays date for filename reference
$todaysDate = date('Ymd');

// Define file names
$ogXmlFile = 'TMOGIssue_'.$todaysDate.'_entire.xml';
$ogXmlFile = 'TMOGIssue_20141118_entire.xml';

//Get Weekly Trademark Gazette, Registration Certificates and Updated Registration Certificates
getXml($ogXmlFile, $tmDir);

// Search for content
$foundTerms = searchTxt($searchArray, $tmDir.$ogXmlFile);

mail_it(convertArray($foundTerms), 'http://www.uspto.gov/doc/'.$ogXmlFile);

function getXml($fileName, $filePath) {
	// Get PDF File from USPTO
	$remote_file = fopen('http://cdn.uspto.gov/doc/'.$fileName, 'r');
	if($remote_file) {
		$local_file = fopen($filePath.$fileName,'w');
		while(! feof($remote_file)) {
			$line = fgets($remote_file);
			fwrite($local_file, $line);
		}
		fclose($local_file);
	}
	fclose($remote_file);

}

function searchTxt($searchArray, $xmlFile) {
// There's gotta be a better way to do this . . . grab the full XML for a single Trademark somehow?
	$retVal = array();
	$prev_contents = '';
	$local_file = fopen($xmlFile, 'r');
	while (!feof($local_file)) {
		$contents = fread($local_file, 1024);
		foreach ($searchArray as $term) {
			$foundTerm = stripos($contents, $term);
			$contentsSub = $contents;
			while($foundTerm) {
				// Get substring from start of $contentsSub to the $term
				$termString = substr($contentsSub, 0, $foundTerm);
				$snStart = strripos($termString, '<com:ApplicationNumberText>');
				$snEnd = strpos($termString, '</com:ApplicationNumberText>', $snStart);
				$serialNum = substr($termString, $snStart + 27, ($snEnd - ($snStart + 27));
				if($snStart) {
					$retVal[$term][] = $serialNum;
				}
				$contentsSub = substr($contentsSub, $foundTerm + strlen($term));
				$foundTerm = stripos($contentsSub, $term);
			}
		}
	}
	return $retVal;
}

function convertArray($snArray) {
	$messageBody = '';
	foreach($snArray as $key => $value) {
		$messageBody .= '<p>'.$key.' = ';
		foreach ($value as $serial) {
			$messageBody .= $serial.', ';
		}
		$messageBody = substr($messageBody, 0, strlen($messageBody) - 2).'<p>';
	}
	return $messageBody;
}
			

function mail_it($messageBody, $ogUrl) {
	$email = new PHPMailer();
	$email->From = 'contact@templarknightsmc.com';
	$email->FromName = 'TKMC Administrator';
	$email->Subject = 'Trademark Official Gazette Summary';
	$email->Body = '
		<html>
		<head>
			<title>Trademark Official Gazette Summary</title>
		</head>
		<body>
			<p>Here is a summary of a search through the Trademark Official Gazette. Each search term is listed below.  The serial number listed is not necessarily, and probably isn\'t the serial number in question, but the term will be around that serial number.  If there is a serial number without a dash - then search for the last 6 digits (including the comma).  The link to the Gazette is: '.$ogUrl.'</p>'.$messageBody.'
		</body>
		</html>';
	$email->AddAddress('contact@templarknightsmc.com');
	$email->isHTML(true);
	$email->Send();
}	

?>
