<?php

namespace Translate\FixText;

/*
Usage:

use function Translate\FixText\text_changes_work;

*/

use function Translate\Fixes\DelMtRefs\del_empty_refs;
use function Translate\Fixes\ExpendRefs\refs_expend_work;
use function Translate\Fixes\FixCats\remove_categories;
use function Translate\Fixes\FixImages\remove_images;
use function Translate\Fixes\fix_langs_links\remove_lang_links;
use function Translate\Fixes\FixTemps\remove_templates_lead;
use function Translate\Fixes\FixTemps\add_missing_title;
use function Translate\Fixes\FixTemps\remove_templates;
use function Translate\Fixes\RefWork\remove_bad_refs;
use function Actions\Functions\test_print;


if (isset($_REQUEST['test'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
};

include_once __DIR__ . '/../WikiParse/Template.php';
include_once __DIR__ . '/../WikiParse/Citations.php';
include_once __DIR__ . '/../WikiParse/Category.php';

include_once __DIR__ . '/fixes/fix_images.php';
include_once __DIR__ . '/fixes/fix_cats.php';
include_once __DIR__ . '/fixes/fix_temps.php';
include_once __DIR__ . '/fixes/fix_langs_links.php';

include_once __DIR__ . '/fixes/del_mt_refs.php';
include_once __DIR__ . '/fixes/expend_refs.php';
include_once __DIR__ . '/fixes/ref_work.php';

function text_changes_work($text, $allText, $expend_refs = false, $title)
{
    // ---
    $title = str_replace('_', ' ', $title);
    // ---
    $text = str_replace("{{drugbox", "{{Infobox drug", $text);
    $text = str_replace("{{Drugbox", "{{Infobox drug", $text);
    // ---
    $text = add_missing_title($text, $title);
    // ---
    if ($expend_refs) {
        $text = refs_expend_work($text, $allText);
    };
    // ---
    $text = remove_bad_refs($text);
    // ---
    $text = del_empty_refs($text);
    // ---
    $text = remove_templates_lead($text);
    // $text = remove_templates($text);
    // ---
    $text = remove_lang_links($text);
    // ---
    $text = remove_images($text);
    // ---
    $text = remove_categories($text);
    // ---
    test_print("text_changes_work: lenth of text:" . strlen($text));
    // ---
    return $text;
}
