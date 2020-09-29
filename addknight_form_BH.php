<?php
	if (!defined('SCRIPTS_DIR')) {
		define('SCRIPTS_DIR', '/home1/templark/scripts');
	}
	require_once(SCRIPTS_DIR.'/includes/defines.inc');
	require_once(SCRIPTS_DIR.'/classes/Database.class');

	//Connect To Database
	$db = LocalDatabase::getReadable('master');
	$ktable='knight_directory';
	$stable='state_directory';
	$ctable='country_directory';
	$lodgeArray = "";
	$ltable='lodge_directory';

	if(isset($_GET["success"])) {
		$query = 'SELECT * FROM ' . $ktable . ' ORDER BY RECORD_NUM DESC LIMIT 1';
		$result = mysqli_query($db->connection, $query);
		if($result) {
			$row = mysqli_fetch_array($result);
			echo '<p>You successfully added ' . $row['FNAME'] . ' ' . $row['LNAME'] . '</p>';
		}
	}
		echo '<form action="http://templarknightsmc.com/scripts/addknight_action.php" method="post">';
		echo '<input type="hidden" name="tkmcNum" /><p />';
		echo 'First Name: <input type="text" name="firstName" /><p />';
		echo 'Last Name: <input type="text" name="lastName" /><p />';
		echo 'Birth Date (yyyy-mm-dd): <input type="text" name="birthDate" /><p />';
		echo 'Nickname: <input type="text" name="nickName" /><p />';
		echo 'Rank:<select name="rank">';
		$query = "SHOW COLUMNS FROM " . $ktable . " LIKE 'RANK'";
		$result = mysqli_query($db->connection, $query);
		if($result) {
			$row = mysqli_fetch_array($result);
			#Extract the values
			#The values are enclosed in single quotes and seperated by commas
			$regex = "/'(.*?)'/";
			preg_match_all($regex, $row[1], $enum_array);
			$i = 0;
			foreach($enum_array[1] as $value) {
				if($i == 2) {
					echo '<option selected="yes" value="' . $i . '">' . $value . '</option>';
				} else {
					echo '<option value="' . $i . '">' . $value . '</option>';
				}
				$i++;
			}
		}
		echo '</select><p />';
		echo 'Street Address: <input type="text" name="streetAddress" /><p />';
		echo 'City: <input type="text" name="city" /><p />';
		echo 'State:<select name="state"><p />';
		$query = 'SELECT * FROM ' . $stable;
		$stateresult = mysqli_query($db->connection, $query);
		if($stateresult) {
			while($staterow = mysqli_fetch_array($stateresult)){
				if($staterow['STATE_NUM'] == $row['STATE']) {
					echo '<option selected="yes" value="' . $staterow['STATE_NUM'] . '">' . $staterow['STATE_NAME'] . '</option>';
				} else {
					echo '<option value="' . $staterow[STATE_NUM] . '">' . $staterow[STATE_NAME] . '</option>';
				}
			}
		}
		echo '</select><p />';
		echo 'Zip Code: <input type="text" name="zipCode" /><p />';
		echo 'Country:<select name="country"><p />';
		$query = 'SELECT * FROM ' . $ctable;
		$countryresult = mysqli_query($db->connection, $query);
		if($countryresult) {
			while($countryrow = mysqli_fetch_array($countryresult)){
				if($countryrow['COUNTRY_NUM'] == $row['COUNTRY']) {
					echo '<option selected="yes" value="' . $countryrow['COUNTRY_NUM'] . '">' . $countryrow['COUNTRY_NAME'] . '</option>';
				} else {
					echo '<option value="' . $countryrow['COUNTRY_NUM'] . '">' . $countryrow['COUNTRY_NAME'] . '</option>';
				}
			}
		}
		echo '</select><p />';
		echo 'Phone (no dashes): <input type="text" name="phoneNum" /><p />';
		echo 'Email Address: <input type="text" name="emailAddr" /><p />';
		echo 'Lodge:<select name="lodgeNum">';
		$query = 'SELECT * FROM ' . $ltable;
		$result = mysqli_query($db->connection, $query);
		if($result) {
			while($row = mysqli_fetch_array($result)){
				$lodgeArray[$row['LODGE_NUM']] = $row['LODGE_NAME'];
				if($row['LODGE_NUM'] == 1) {
					echo '<option selected="yes" value="' . $row['LODGE_NUM'] . '">' . $row['LODGE_NAME'] . '</option>';
				} else {
					echo '<option value="' . $row['LODGE_NUM'] . '">' . $row['LODGE_NAME'] . '</option>';  
				}
			}
		}
		echo '</select><p />';
		echo 'Referred by (use nickname if possible): <input type="text" name="referredBy" /><p />';
		echo '<p>Mentor:<select name="mentor">';
		$query = 'SELECT * FROM ' . $ktable . ' ORDER BY LODGE, NICKNAME';
		$result = mysqli_query($db->connection, $query);
		if($result) {
			echo '<option value="BLANK"> ---Select--- </option>';
			while($row = mysqli_fetch_array($result)) {
				if($row['DATE_WITHDREW'] == '0000-00-00' && $row['RANK'] != "Provisional" && $row['RANK'] != "Honorary") {
					echo '<option value="' . $row['RECORD_NUM'] . '">' . $row['FNAME'] . ' "' . $row['NICKNAME'] . '" ' . $row['LNAME'] . ' [' . $lodgeArray[$row['LODGE']] . ']</option>';
				}
			}
		}
		echo '</select><p />';
		echo '<p />';
		echo 'Date Joined (yyyy-mm-dd): <input type="text" name="dateJoined" /><p />';
		echo '<input type="hidden" name="dateApproved" /><p />';
		echo '<input type="hidden" name="dateKnighted" /><p />';
		echo '<input type="hidden" name="dateWithdrew" /><p />';
		echo '<p>Notes:<br>';
		echo '<textarea name="notes" rows="10" cols="40"></textarea></p>';
		echo '<p><input type="submit" value="Create Record"></p>';
		echo '</form>';
	mysqli_close($db->connection);
?>
