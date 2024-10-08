<?PHP

use function Actions\MdwikiSql\fetch_query;

$year = $_REQUEST['year'] ?? 'all';
$camp = $_REQUEST['camp'] ?? 'all';
$project = $_REQUEST['project'] ?? 'all';

$tab_for_graph2 = [
    "year" => $year,
    "campaign" => $camp,
    "user_group" => $project
];

if ($camp == 'all' && isset($_REQUEST['cat'])) {
    $camp = $cat_to_camp[$_REQUEST['cat']] ?? 'all';
}
$camp_cat = $camp_to_cat[$camp] ?? '';

function makeSqlQuery()
{
    global $year, $camp, $project, $camp_cat;
    $queryPart1Group = "SELECT p.title,
        p.target, p.cat, p.lang, p.word, YEAR(p.pupdate) AS pup_y, p.user, u.user_group, LEFT(p.pupdate, 7) as m
        FROM pages p, users u
    ";

    $queryPart1 = "SELECT p.title,
        p.target, p.cat, p.lang, p.word, YEAR(p.pupdate) AS pup_y, LEFT(p.pupdate, 7) as m,
        p.user,
        (SELECT u.user_group FROM users u WHERE p.user = u.username) AS user_group
        FROM pages p
    ";

    $queryPart2 = "
        WHERE p.target != ''
    ";
    // 2023-08-22
    // if ($camp != 'all' && !empty($camp_cat)) $queryPart2 .= "AND p.cat = '$camp_cat' \n";

    if ($year != 'all') {
        $queryPart2 .= "AND YEAR(p.pupdate) = '$year' \n";
    }

    if ($project != 'all') {
        $queryPart1 = $queryPart1Group;
        $queryPart2 .= "AND p.user = u.username \n";
        $queryPart2 .= "AND u.user_group = '$project' \n";
    }

    $query = $queryPart1 . $queryPart2;

    if (isset($_REQUEST['test'])) {
        echo $query;
    }
    return $query;
}
//---
$qua_all = makeSqlQuery();

$Words_total = 0;
$Articles_numbers = 0;
$global_views = 0;
$sql_users_tab_to_lang = array();
$sql_users_tab = array();
$Users_word_table = array();
$sql_Languages_tab = array();
$all_views_by_lang = array();
$Views_by_users = array();

$Views_by_lang_target = make_views_by_lang_target();
$tab_for_graph = [];
// $articles_to_camps, $camps_to_articles

foreach (fetch_query($qua_all) as $Key => $teb) {
    $title  = $teb['title'] ?? "";
    //---
    // 2023-08-22
    if ($camp != 'all' && !empty($camp_cat)) {
        if (!in_array($title, $camps_to_articles[$camp])) continue;
    }
    //---
    $month  = $teb['m'] ?? ""; // 2021-05
    //---
    if (!isset($tab_for_graph[$month])) $tab_for_graph[$month] = 0;
    $tab_for_graph[$month] += 1;
    //---
    $cat    = $teb['cat'] ?? "";
    $lang   = $teb['lang'] ?? "";
    $user   = $teb['user'] ?? "";
    $target = $teb['target'] ?? "";
    $word   = $teb['word'] ?? "";
    if ($word == 0) {
        $word = $Words_table[$title] ?? 0;
    }
    $coco = $Views_by_lang_target[$lang][$target][$year] ?? 0;

    $Words_total += $word;
    $Articles_numbers += 1;
    $global_views += $coco;

    if (!isset($all_views_by_lang[$lang])) $all_views_by_lang[$lang] = 0;
    $all_views_by_lang[$lang] += $coco;

    if (!isset($sql_Languages_tab[$lang])) $sql_Languages_tab[$lang] = 0;
    $sql_Languages_tab[$lang] += 1;

    if (!isset($Users_word_table[$user])) $Users_word_table[$user] = 0;
    $Users_word_table[$user] += $word;

    if (!isset($Views_by_users[$user])) $Views_by_users[$user] = 0;
    $Views_by_users[$user] += $coco;

    if (!isset($sql_users_tab[$user])) $sql_users_tab[$user] = 0;
    $sql_users_tab[$user] += 1;

    if (!isset($sql_users_tab_to_lang[$user])) $sql_users_tab_to_lang[$user] = [];
    if (!isset($sql_users_tab_to_lang[$user][$lang])) $sql_users_tab_to_lang[$user][$lang] = 0;
    $sql_users_tab_to_lang[$user][$lang] += 1;
}
