<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
require_once(SCRIPTS_DIR.'/includes/form_functions.inc');

//Connect to Database
$db = LocalDatabase::getReadable('master');
$qtable='quotes';
$ktable='knight_directory';
$lodgeNum=1;
$lodge='';

echo 'The Knights Templar of old were not only courageous, but they also were considered sages as knowledge of their wisdom and intellect spread across the lands.  Today, the Templar Knights are no different.  Spread the wisdom of the TKMC by sharing the tidbits of wisdom that you hear (these are meant to be funny):';

if(isset($_GET["success"])) {
	$query = 'SELECT * FROM ' . $qtable . ' ORDER BY RECORD_NUM DESC LIMIT 1';
    $result = mysqli_query($db->connection, $query);
    if($result) {
		$row = mysqli_fetch_array($result);
		echo '<table><tr><td><font size="4">You successfully added the quote "' . $row['quote'] . '"</font></td></tr></table>';
    }
} else {
	echo '<form action="http://templarknightsmc.com/scripts/templarwisdom_action.php" method="post">
	Quote:<br><textarea name="quote" rows="2" cols="60"></textarea><p><p>';
	members_dropdown($db, $ktable, false, 0, 'Who said it:');
	echo '<br><p><input type="submit" value="Quote it!"></p>';
	echo '</form>';
}
mysqli_close($db->connection);

?>