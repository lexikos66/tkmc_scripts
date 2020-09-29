The prices listed below, take into account the charges collected by PayPal (2.9% + $ .30 USD). If you would like to avoid the convenience of PayPal, please contact Reaper of the Grand Lodge to coordinate payment.
<p><hr><p>If you are a Provisional Knight and need to pay the ENTRANCE FEE or any other fees for the Knighting Ceremony, use this area.
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<div style="display:none">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="8U24V89KM6K6N">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" value="Knighting Fees">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="cn" value="Add special instructions to the seller:">
<input type="hidden" name="no_shipping" value="2">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_paynowCC_LG.gif:NonHosted">
</div>
<table>
<tr><td><input type="hidden" name="on0" value="Knighting Fees">Entrance Fee/Knighting Fees</td></tr><tr><td><select name="os0">
	<option value="Colors/Nickname">Colors/Nickname $109.00 USD</option>
	<option value="Entrance Fee">Entrance Fee $78.00 USD</option>
	<option value="Nickname Patch">Nickname Patch $6.00 USD</option>
</select> </td></tr>
<tr><td><input type="hidden" name="on1" value="Lodge">Lodge</td></tr><tr><td><select name="os1">
<?php
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect to Database
$db = LocalDatabase::getReadable('master');  
$usertable='lodge_directory';
$query = 'SELECT * FROM ' . $usertable . ' ORDER BY LODGE_NAME';
$result = mysql_query($query);
if($result) {
	while($row = mysql_fetch_array($result)){
		if($row['DATE_DISBANDED'] == "0000-00-00") {
			echo '<option value="' . $row['LODGE_NAME'] . '">' . $row['LODGE_NAME'] . '</option>';  
		}
    }
}
mysql_close($db->connection);
?>
</select> </td></tr>
<tr><td><input type="hidden" name="on2" value="Name">Name</td></tr><tr><td><input type="text" name="os2" maxlength="200"></td></tr>
<tr><td><input type="hidden" name="on3" value="Nickname">Nickname</td></tr><tr><td><input type="text" name="os3" maxlength="200"></td></tr>
</table>
<div style="display:none">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="option_select0" value="Colors/Nickname">
<input type="hidden" name="option_amount0" value="109.00">
<input type="hidden" name="option_select1" value="Entrance Fee">
<input type="hidden" name="option_amount1" value="78.00">
<input type="hidden" name="option_select2" value="Nickname Patch">
<input type="hidden" name="option_amount2" value="6.00">
<input type="hidden" name="option_index" value="0">
</div>
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<p><hr><p>If you are a member and need to pay your annual fees, use this area.
<em>*Annual Fees are $75. Thirty percent (30%) stays in the local lodge; Seventy percent (70%) is paid to the Grand Council</em>.
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<div style="display:none">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="8U24V89KM6K6N">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" value="Annual Member Fees">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="cn" value="Add special instructions to the seller:">
<input type="hidden" name="no_shipping" value="2">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted">
</div>
<table>
<tr><td><input type="hidden" name="on0" value="Member Fees">Member Fees</td></tr><tr><td><select name="os0">
	<option value="Annual Fee - Grand Lodge">Annual Fee - Grand Lodge $78.00 USD</option>
	<option value="Annual Fee - Expansion Lodge">Annual Fee - Expansion Lodge $55.00 USD</option>
</select> </td></tr>
<tr><td><input type="hidden" name="on1" value="Lodge">Lodge</td></tr><tr><td><select name="os1">
<?php
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect to Database
$db = LocalDatabase::getReadable('master');
$usertable='lodge_directory';
$query = 'SELECT * FROM ' . $usertable . ' ORDER BY LODGE_NAME';
$result = mysql_query($query);
if($result) {
	while($row = mysql_fetch_array($result)){
		if($row['DATE_DISBANDED'] == "0000-00-00") {
			echo '<option value="' . $row['LODGE_NAME'] . '">' . $row['LODGE_NAME'] . '</option>';  
		}
    }
}
mysql_close($db->connection);
?>
</select> </td></tr>
<tr><td><input type="hidden" name="on2" value="Name">Name</td></tr><tr><td><input type="text" name="os2" maxlength="200"></td></tr>
</table>
<div style="display:none">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="option_select0" value="Annual Fee - Grand Lodge">
<input type="hidden" name="option_amount0" value="78.00">
<input type="hidden" name="option_select1" value="Annual Fee - Expansion Lodge">
<input type="hidden" name="option_amount1" value="55.00">
<input type="hidden" name="option_index" value="0">
</div>
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

<p><hr><p>If you need to pay an optional amount due to when you were Knighted, use this area.  You will be prompted to enter an amount when you start the transaction.
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<div style="display:none">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="8U24V89KM6K6N">
<input type="hidden" name="lc" value="US">
<input type="hidden" name="item_name" value="Optional Payment">
<input type="hidden" name="button_subtype" value="services">
<input type="hidden" name="no_note" value="0">
<input type="hidden" name="cn" value="Add special instructions to the seller:">
<input type="hidden" name="no_shipping" value="2">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="bn" value="PP-BuyNowBF:btn_paynow_LG.gif:NonHosted">
</div>
<table>
<tr><td><input type="hidden" name="on0" value="Lodge">Lodge</td></tr><tr><td><select name="os0">
<?php
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');

//Connect to Database
$db = LocalDatabase::getReadable('master');
$usertable='lodge_directory';
$query = 'SELECT * FROM ' . $usertable . ' ORDER BY LODGE_NAME';
$result = mysql_query($query);
if($result) {
	while($row = mysql_fetch_array($result)){
		if($row['DATE_DISBANDED'] == "0000-00-00") {
			echo '<option value="' . $row['LODGE_NAME'] . '">' . $row['LODGE_NAME'] . '</option>';  
		}
    }
}
mysql_close($db->connection);
?>
</select> </td></tr>
<tr><td><input type="hidden" name="on1" value="Name">Name</td></tr><tr><td><input type="text" name="os1" maxlength="200"></td></tr>
</table>
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynow_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
