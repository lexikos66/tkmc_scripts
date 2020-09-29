<?php
   if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
	}

	require_once(SCRIPTS_DIR.'/includes/defines.inc');
	require_once(SCRIPTS_DIR.'/classes/Database.class');
	require_once(SCRIPTS_DIR.'/includes/form_functions.inc');
	
	//Connect To Database
	$db = LocalDatabase::getReadable('master');
	$ltable='lodge_directory';
	
	if(isset($_GET["success"])) {
		echo 'You successfully updated the record.<br>';
	}

	if(isset($_GET["lodgeNum"])) {
		$query = 'SELECT * FROM ' . $ltable . ' WHERE LODGE_NUM = ' . $_GET['lodgeNum'];
		$result = mysqli_query($db->connection, $query);
		if($result) {
			$row = mysqli_fetch_array($result);
			echo '<p style="text-align:center;font-size:20px">You are modifying information for: '.$row['LODGE_NAME'].'</p>';
			echo 'To update another Lodge, select it from the list below:<br><br>';
			echo '<form action="http://member.templarknightsmc.com/lodge/update-lodge" method="get">';
			lodge_dropdown($db, $ltable, true);
			echo '</select></p><input type="submit" value="Find Lodge!"></p>';
			echo '</form>';
			echo '<p><hr></p>';
			echo '<form action="http://templarknightsmc.com/scripts/updatelodge_action.php" method="post">';
			echo 'Lodge Code (Lowercase name of the Lodge with an underscore for a space): <input type="text" name="lodgeCode" value="' . $row['LODGE_CODE'] . '" /><p>';
			echo 'Lodge Name: <input type="text" name="lodgeName" value="' . $row['LODGE_NAME'] . '" /><p>';
			echo 'City: <input type="text" name="city" value="' . $row['CITY'] . '" /><p>';
			echo 'State (No Abbreviation): <input type="text" name="state" value="' . $row['STATE'] . '" /><p>';
			echo 'Country: <input type="text" name="country" value="' . $row['COUNTRY'] . '" /><p>';
			echo 'Lodge eMail Address: <input type="text" name="emailAddr" value="' . $row['EMAIL_ADDR'] . '" size="50" /><p>';
			echo 'Lodge Website URL: <input type="text" name="webAddr" value="' . $row['WEB_ADDR'] . '" size="50" /><p>';
			echo 'Lodge Facebook URL: <input type="text" name="fbAddr" value="' . $row['FB_ADDR'] . '" size="50" /><p>';
			echo 'Lodge Twitter URL: <input type="text" name="twitterAddr" value="' . $row['TWITTER_ADDR'] . '" size="50" /><p>';
			echo 'Lodge YouTube URL: <input type="text" name="youtubeAddr" value="' . $row['YOUTUBE_ADDR'] . '" size="50" /><p>';
			echo '<p>Dues Month:';
			echo '<select name="duesMonth">';
			$rankquery = "SHOW COLUMNS FROM " . $ltable . " LIKE 'DUES_MONTH'";
			$rankresult = mysqli_query($db->connection, $rankquery);
			if($rankresult) {
				$rankrow = mysqli_fetch_array($rankresult);
				#Extract the values
				#The values are enclosed in single quotes and seperated by commas
				$regex = "/'(.*?)'/";
				preg_match_all($regex, $rankrow[1], $enum_array);
				$i = 0;
				echo '<option selected="yes" value="12">- Selected -</option>';
				foreach($enum_array[1] as $value) {
					if($value == $row['DUES_MONTH']) {
						echo '<option selected="yes" value="' . $i . '">' . $value . '</option>';
					} else {
						echo '<option value="' . $i . '">' . $value . '</option>';
					}
					$i++;
				}
			}
			echo '</select>';
			echo '</p>';
			echo 'Creation Date [Date Lodge is Officially Formed] (yyyy-mm-dd): <input type="text" name="dateCreation" value="' . $row['DATE_CREATION'] . '" /><p>';
			echo 'Disbanded Date (yyyy-mm-dd): <input type="text" name="dateDisbanded" value="' . $row['DATE_DISBANDED'] . '" /><p>';
			echo '<input type="hidden" name="lodgeNum" value="' . $row['LODGE_NUM'] . '" /><br>';
			echo '<p><input type="submit" value="Update Record"></p>';
			echo '</form>';
		}
	} else {
		echo '<form action="http://member.templarknightsmc.com/lodge/update-lodge" method="get">';
		lodge_dropdown($db, $ltable, true);
		echo '</select></p><input type="submit" value="Find Lodge!"></p>';
		echo '</form>';
	}
	mysqli_close($db->connection);
?> 

