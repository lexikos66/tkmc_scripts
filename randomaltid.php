<?php
$length = 13;
$vowels = 'AEIOUY1234567890';
$consonants = 'BCDFGHJKLMNPQRSTVWXZ';
$altId = '';
$alt = time() % 2;
for ($i = 0; $i < $length; $i++) {
	if ($alt == 1) {
		$altId .= $consonants[(rand() % strlen($consonants))];
		$alt = 0;
	}
	else {
		$altId .= $vowels[(rand() % strlen($vowels))];
		$alt = 1;
	}
}
echo $altId;

?>

