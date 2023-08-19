<?PHP
//---
/*
isset\((\$_\w+\[.*?\])\)\s*\?\s*\1\s*:\s*(.*?);
isset\((\$.*?\[.*?\])\)\s*\?\s*\1\s*:\s*(.*?);
isset\((.*?)\)\s*\?\s*\1\s*:\s*(.*?);
\(*isset\((.*?)\)\)*\s*\?\s*\1\s*:\s*(.*?);
$1 ?? $2;
*/
//---
require('header.php');
require('langcode.php');
include_once('functions.php');
include_once('td_config.php');
include_once('sql_tables.php');
//---
$conf = get_configs('conf.json');
//---
$allow_whole_translate = $settings['allow_type_of_translate']['value'] ?? '1';
//---
$catinput_depth = array();
$catinput_list = array();
//---
$main_cat = ''; # RTT
//---
foreach (execute_query('select category, display, depth, def from categories;') AS $Key => $table ) {
    $category = $table['category'];
    $display  = $table['display'];
    $catinput_list[$display] = $category;
    //---
    $default  = $table['def'];
    if ($default == 1 || $default == '1') $main_cat = $category;
    //---
    $catinput_depth[$category] = $table['depth'];
    //---
};
//---
$req  = load_request();
$code = $req['code'];
$cat  = ($req['cat'] != '') ? $req['cat'] : $main_cat;
$code_lang_name = $req['code_lang_name'];
//---
$tra_type  = $_REQUEST['type'] ?? '';
if ($allow_whole_translate == '0') $tra_type = 'lead';
//---
$cat_ch = htmlspecialchars($cat, ENT_QUOTES);
//---
$depth  = $_REQUEST['depth'] ?? 1;
$depth  = $depth * 1 ;
//---
$depth  = $catinput_depth[$cat] ?? 1;
//---
function print_form_start1() {
    //---
    global $allow_whole_translate ;
    global $lang_to_code, $catinput_list;
    global $cat_ch, $code_lang_name, $code, $username, $tra_type;
    //---
    $lead_checked = "checked";
    $all_checked = "";
    //---
    if ($tra_type == 'all') {
        $lead_checked = "";
        $all_checked = "checked";
    };
    //---
    $coco = $code_lang_name;
    if ( $coco == '') { $coco = $code ; };
    //---
    $lang_list = '';
    //---
    foreach ( $lang_to_code AS $langeee => $codr ) {
		$selected = ($codr == $code) ? 'selected' : '';
        $lang_list .= <<<HTML
            <option data-tokens='$codr' value='$codr' $selected>$langeee</option>
            HTML;
    };
    //---
    $langse_old = <<<HTML
		<input list='sLanguages' type='text' placeholder='two letter code' id='code' name='code' value='$coco' autocomplete='off' role='combobox' class='form-select' required>
			<datalist id='Languages' class='selectpickerr' role='listbox' data-bs-theme="auto">
			$lang_list
			</datalist>
		</input>
	HTML;
	//---
    $langse = <<<HTML
        <select 
            class="selectpicker"
            id='code'
            name='code'
            placeholder='two letter code'
            data-live-search="true"
            data-container="body"
            data-live-search-style="begins"
            data-bs-theme="auto"
            data-style='btn active'
            data-width="100%"
            required>
            $lang_list
        </select>
    HTML;
	//---
    $err = '';
    //---
    if ($code_lang_name == '' and $code != '') { 
        $err = "<span style='font-size:13pt;color:red'>code ($code) not valid wiki.</span>";
    } else {
        if ($code != '') { $_SESSION['code'] = $code; };
    };
    //---
    $uiu = <<<HTML
    <a role="button" class="btn btn-primary" onclick="login()">
    <i class="fas fa-sign-in-alt fa-sm fa-fw mr-1"></i><span class="navtitles">Login</span>
    </a>
    HTML;
    //---
    if ( global_username != '' ) $uiu = '<input type="submit" name="doit" class="btn btn-primary" value="Do it"/>';
    //---
    $catinput = make_drop($catinput_list, $cat_ch);
    //---
    $catinput = <<<HTML
        <select dir='ltr' name='cat' class='form-select' data-bs-theme="auto">
            $catinput
        </select>
    HTML;
    //---
    $ttype = <<<HTML
        <div class='form-check form-check-inline'>
            <input type='radio' class='form-check-input' id='customRadio' name='type' value='lead' $lead_checked>
            <label class='form-check-label' for='customRadio'>The lead only</label>
        </div>
        <div class='form-check form-check-inline'>
            <input type='radio' class='form-check-input' id='customRadio2' name='type' value='all' $all_checked>
            <label class='form-check-label' for='customRadio2'>The whole article</label>
        </div>
    HTML;
    //---
	$col12 		= 'col-10';
	$gridclass 	= 'input-group col-7 mb-3';
    //---
    $d2 = <<<HTML
        <div class='$col12'>
            <div class='form-group'>
                <div class='$gridclass'>
                    <div class='input-group-prepend'>
                        <span class='input-group-text' for="%s">%s</span>
                    </div>
                        %s
                </div>
            </div>
        </div>
    HTML;
    //---
	$d22 = <<<HTML
        <div class='$col12'>
            <div class="mb-3">
                <label for="%s" class="form-label"><b>%s</b></label>
                %s
            </div>
        </div>
    HTML;
    //---
	$in_cat = sprintf($d22, 'cat', 'Campaign', $catinput);
	//---
	$in_lng = sprintf($d22, 'code', 'Language', "<div>$langse $err</div>");
	//---
	$in_typ = '';
    if ($allow_whole_translate == '1') { 
        $in_typ = sprintf($d22, 'type', 'Type', "<div class='form-control'>$ttype</div>");
    } else {
        $in_typ = "<input name='type' value='lead' hidden/>";
    };
    //---
    $d = <<<HTML
    <div class='row'>
        $in_cat
        $in_lng
        $in_typ
        <div class='$col12'>
            <h4 class='aligncenter mb-0'>
                $uiu
            </h4>
        </div>
    </div>
    
    HTML;
    //---
    return $d;
    //---
};
//---
$img_src = '//upload.wikimedia.org/wikipedia/commons/thumb/5/58/Wiki_Project_Med_Foundation_logo.svg/400px-Wiki_Project_Med_Foundation_logo.svg.png';
//---
$form_start1  = print_form_start1();
//---
$intro = <<<HTML
    This tool looks for Wikidata items that have a page on mdwiki.org but not in another wikipedia language <a href='?cat=RTT&depth=1&code=ceb&doit=Do+it'>(Example)</a>. <a href='//mdwiki.org/wiki/WikiProjectMed:Translation_task_force'><b>How to use.</b></a>
HTML;
//---
echo <<<HTML
<div class='container'>
  <div class='card'>
    <div class='card-header'>
        $intro
    </div>
    <div class='card-body mb-0'>
      <div class='mainindex'>
		<div style='float:right'>
            <img class='medlogo' src='$img_src' decoding='async' alt='Wiki Project Med Foundation logo'>
        </div>
        <form method='GET' action='index.php' class='form-inline'>
            $form_start1
        </form>
      </div>
    </div>
  </div>
</div>
<!-- <script src='/Translation_Dashboard/js/codes.js'></script> -->
HTML;
//---
require('results.php');
//---
require('foter.php');
//---
?>
