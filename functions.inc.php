<?php

function redirect($url) {
	header("Location: " . $url);
}

function cleanGet($get_var) {
	return (isset($_GET[$get_var]) && trim($_GET[$get_var]) != "") ? cleanInput($_GET[$get_var]) : '';
}

function cleanPost($post_var) {
	return (isset($_POST[$post_var]) && trim($_POST[$post_var]) != '') ? cleanInput($_POST[$post_var]) : '';
}

function cleanInput($input) {
	return mysql_real_escape_string(trim(htmlspecialchars(strip_tags($input), ENT_QUOTES, "UTF-8")));
}

function startsWith($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}

function endsWith($haystack, $needle) {
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

?>
