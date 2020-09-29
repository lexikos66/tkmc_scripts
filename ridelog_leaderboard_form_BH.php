<?php

if (!defined('SCRIPTS_DIR')) {
	define('SCRIPTS_DIR', '/home1/templark/scripts');
}
require_once(SCRIPTS_DIR.'/includes/defines.inc');
require_once(SCRIPTS_DIR.'/classes/Database.class');
	
//Connect To Database
$db = LocalDatabase::getReadable('master');
$lodgetable='lodge_directory';
$ktable='knight_directory';
$rtable='ride_directory';
$ctable='ride_corollary';
$miles_array='';
$lodgeName='';
$lodgeNum ='';
$mileage = 0;
$array_num = 0;
$query = '';

$current_user = wp_get_current_user();
$roles = implode('","',$current_user->roles);
$query = 'SELECT * FROM ' . $lodgetable . ' WHERE LODGE_CODE in ("'. $roles .'")';
$result = mysqli_query($db->connection, $query);
if($result) {
    while($row = mysqli_fetch_array($result)){
        $lodgeName = $row['LODGE_NAME'];
        $lodgeNum = $row['LODGE_NUM'];
    }
}

$query = 'SELECT * FROM ' . $ktable . ' WHERE LODGE = '.$lodgeNum.' && DATE_WITHDREW=0000-00-00';

$result = mysqli_query($db->connection, $query);

if($result) {
	while($row = mysqli_fetch_array($result)) {
  		$query = 'SELECT k.NICKNAME, k.FNAME, m.MILES FROM knight_directory AS k INNER JOIN ride_corollary AS c ON k.RECORD_NUM=c.KNIGHT_NUM JOIN ride_directory AS m on c.RIDE_NUM=m.RIDE_NUM WHERE k.RECORD_NUM = ' . $row['RECORD_NUM'];
		$inner_result = mysqli_query($db->connection, $query);
  		if ($inner_result) {
  			$mileage = 0;
			while($inner_row = mysqli_fetch_array($inner_result)){
    			$mileage = $mileage + $inner_row['MILES'];
  			}
		}
		if ($row['NICKNAME'] == "") {
			$miles_array[$row['FNAME'].'_'.$row['RECORD_NUM']] = $mileage;
		} else {
			$miles_array[$row['NICKNAME'].'_'.$row['RECORD_NUM']] = $mileage;
		}
		$array_num += 1;
	}
}
print_leaders($miles_array, $lodgeName);
mysqli_close($db->connection);

function print_leaders($array, $lodgeName) {
$i = 0;
echo '<table><tr><th colspan=2 style="text-align: center">'.$lodgeName.'</th></tr>';
arsort($array);
foreach ($array as $key => $value) {	
		if ($i < 10) {
                     echo '<tr><td style="text-align: center">' . substr($key, 0, strpos($key, '_')) . '</td><td> ' . $value .   '</td></tr>';
                     $i++;
                } else { break; }
	}
echo '</table>';
}	
?>