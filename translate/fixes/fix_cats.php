<?php
namespace Translate\Fixes\FixCats;

/*
Usage:

use function Translate\Fixes\FixCats\remove_categories;

*/


use function WikiParse\Category\get_categories;

function remove_categories($text)
{
    // ---
    $categories = get_categories($text);
    // ---
    foreach ($categories as $name => $cat) {
        // echo "delete category: " . $name . "<br>";
        $text = str_replace($cat, '', $text);
    }
    // ---
    return $text;
}
