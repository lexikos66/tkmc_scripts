<?php
$current_user = wp_get_current_user();
echo '<p>Hey '. $current_user->display_name . '!  Welcome to the Templar Knights Motorcycle Club Member Area. If there is something you don\'t have access to, if something is broken or if you have an idea for something you\'d like to see here, please reach out to <a href="mailto:hollywood@templarknightsmc.com?subject:Member%20Area%20Request">Hollywood</a>.<br><br>ENJOY the site!';
?>