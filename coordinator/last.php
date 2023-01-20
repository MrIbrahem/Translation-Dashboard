<div class='card-header'>
    <h4>Most recent translations:</h4>
</div>
<div class='card-body'>
<?PHP
//---
function make_td($tabg, $nnnn) {
    //---
    global $code_to_lang, $Words_table, $views_sql, $username;
    //---
    $id       = $tabg['id'];
    $date     = $tabg['date'];
    //---
    //return $date . '<br>';
    //---
    $user     = $tabg['user'];
    $llang    = $tabg['lang'];
    $md_title = $tabg['title'];
    $cat      = $tabg['cat'];
    $word     = $tabg['word'];
    $targe    = $tabg['target'];
    $pupdate  = isset($tabg['pupdate']) ? $tabg['pupdate'] : '';
    //---
    $views_number = isset($views_sql[$targe]) ? $views_sql[$targe] : '?';
    //---
    $lang2 = isset($code_to_lang[$llang]) ? $code_to_lang[$llang] : $llang;
    //---
    $ccat = make_cat_url( $cat );
    //---
    $worde = isset($word) ? $word : $Words_table[$md_title];
    //---
    $nana = make_mdwiki_title( $md_title );
    //---
    $targe33 = make_target_url( $targe, $llang );
    //---
    $view = make_view_by_number($targe, $views_number, $llang, $pupdate);
    //---
    $mail_params = array( 'user' => $user, 'lang' => $llang, 'target' => $targe, 'date' => $pupdate, 'title' => $md_title, 'nonav' => '1');
    //---
    $mail_url = "coordinator.php?ty=email&" . http_build_query( $mail_params );
    //---
	$onclick = 'pupwindow("' . $mail_url . '")';
    //---
    $mail = "<a class='btn btn-primary btn-sm' onclick='$onclick'>Email</a>";
    //---
    $laly = '
        <tr>
            <td>' . $nnnn   . '</td>
            <td><a target="" href="leaderboard.php?user=' . $user . '">' . $user . '</a></td>
            <td>' . $mail . '</td>
            <td><a target="" href="leaderboard.php?langcode=' . $llang . '">' . $lang2 . '</a>' . '</td>
            <td style="max-width:150px;">' . $nana  . '</td>
            <!-- <td>' . $date  . '</td> -->
            <td>' . $ccat  . '</td>
            <!-- <td>' . $worde . '</td> -->
            <td style="max-width:150px;">' . $targe33 . '</td>
            <td>' . $pupdate . '</td>
            <td>' . $view . '</td>
            '; 
    //---
    $laly .= '
        </tr>';
    //---
    return $laly;
};
//---
$quaa = "select * from pages where target != ''
ORDER BY pupdate DESC
limit 100
;";
$dd = quary2($quaa);
//---
$sato = '
	<table class="table table-sm table-striped soro" style="font-size:90%;">
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th></th>
            <th><span data-toggle="tooltip" title="Language">Lang.</span></th>
            <th>Title</th>
            <th>Category</th>
            <!-- <th>Words</th> -->
            <th>Translated</th>
            <th>Date</th>
            <th>Views</th>
        </tr>
    </thead>
    <tbody>
';
//---
$noo = 0;
foreach ( $dd AS $tat => $tabe ) {
    //---
    $noo = $noo + 1;
    $sato .= make_td($tabe, $noo);
    //---
};
//---
$sato .= '
    </tbody>
</table>
';
print $sato;
//---
?>
<script>
function pupwindow(url) {
	window.open(url, 'popupWindow', 'width=750,height=550,scrollbars=yes');
};
</script>