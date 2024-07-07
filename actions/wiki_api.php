<?php

function get_url_result(string $url): string
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");

    $output = curl_exec($ch);
    if ($output === FALSE) {
        echo("<br>cURL Error: " . curl_error($ch));
    }

    curl_close($ch);

    return $output;
}

function make_view_by_number($target, $numb, $lang, $pupdate)
{
    // remove spaces and tab characters
    $target = trim($target);
    $numb2 = ($numb != '') ? $numb : "?";
    $start = $pupdate != '' ? $pupdate : '2019-01-01';
    $end = date("Y-m-d", strtotime("yesterday"));

    $url = 'https://pageviews.wmcloud.org/?' . http_build_query(array(
        'project' => "$lang.wikipedia.org",
        'platform' => 'all-access',
        'agent' => 'all-agents',
        'start' => $start,
        'end' => $end,
        // 'range' => 'all-time',
        'redirects' => '0',
        'pages' => $target,
    ));
    $start2 = $pupdate != '' ? str_replace('-', '', $pupdate) : '20190101';

    $url2 = 'https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/' . $lang . '.wikipedia/all-access/all-agents/' . rawurlencode($target) . '/daily/' . $start2 . '/2030010100';

    $link = "<a target='_blank' href='$url'>$numb2</a>";

    if ($numb2 == '?' || $numb2 == 0 || $numb2 == '0') {
        $link = "<a target='_blank' name='toget' hrefjson='$url2' href='$url'>$numb2</a>";
    };
    return $link;
};

function get_views($target, $lang, $pupdate)
{
    if ($target == '') return 0;
    $start2 = $pupdate != '' ? str_replace('-', '', $pupdate) : '20190101';
    $url = 'https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/' . $lang . '.wikipedia/all-access/all-agents/' . rawurlencode($target) . '/daily/' . $start2 . '/2030010100';
    // ---
    // $output = file_get_contents( $url );
    $output = get_url_result($url);
    // ---
    $result = json_decode($output, true);
    //---
    if (!is_array($result)) {
        $result = array();
    }
    //---
    // $view = 0;
    // foreach ($result['items'] AS $da) $view += $da['views'];

    $view = isset($result['items']) ? array_sum(array_column($result['items'], 'views')) : 0;

    return $view;
};
