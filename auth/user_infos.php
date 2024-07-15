<?php
//---
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/helps.php';
//---
$secure = ($_SERVER['SERVER_NAME'] == "localhost") ? false : true;
if ($_SERVER['SERVER_NAME'] != 'localhost') {
	session_name("mdwikitoolforgeoauth");
	session_set_cookie_params(0, "/", $domain, $secure, $secure);
}
session_start();
//---
$username = get_from_cookie('username');
$username = str_replace("+", " ", $username);
//---
if ($username == '' && $_SERVER['SERVER_NAME'] == 'localhost') {
	$username = $_SESSION['username'] ?? '';
}
//---
function echo_login()
{
	global $username;
	$safeUsername = htmlspecialchars($username); // Escape characters to prevent XSS

	if ($username == '') {
		echo <<<HTML
			Go to this URL to authorize this tool:<br />
			<a href='auth.php?a=login'>Login</a><br />
		HTML;
	} else {
		echo <<<HTML
			You are authenticated as $safeUsername.<br />
			Continue to <a href='auth.php?a=edit'>edit</a><br>
			<a href='auth.php?a=logout'>logout</a>
		HTML;
	};
	//---
};