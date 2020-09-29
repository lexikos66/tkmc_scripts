<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');
	
//Connect To Database
$db = LocalDatabase::getWritable('fpr');
$promo_table='2015_reg_promo';
$canRegister=0;
$checkNum=0;
$pnNum=0;

echo 'We are thrilled to inform you that we have teamed up with Mi Ranchito in American Fork to provide you with a $5 discount on the registration price for the 2015 Fallen Patriot Ride.  In order to qualify for this discount, all you have to do is eat at the Mi Ranchito American Fork Restaurant between now and May 21st, 2015; post your initial receipt with your Chk#/PN (Not your Customer Copy of the Credit Card receipt) to Facebook tagging "miranchitogrillaf" and "templarknightsmc"; and then fill out the form below with your Chk#/PN number from your receipt!<br>';

if (isset($_POST["canRegister"])) {
	if($_POST["canRegister"] == 1) {
		//See if Value is in database
  		$query = 'SELECT * FROM ' . $promo_table . ' WHERE check_num = '.$_POST["chkNum"]. ' AND pn_num = '.$_POST["pnNum"] . ' LIMIT 1';
	}
	$result = mysql_query($query, $db->connection);
	if($result) {
  		if($row = mysql_fetch_array($result)) {
			if($row['is_used'] == "1") {
				$canRegister = 2;
			} else {
				$canRegister = 1;
				$query = 'UPDATE '.$promo_table.' SET is_used = 1 WHERE check_num = '.$_POST["chkNum"].' AND pn_num = '.$_POST["pnNum"];
				$result = mysql_query($query, $db->connection);
			}
		}
	} 			
		
	if($canRegister == 0) {
		echo '<br><strong>We are unable to verify Check Number '.$_POST["chkNum"].'/'.$_POST["pnNum"].'.  Check Numbers are updated nightly.  We are sorry for the inconvenience.  Please try again tomorrow.</strong><br>';
		display_form();
	}
	if($canRegister == 2) {
		echo '<br><strong>Check Number '.$_POST["chkNum"].'/'.$_POST["pnNum"].' has already been used.  Only one Promotional Discount is valid per check.  Please visit the <a href="http://fallenpatriotride.com/register/">regular registration page</a> or enter another check number.</strong><br>';
		display_form();
	}
	if($canRegister == 1) {
		echo '<p>Pre-register for the 6th Annual Fallen Patriot Ride to be held on Saturday, May 23, 2015 by completing the form below and receive a FREE Ride Pin.</p><p>The registration price for the Rider includes: Breakfast, Lunch, Ride Pin and an event T-Shirt (Please specify the size).</p><p>If you register your Passenger, they will receive: Breakfast, Lunch and a Ride Pin.</p><p>For an additional $5 (with your pre-registration), you can get your passenger a T-Shirt.  Choose “Rider/Pasenger w/Additional T-Shirt” and make sure you specify <strong>two</strong> sizes in the “T-Shirt” field.</p><p><em>*The Prices below include the paypal fee of .029% + .30 on each transaction</em></p><pre><code>[raw_html_snippet id="paypal_registration_promo"]</code></pre>';
	}
} else {
	display_form();
}

mysql_close($db->connection);

function display_form() {
  echo '<form action="http://fallenpatriotride.com/register/mi-ranchito" method="post">';
  echo 'Chk# \ PN';
  echo '<div id="check"><input type="text" name="chkNum"/> \ <input type="text" name="pnNum"/></div>';
  echo '<input type="hidden" name="canRegister" value="1"/>';
  echo '<input type="submit" value="Verify" id="verify-button" class="button"/>';
  echo '</form>';
} 

?>