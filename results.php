<?PHP
//---
include_once('tables.php'); 
include_once('langcode.php');
include_once('getcats.php');
include_once('functions.php');
include_once('sql_tables.php');
//---
$doit = isset($_REQUEST['doit']);
//---
$code = $_REQUEST['code'] ?? '';
//---
if ($code == 'undefined') $code = "";
//---
$code = $lang_to_code[$code] ?? $code;
$code_lang_name = $code_to_lang[$code] ?? ''; 
//---
$tra_type  = $_REQUEST['type'] ?? '';
//---
$cat = $_REQUEST['cat'] ?? '';
//---
if ($cat == "undefined") $cat = "RTT";
//---
$translation_button = $settings['translation_button_in_progress_table']['value'] ?? '0';
if (global_username != 'James Heilman' && global_username != 'Mr. Ibrahem') $translation_button = '0';
//---
function sort_py_PageViews( $items ) {
    //---
    global $enwiki_pageviews_table;
    //---
    $dd = array();
    //---
    // sort py PageViews
    foreach ( $items AS $t ) {
        $t = str_replace ( '_' , ' ' , $t );
        //---
        $kry = $enwiki_pageviews_table[$t] ?? 0; 
        //---
        $dd[$t] = $kry;
        //---
    };
    //---
    arsort($dd);
    //---
    return $dd;
};
//---
function sort_py_importance( $items ) {
    //---
    global $Assessments_fff, $Assessments_table;
    $dd = array();
    //---
    foreach ( $items AS $t ) {
        $t = str_replace ( '_' , ' ' , $t );
        //---
        $aa = $Assessments_table[$t] ?? null;
        //---
        $kry = $Assessments_fff['Unknown'] ?? '';
        //---
        if ( isset($aa) ) {
            $kry = $Assessments_fff[$aa] ?? $Assessments_fff['Unknown'];
        };
        //---
        $dd[$t] = $kry;
        //---
    };
    //---
    arsort($dd);
    //---
    return $dd;
};
//---
function make_table( $items, $cod, $cat, $inprocess=false ) {
    global $Words_table, $All_Words_table, $Assessments_table ,$tra_type;
    global $Lead_Refs_table, $All_Refs_table, $enwiki_pageviews_table, $translation_button;
    //---
    global $sql_qids;
    //---
    $Refs_word = 'Lead refs';
    $Words_word = 'Words';
    //---
    if ($tra_type == 'all') { 
        $Words_word = 'words';
        $Refs_word = 'References';
        };
    //---
    // $Translate_th = "<div class='d-none d-sm-block'><th class='spannowrap' tt='h_len'>Translate</th></div>";
    $Translate_th = "<th class='d-none d-sm-block spannowrap' tt='h_len'>Translate</th>";
    //---
    $in_process = array();
    $inprocess_first = '';
    //---
    if ( $inprocess ) {
        $inprocess_first = '<th>user</th><th>date</th>';
        //---
        $in_process = $items;
        //---
        $items = array_keys($items);
        //---
        if ($translation_button != '1') {
            $Translate_th = '';
        };
        //---
    };
    //---
	$frist = <<<HTML
    <!-- <div class="table-responsive"> -->
    <table class="table table-sm sortable table-striped" id="main_table">
        <thead>
            <tr>
                <th class="num">#</th>
                <th class="spannowrap" tt="h_title">Title</th>
                $Translate_th
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Page views in last month in English Wikipedia">Pageviews</span></th>
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Page important from medicine project in English Wikipedia">Importance</span></th>
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="number of word of the article in mdwiki.org">$Words_word</span></th>
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="number of reference of the article in mdwiki.org">$Refs_word</span></th>
                <th class="spannowrap" tt="h_len"><span data-toggle="tooltip" title="Wikidata identifier">qid</span></th>
                $inprocess_first
            </tr>
        </thead>
        <tbody>
    HTML;
    //---
    $dd = array();
    //---
    // $dd = sort_py_importance($items);
    $dd = sort_py_PageViews($items);
    //---
    $list = "" ;
    $cnt = 1 ;
    //---
    foreach ( $dd AS $v => $gt) {
        if ( $v == '' ) continue;
        $title = str_replace ( '_' , ' ' , $v );
        //---
        $title2 = rawurlEncode($title);
        //---
        $cat2 = rawurlEncode($cat);
        //---
        $urle = "//mdwiki.org/wiki/$title2";
        $urle = str_replace( '+' , '_' , $urle );
        //---
        $pageviews = $enwiki_pageviews_table[$title] ?? 0; 
        //---
        $qid = $sql_qids[$title] ?? "";
        $qid = ($qid != '') ? "<a href='https://wikidata.org/wiki/$qid'>$qid</a>" : '';
        //---
        $word = $Words_table[$title] ?? 0; 
        //---
        $refs = $Lead_Refs_table[$title] ?? 0; 
        //---
        if ($tra_type == 'all') { 
            $word = $All_Words_table[$title] ?? 0;
            $refs = $All_Refs_table[$title] ?? 0;
            };
        //---
        $asse = $Assessments_table[$title] ?? '';
        //---
        if ( $asse == '' ) $asse = 'Unknown';
        //---
        $params = array(
            "title" => $title2,
            "code" => $cod,
            "username" => global_username,
            "cat" => $cat2,
            "type" => $tra_type
            );
        //---
        $translate_url = 'translate.php?' . http_build_query($params);
        //---
        $tab = <<<HTML
            <a role='button' class='btn btn-primary' onclick='login()'>
                <i class='fas fa-sign-in-alt fa-sm fa-fw mr-1'></i><span class='navtitles'>Login</span>
            </a>
            HTML;
        //---
        if ( global_username != '' ) $tab = "<a href='$translate_url' class='btn btn-primary btn-sm'>Translate</a>";
        //---
        // $tab_td = "<div class='d-none d-sm-block'><td class='num'>$tab</td></div>";
        $tab_td = "<td class='d-none d-sm-block num'>$tab</td>";
        //---
        $inprocess_tds = '';
        if ( $inprocess ) {
            $_user_ = $in_process[$v]['user'];
            $_date_ = $in_process[$v]['date'];
            $inprocess_tds = "<td>$_user_</td><td>$_date_</td>";
            if ($translation_button != '1') $tab_td = '';
        };
        //---
        $list .= <<<HTML
            <tr>
                <td class='num'>$cnt</td>
                <td class='link_container spannowrap'><a target='_blank' href='$urle'>$title</a></td>
                $tab_td
                <td class='num'>$pageviews</td>
                <td class='num'>$asse</td>
                <td class='num'>$word</td>
                <td class='num'>$refs</td>
                <td>$qid</td>
                $inprocess_tds
            </tr>
            HTML;
        //---
        $cnt++ ;
        //---
    };
    //---
    $script = '' ;
    if ($script =='3') $script = '';
    //---
    $last = <<<HTML
        </tbody>
    </table>
    <!-- </div> -->
    HTML;
    //---
    return $frist . $list . $last . $script ;
    //---
    }
