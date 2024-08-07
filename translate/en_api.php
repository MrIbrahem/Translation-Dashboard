<?php
namespace Translate\EnAPI;

/*
Usage:

use function Translate\EnAPI\getLoginToken;
use function Translate\EnAPI\loginRequest;
use function Translate\EnAPI\getCSRFToken;
use function Translate\EnAPI\send_params;
use function Translate\EnAPI\do_edit;
use function Translate\EnAPI\Find_pages_exists_or_not;

*/

/*
    edit.php

    MediaWiki API Demos
    Demo of `Edit` module: POST request to edit a page
    MIT license
*/

include_once(__DIR__ . '/../infos/user_account_new.php');
$my_username = $my_username;
$lgpass_enwiki = $lgpass_enwiki;
$usr_agent = $user_agent;

$endPoint = "https://simple.wikipedia.org/w/api.php";

// Step 1: GET request to fetch login token
function getLoginToken()
{
	global $endPoint, $usr_agent;

	$params1 = [
		"action" => "query",
		"meta" => "tokens",
		"type" => "login",
		"format" => "json"
	];

	$url = $endPoint . "?" . http_build_query($params1);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

	curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	$output = curl_exec($ch);
	if ($output === FALSE) {
		echo ("<br>cURL Error: " . curl_error($ch) . "<br>$url");
	}
	curl_close($ch);
	// ---
	// echo "<pre>";
	// echo htmlentities(var_export($output, true));
	// echo "</pre><br>";
	//---
	$result = json_decode($output, true);
	if (!is_array($result)) {
		$result = array();
	}
	return $result["query"]["tokens"]["logintoken"] ?? "";
}

// Step 2: POST request to log in. Use of main account for login is not
// supported. Obtain credentials via Special:BotPasswords
// (https://www.mediawiki.org/wiki/Special:BotPasswords) for lgname & lgpassword
function loginRequest($logintoken)
{
	global $endPoint, $usr_agent, $my_username, $lgpass_enwiki;

	$params2 = [
		"action" => "login",
		"lgname" => $my_username,
		"lgpassword" => $lgpass_enwiki,
		"lgtoken" => $logintoken,
		"format" => "json"
	];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $endPoint);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params2));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	$url = "{$endPoint}?" . http_build_query($params2);

	$output = curl_exec($ch);
	if ($output === FALSE) {
		echo ("<br>cURL Error: " . curl_error($ch) . "<br>$url");
	}
	curl_close($ch);
}

// Step 3: GET request to fetch CSRF token
function getCSRFToken()
{
	global $endPoint, $usr_agent;

	$params3 = [
		"action" => "query",
		"meta" => "tokens",
		"format" => "json"
	];

	$url = $endPoint . "?" . http_build_query($params3);

	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	$output = curl_exec($ch);
	if ($output === FALSE) {
		echo ("<br>cURL Error: " . curl_error($ch) . "<br>$url");
	}
	curl_close($ch);

	$result = json_decode($output, true);
	if (!is_array($result)) {
		$result = array();
	}
	return $result["query"]["tokens"]["csrftoken"] ?? "";
}

// Step 4: POST request to edit a page
function send_params($params4)
{
	global $endPoint, $usr_agent;

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $endPoint);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params4));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($ch, CURLOPT_USERAGENT, $usr_agent);

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);

	$output = curl_exec($ch);

	$url = "{$endPoint}?" . http_build_query($params4);

	if ($output === FALSE) {
		echo ("<br>cURL Error: " . curl_error($ch) . "<br>$url");
	}
	curl_close($ch);

	return $output;
}

function do_edit($title, $text, $summary)
{

	$login_Token = getLoginToken(); // Step 1
	// ---
	if ($login_Token == "") {
		echo "<br>Login Token not found<br>";
		return false;
	}
	// ---
	loginRequest($login_Token); // Step 2
	$csrf_Token = getCSRFToken(); // Step 3
	// ---
	if ($csrf_Token == "") {
		echo "<br>CSRF Token not found<br>";
		return false;
	}
	// ---
	$params4 = [
		"action" => "edit",
		"title" => $title,
		"text" => $text,
		"token" => $csrf_Token,
		"summary" => $summary,
		"format" => "json"
	];

	$result = send_params($params4);

	$result = json_decode($result, true);
	//---
	// echo "<pre>"; print_r($result); echo "</pre>";
	//---
	return $result;
}

function Find_pages_exists_or_not($title)
{
	// {"action": "query", "titles": title, "rvslots": "*"}
	$params = [
		"action" => "query",
		"titles" => $title,
		'format' => 'json',
		"formatversion" => 2
	];

	$result = send_params($params);

	$result = json_decode($result, true);
	$result = $result['query']['pages'] ?? [];

	if (count($result) > 0) {
		$page = $result[0];
		$pageid = ($page['pageid']) ? 'true' : 'false';
		return $pageid;
	}

	return false;
}
