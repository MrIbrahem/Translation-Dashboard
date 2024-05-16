<?php
$print_t = false;

if (isset($_REQUEST['test'])) {
    $print_t = true;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

define('print_te', $print_t);

include_once 'actions/html.php';
include_once 'actions/wiki_api.php';
include_once 'actions/mdwiki_api.php';
include_once 'actions/mdwiki_sql.php';


function load_request() {
    global $lang_to_code, $code_to_lang, $camp_to_cat, $cat_to_camp;
    //---
    $code = $_REQUEST['code'] ?? '';
    //---
    if ($code == 'undefined') $code = "";
    //---
    $code = $lang_to_code[$code] ?? $code;
    $code_lang_name = $code_to_lang[$code] ?? '';
    //---
    $cat  = $_REQUEST['cat'] ?? '';
    if ($cat == 'undefined') $cat = "";
    //---
    $camp = $_REQUEST['camp'] ?? '';
    //---
    if ($cat == "" && $camp != "") {
        $cat = $camp_to_cat[$camp] ?? $cat;
    }
    //---
    if ($cat != "" && $camp == "") {
        $camp = $cat_to_camp[$cat] ?? $camp;
    }
    // if ($cat == "") $cat = "RTT";
    //---
    return [
        'code' => $code,
        'cat' => $cat,
        'camp' => $camp,
        'code_lang_name' => $code_lang_name
    ];
}
//---
function escape_string($unescaped_string) {
    // Alternative mysql_real_escape_string without mysql connection
    $replacementMap = [
        "\0" => "\\0",
        "\n" => "",
        "\r" => "",
        "\t" => "",
        chr(26) => "\\Z",
        chr(8) => "\\b",
        '"' => '\"',
        "'" => "\'",
        '_' => "\_",
        "%" => "\%",
        '\\' => '\\\\'
    ];

    return \strtr($unescaped_string, $replacementMap);
}
function strstartswithn($text, $word) {
    return strpos($text, $word) === 0;
}

function strendswith($text, $end) {
    return substr($text, -strlen($end)) === $end;
}

function test_print($s) {
    if (print_te && gettype($s) == 'string') {
        echo "<br>$s";
    } elseif (print_te) {
        echo "<br>";
        print_r($s);
    }
}

$usrs = array_map('current', execute_query("SELECT user FROM coordinator;"));
?>
