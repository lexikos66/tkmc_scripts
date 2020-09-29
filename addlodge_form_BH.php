<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
//Connect To Database
$db = LocalDatabase::getReadable('master');
$ltable='lodge_directory';

if(isset($_GET["success"])) {
    $query = 'SELECT * FROM ' . $table . ' ORDER BY LODGE_NUM DESC LIMIT 1';
    $result = mysqli_query($db->connection, $query);
    if($result) {
      $row = mysqli_fetch_array($result);
      echo 'You successfully added <strong>' . $row['LODGE_NAME'] . '</strong> Lodge.';
    }
} else {
	echo '<form action="http://templarknightsmc.com/scripts/addlodge_action.php" method="post">';
	echo '<p>Lodge Code (Lowercase name of the Lodge with an underscore for a space): <input type="text" name="lodgeCode" /></p>';
	echo 'Lodge Name: <input type="text" name="lodgeName" /><p>';
	echo 'City: <input type="text" name="city" /><p>';
	echo 'State (No Abbreviation): <input type="text" name="state" /><p>';
	echo 'Country: <input type="text" name="country" /><p>';
	echo 'Lodge eMail Address: <input type="text" name="emailAddr" value="@templarknightsmc.com" size="50" /><p>';
	echo 'Lodge Website URL: <input type="text" name="webAddr" value="http://templarknightsmc.com" size="50" /><p>';
	echo 'Lodge Facebook URL: <input type="text" name="fbAddr" value="https://www.facebook.com/templarknightsmc" size="50" /><p>';
	echo 'Lodge Twitter URL: <input type="text" name="twitterAddr" value="http://twitter.com/TemplarKnightMC" size="50" /><p>';
	echo 'Lodge YouTube URL: <input type="text" name="youtubeAddr" value="http://youtube.com/user/TemplarKnightsMc" size="50" /><p>';
	echo '<p>Dues Month:<br><select name="duesMonth">';
	$query = "SHOW COLUMNS FROM " . $ltable . " LIKE 'DUES_MONTH'";
	$result = mysqli_query($db->connection, $query);
	if($result) {
		$row = mysqli_fetch_array($result);
		#Extract the values
		#The values are enclosed in single quotes and seperated by commas
		$regex = "/'(.*?)'/";
		preg_match_all($regex, $row[1], $enum_array);
		$i = 0;
		foreach($enum_array[1] as $value) {
			echo '<option value="' . $i . '">' . $value . '</option>';
			$i++;
		}
		echo '<option selected="yes" value="12">- Select -</option>';
	}
	echo '</select></p>';
	echo '<p><input type="submit" value="Create Record"></p>';
	echo '</form>';
 }
mysqli_close($db->connection);

 ?>
