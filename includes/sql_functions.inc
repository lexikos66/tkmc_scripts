<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}

/**
 * SQL Show Table Status
 */
function sql_get_table_update_time($db, $table) {
	$query = 'SHOW TABLE STATUS';
	$result = mysqli_query($db->connection, $query);
	if($result) {
    	while($row = mysqli_fetch_array($result)){
      		if ($row['Name'] == $table) {
				return $row['Update_time'];
			}
    	}
  	}
}

/**
 * SQL Get Application Versions
 */
 function sql_get_app_version($db, $app_name, $type) {
	$query = 'SELECT version FROM app_info WHERE name="'.$app_name.'" AND type="'.$type.'"';
	$result = mysqli_query($db->connection, $query);
	if($result) {
		$row = mysqli_fetch_array($result);
		return $row['version'];
	}
}

/**
 * SQL Set Application Versions
 */
 function sql_set_app_version($db, $app_name, $type, $version) {
	$query = 'UPDATE app_info SET version='.$version.' WHERE name="'.$app_name.'" AND type="'.$type.'"';
	$result = mysqli_query($db->connection, $query);
	return $result;
}