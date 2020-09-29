<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/form_tools.inc');

/**
 * Lodge DropDown
 */
function lodge_dropdown($db, $table, $allLodges = false) {
	if ($allLodges) {
		$query = 'SELECT * FROM ' . $table . ' ORDER BY LODGE_NAME';
	} else {
		$query = 'SELECT * FROM ' . $table . ' WHERE DATE_DISBANDED = 0000-00-00 ORDER BY LODGE_NAME';
	}
	echo 'Select a Lodge: ';
	echo '<select name="lodgeNum">';
	$result = mysqli_query($db->connection, $query);
	if($result) {
    	while($row = mysqli_fetch_array($result)){
      		echo '<option value="' . $row['LODGE_NUM'] . '">' . $row['LODGE_NAME'] . '</option>';  
    	}
  	}
	echo '</select>';
}

/**
 * Lodge DropDown Only
 */
function lodge_dropdown_only($db, $table, $allLodges = false) {
	$retval='';
	if ($allLodges) {
		$query = 'SELECT * FROM ' . $table . ' ORDER BY LODGE_NAME';
	} else {
		$query = 'SELECT * FROM ' . $table . ' WHERE DATE_DISBANDED = 0000-00-00 ORDER BY LODGE_NAME';
	}
	$retval='<select name="lodgeNum">';
	$result = mysqli_query($db->connection, $query);
	if($result) {
    	while($row = mysqli_fetch_array($result)){
      		$retval .= '<option value="' . $row['LODGE_NUM'] . '">' . $row['LODGE_NAME'] . '</option>';  
    	}
  	}
	$retval.='</select>';
	return $retval;
}

/**
 * Provisional DropDown
 */
function provisional_dropdown($db, $table, $lodgeNum = 0, $allProvisionals = false) {
	if ($allProvisionals) {
		if ($lodgeNum == 0) {
			$query = 'SELECT * FROM ' . $table . ' WHERE RANK = 3 ORDER BY LODGE, FNAME';
		} else {
			$query = 'SELECT * FROM ' . $table . ' WHERE LODGE = ' . $lodgeNum . ' AND RANK = 3 ORDER BY FNAME';
		}
	} else {
		if ($lodgeNum == 0) {
			$query = 'SELECT * FROM ' . $table . ' WHERE RANK = 3 AND DATE_WITHDREW = 0000-00-00 ORDER BY LODGE, FNAME';
		} else {
			$query = 'SELECT * FROM ' . $table . ' WHERE LODGE = ' . $lodgeNum . ' AND RANK = 3 AND DATE_WITHDREW = 0000-00-00 ORDER BY FNAME';
		}
	}
	echo 'Select a Provisional: ';
	echo '<select name="knightNum">';
	$result = mysqli_query($db->connection, $query);
	if($result) {
    	while($row = mysqli_fetch_array($result)){
      		echo '<option value="' . $row[RECORD_NUM] . '">' . $row[FNAME] . " '" . $row[NICKNAME] . "' " . $row[LNAME] . '</option>';  
    	}
  	}
	echo '</select>';
}

/**
 * Non-Patched Members, ie Provisionals & Recruiting Sergeants & Honorary DropDown
 */
function nonpatched_dropdown($db, $table, $lodgeNum = 0, $allNonPatched = false) {
	if ($allNonPatched) {
		if ($lodgeNum == 0) {
			$query = 'SELECT * FROM ' . $table . ' WHERE RANK = 3 OR RANK = 12 ORDER BY LODGE, FNAME';
		} else {
			$query = 'SELECT * FROM ' . $table . ' WHERE LODGE = ' . $lodgeNum . ' AND (RANK = 3 OR RANK = 12) ORDER BY FNAME';
		}
	} else {
		if ($lodgeNum == 0) {
			$query = 'SELECT * FROM ' . $table . ' WHERE (RANK = 3 OR RANK = 12) AND DATE_WITHDREW = 0000-00-00 ORDER BY LODGE, FNAME';
		} else {
			$query = 'SELECT * FROM ' . $table . ' WHERE LODGE = ' . $lodgeNum . ' AND (RANK = 3 OR RANK = 12) AND DATE_WITHDREW = 0000-00-00 ORDER BY FNAME';
		}
	}
	echo 'Select a Provisional:';
	echo '<select name="knightNum">';
	$result = mysqli_query($db->connection, $query);
	if($result) {
    	while($row = mysqli_fetch_array($result)){
      		echo '<option value="' . $row[RECORD_NUM] . '">' . $row[FNAME] . " '" . $row[NICKNAME] . "' " . $row[LNAME] . '</option>';  
    	}
  	}
	echo '</select>';
}

/**
 * Members DropDown
 */
