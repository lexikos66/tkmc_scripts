<?php
if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
//Connect To Database
$db = LocalDatabase::getReadable('master');
$ltable='lodge_directory';
$ctable='fin_charity_events';
$stable='fin_sponsors';

if (isset($_GET['success'])) {
	echo '<p>Event was successfully added.</p>';
}

echo '<div><form action="http://templarknightsmc.com/scripts/addcharityevent_action.php" method="post"><table>';
echo '<tr><td>Event Name:</td><td><input type="text" name="eventName" /></tr>';
echo '<tr><td>Event Year:</td><td><select name="year">';
$todaysDate = getDate();
echo '<option value="' . ($todaysDate['year'] - 2) . '">' . ($todaysDate['year'] - 2) . '</option>';
echo '<option value="' . ($todaysDate['year'] - 1) . '">' . ($todaysDate['year'] - 1) . '</option>';
echo '<option selected="yes" value="' . $todaysDate['year'] . '">' . $todaysDate['year'] . '</option>';
echo '</select></td></tr>';
echo '<tr><td>Amount (no commas, just numbers):</td><td><input type="text" name="amount" /></td></tr>';
echo '<tr><td>Sponsor:</td><td><input type="text" name="sponsor">';
echo '<tr><td>Reason:</td><td><input type="text" name="reason" /></td></tr>';
echo '<tr><td>Sponsoring Lodge:</td><td>'.lodge_dropdown_only($db, $ltable).'</td></tr></table>';
echo '<input type="submit" value="Record Event"></form></div><br />';
mysql_close($db->connection);

$db = LocalDatabase::getReadable('finance');
$query = 'SELECT * FROM ' . $ctable . ' ORDER BY YEAR DESC';
$result = mysql_query($query);
echo '<div style="height:709px;width:900px;overflow-x:hidden;overflow-y:scroll;"><table><tr><th>Charitable Event</th><th>Year</th><th>Amount Raised</th><th>Sponsor</th><th>Reason</th></tr>';

$moneyFormat = '%=_#12n';
setlocale(LC_MONETARY, 'en_US');

if($result) {
while($row = mysql_fetch_array($result)){
	echo '<tr><td>' . $row['EVENT'] . '</td><td>' . $row['YEAR'] . '</td><td align="right">' . money_format($moneyFormat, $row['AMOUNT_RAISED']) . '</td><td>' . $row['SPONSOR'] . '</td><td>' . $row['REASON'] . '</td><td></tr>';
}
}
echo '</table></div>';
mysql_close($db->connection);
?>