//---
$doit2 = false ;
//---
if ( $code_lang_name != '' ) $doit2 = true;
//---
echo "<div class='container'>";
//---
if ( $doit && $doit2 ) {
    //---
    if (global_test) echo '$doit and $doit2:<br>';
    //---
    $items = array() ;
    //---
    $items = get_cat_members($cat, $depth, $code) ; # mdwiki pages in the cat
    //---
    $len_of_exists_pages = $items['len_of_exists'];
    $items_missing       = $items['missing'];
    //---
    $missing = array();
    foreach ( $items_missing as $key => $cca ) if (!in_array($cca, $missing)) $missing[] = $cca;
    //---
    $in_process = get_in_process($missing, $code);
    //---
    $len_in_process = count($in_process);
    //---
    $len_of_missing_pages = count($missing);
    $len_of_all           = $len_of_exists_pages + $len_of_missing_pages;
    //---
	$cat2 = "Category:" . str_replace ( 'Category:' , '' , $cat );
	$caturl = "<a href='https://mdwiki.org/wiki/$cat2'>category</a>";
    //---
    $ix =  "Found $len_of_all pages in $caturl, $len_of_exists_pages exists, and $len_of_missing_pages missing in (<a href='https://$code.wikipedia.org'>$code</a>), $len_in_process In process." ;
    //---
    $res_line = " Results ";
    //---
    if (global_test != '') $res_line .= 'test:';
    //---
    // delete $in_process keys from $missing
    if ($len_in_process > 0) {
        $missing = array_diff($missing, array_keys($in_process));
    };
    //---
    $table = make_table($missing, $code, $cat) ;
    //---
    echo <<<HTML
    <br>
    <div class='card'>
        <div class='card-header'>
            <h5>$res_line:</h5>
            <!-- <h5>$ix</h5> -->
        </div>
        <div class='card-body1 card2'>
            $table
        </div>
    </div>
    HTML;
    //---
    if ($len_in_process > 0) {
        //---
        $table_2 = make_table($in_process, $code, $cat, $inprocess=true) ;
        //---
        echo <<<HTML
        <br>
        <div class='card'>
            <div class='card-header'>
                <h5>$len_in_process in process</h5>
            </div>
            <div class='card-body1 card2'>
                $table_2
            </div>
        </div>
        HTML;
    };
    //---
    if (isset($doit) && global_test != '' ) {
        //---
        echo "_REQUEST code:" . isset($_REQUEST['code']) . "<br>";
        echo "code:$code<br>";
        echo "code_lang_name:$code_lang_name<br>";
        //---
    };
    echo '</div>';
};
//---
echo "</div>";
//---
?>