function members_dropdown($db, $table, $fullName = false, $lodgeNum = 0, $title = 'Select a Member:', $allMembers = false, $sortBy = 'NICKNAME') {
	$lodgeArray = get_lodge_array($db);
	if ($fullName) {
		$sortBy = 'FNAME';
	}
	if ($allMembers) {
		if ($lodgeNum == 0) {
			$query = 'SELECT * FROM ' . $table . ' ORDER BY LODGE, '.$sortBy;
		} else {
			$query = 'SELECT * FROM ' . $table . ' WHERE LODGE = ' . $lodgeNum . ' ORDER BY '.$sortBy;
		}
	} else {
		if ($lodgeNum == 0) {
			$query = 'SELECT * FROM ' . $table . ' WHERE DATE_WITHDREW = 0000-00-00 ORDER BY LODGE, '.$sortBy;
		} else {
			$query = 'SELECT * FROM ' . $table . ' WHERE LODGE = ' . $lodgeNum . ' AND DATE_WITHDREW = 0000-00-00 ORDER BY '.$sortBy;
		}
	}
	echo $title;
	$display = '';
	echo '<select name="knightNum">';
	$result = mysqli_query($db->connection, $query);
	if($result) {
    	while($row = mysqli_fetch_array($result)){
      		if ($fullName || ($row[NICKNAME] == '')) {
				$display = '<option value="' . $row[RECORD_NUM] . '">' . $row[FNAME] . " '" . $row[NICKNAME] . "' " . $row[LNAME];
			} else {
				$display = '<option value="' . $row[RECORD_NUM] . '">' . $row[NICKNAME];
			}
			if ($lodgeNum ==0) {
				$display .= ' [' . $lodgeArray[$row[LODGE]] . ']</option>';
			} else {
				$display .= '</option>';
			}
			echo $display;
    	}
  	}
	echo '</select>';
}

/**
 * Rank DropDown
 */
function rank_dropdown($db, $table, $rank = 'Knight', $title = 'Rank:') {
	echo $title;
    echo '<select name="rank">';
    $rankquery = "SHOW COLUMNS FROM " . $table . " LIKE 'RANK'";
    $rankresult = mysqli_query($db->connection, $rankquery);
    if($rankresult) {
		$rankrow = mysqli_fetch_array($rankresult);
        #Extract the values
        #The values are enclosed in single quotes and seperated by commas
        $regex = "/'(.*?)'/";
        preg_match_all($regex, $rankrow[1], $enum_array);
        $i = 0;
        foreach($enum_array[1] as $value) {
			if($value == $rank) {
				echo '<option selected="yes" value="' . $i . '">' . $value . '</option>';
			} else {
				echo '<option value="' . $i . '">' . $value . '</option>';
			}
			$i++;
		}
	}
	echo '</select>';
}

/**
 * Get Rank
 */
function get_rank($db, $table, $rank = 'Knight') {
    $rankquery = "SHOW COLUMNS FROM " . $table . " LIKE 'RANK'";
    $rankresult = mysqli_query($db->connection, $rankquery);
    if($rankresult) {
		$rankrow = mysqli_fetch_array($rankresult);
        #Extract the values
        #The values are enclosed in single quotes and seperated by commas
        $regex = "/'(.*?)'/";
        preg_match_all($regex, $rankrow[1], $enum_array);
        $i = 0;
        foreach($enum_array[1] as $value) {
			if($value == $rank) {
				return $i;
			}
			$i++;
		}
		return 0;
	}
}

/**
 * State DropDown Only
 */
function state_dropdown_only($db, $table) {
	$retval='';
	
	$retval .= '<select name="state">';
	$query = 'SELECT * FROM ' . $table;
	$stateresult = mysqli_query($db->connection, $query);
	if($stateresult) {
		while($staterow = mysqli_fetch_array($stateresult)){
			if($staterow['STATE_NUM'] == 0) {
				$retval .= '<option selected="yes" value="' . $staterow['STATE_NUM'] . '">' . $staterow['STATE_NAME'] . '</option>';
			} else {
				$retval .= '<option value="' . $staterow['STATE_NUM'] . '">' . $staterow['STATE_NAME'] . '</option>';
			}
		}
	}
	$retval .= '</select>';
	return $retval;
}

/**
 * Country DropDown Only
 */
function country_dropdown_only($db, $table) {
	$retval='';
	
	$retval .= '<select name="country">';
	$query = 'SELECT * FROM ' . $table;
	$result = mysqli_query($db->connection, $query);
	if($result) {
		while($row = mysqli_fetch_array($result)){
			if($row['COUNTRY_NUM'] == 0) {
				$retval .= '<option selected="yes" value="' . $row['COUNTRY_NUM'] . '">' . $row['COUNTRY_NAME'] . '</option>';
			} else {
				$retval .= '<option value="' . $row['COUNTRY_NUM'] . '">' . $row['COUNTRY_NAME'] . '</option>';
			}
		}
	}
	$retval .= '</select>';
	return $retval;
}
