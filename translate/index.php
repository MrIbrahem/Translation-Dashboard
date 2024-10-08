<?php
// Define root path
require_once __DIR__ . '/../actions/curl_api.php';
include_once __DIR__ . '/inserter.php';

use function Actions\CurlApi\post_url_params_result;
use function Actions\Html\login_card;
use function Actions\Html\make_input_group;
use function Actions\Html\make_mdwiki_title;
use function Actions\Html\make_translation_url;
use function Actions\Html\make_target_url;
use function Actions\Functions\test_print;
use function Translate\Translator\startTranslatePhp;
use function Translate\Inserter\insertPage;

$pathParts = explode('public_html', __FILE__);
// the root path is the first part of the split file path
$ROOT_PATH = $pathParts[0];

$fix_ref_in_text = $settings['fix_ref_in_text']['value'] ?? '0';
$fix_ref_in_text = ($fix_ref_in_text == "1") ? true : false;

// Get parameters from the URL
$coden = strtolower($_GET['code']);
$title_o = $_GET['title'] ?? "";
// $useree  = (global_username != '') ? global_username : $_GET['username'];
$useree = (global_username != '') ? global_username : '';

// Display form if 'form' is set in the URL
if (isset($_GET['form'])) {
    $tit_line = make_input_group('title', 'title', $title_o, 'required');
    $cod_line = make_input_group('code', 'code', $coden, 'required');

    $nana = <<<HTML
        <div class='card' style='font-weight: bold;'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-md-10 col-md-offset-1'>
                        <form action='translate.php' method='GET'>
                            $tit_line
                            $cod_line
                            <input class='btn btn-outline-primary' type='submit' name='start' value='Start' />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    HTML;
    echo $nana;
}


function find_pages_exists_or_not($title)
{
    // {"action": "query", "titles": title, "rvslots": "*"}
    $params = [
        "action" => "query",
        "titles" => $title,
        'format' => 'json',
        "formatversion" => 2
    ];
    $endPoint = "https://simple.wikipedia.org/w/api.php";

    $result = post_url_params_result($endPoint, $params);

    $result = $result['query']['pages'] ?? [];
    // ---
    test_print(json_encode($result));
    // ---
    if (count($result) > 0) {
        $page = $result[0];
        $misssing = $page['missing'] ?? '';
        $pageid = $page['pageid'] ?? '';
        // ---
        if (empty($misssing) || !empty($pageid)) {
            return true;
        }
    }
    return false;
}

function go_to_translate_url($output, $go, $title_o, $coden, $tr_type, $test)
{
    // ---
    $url = make_translation_url($title_o, $coden, $tr_type);
    $title_o2 = rawurlencode(str_replace(' ', '_', $title_o));
    // ---
    $page_en = $tr_type == 'all' ? "User:Mr. Ibrahem/$title_o2/full" : "User:Mr. Ibrahem/$title_o2";
    // ---
    if ($coden == 'en') {
        $url = "//en.wikipedia.org/w/index.php?title=$page_en&action=edit";
    }
    // ---
    if (trim($output) == true || $go) {

        if (!empty($test) && (!$go)) {
            $wiki = $coden . "wiki";
            echo <<<HTML
                <br>trim(output) == "true"
                <br><a href='$url' target='_blank'>go to ContentTranslation in $wiki</a>
            HTML;
        } else {
            echo <<<HTML
                <script type='text/javascript'>
                window.open('$url', '_self');
                </script>
                <noscript>
                    <meta http-equiv='refresh' content='0; url=$url'>
                </noscript>
            HTML;
        }
    } elseif (trim($output) == 'notext') {
        $li = make_mdwiki_title($title_o);
        // ---
        echo <<<HTML
            page: $li has no text..<br>
        HTML;
    } else {
        $en_link = make_target_url($page_en, "en", $name = $title_o);
        echo <<<HTML
            error when save to enwiki. $en_link.<br>($output)
        HTML;
    }
}
//---
// Display login button if user is not logged in
if (empty($useree)) echo login_card();
//---
$user_valid = (!empty($useree)) ? true : false;
$go = $_GET['go'] ?? '';
$go = (!empty($go)) ? true : false;
//---
// TODO: temporary solution
// $user_valid = true;
// $go = true;
// Process form data if title, code, and user are set
if (!empty($title_o) && !empty($coden) && $user_valid) {
    $title_o = trim($title_o);
    $coden   = trim($coden);
    $useree  = trim($useree);

    $test    = $_GET['test'] ?? '';
    $cat     = $_GET['cat'] ?? '';
    $camp    = $_GET['camp'] ?? '';
    $fixref  = $_GET['fixref'] ?? '';
    $tr_type = $_GET['type'] ?? 'lead';

    $useree  = rawurldecode($useree);
    $cat     = rawurldecode($cat);
    $camp    = rawurldecode($camp);
    $title_o = rawurldecode($title_o);

    $word = $Words_table[$title_o] ?? 0;

    if ($tr_type == 'all') {
        $word = $All_Words_table[$title_o] ?? 0;
    }
    // ---
    $title2 = 'User:Mr. Ibrahem/' . $title_o;
    // ---
    $output = false;
    // ---
    $output = startTranslatePhp($title_o, $tr_type, false, $expend_refs = $fix_ref_in_text);
    // ---
    test_print("output startTranslatePhp: ($output)");
    // ---
    if ($output != true) {
        $output = find_pages_exists_or_not($title2);
        test_print("output find_pages_exists_or_not: ($output)");
    };
    // ---
    echo "<br>result: $output";
    // ---
    if ($output == true) {
        insertPage($title_o, $word, $tr_type, $cat, $coden, $useree);
        // ---
        go_to_translate_url($output, $go, $title_o, $coden, $tr_type, $test);
    }
}
