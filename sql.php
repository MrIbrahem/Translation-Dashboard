<?php
//--------------------
// ******************
// Read the ini file
$inifile_local = '../OAuthConfig.ini';
$inifile_mdwiki = '/data/project/mdwiki/OAuthConfig.ini';
// ******************
$inifile = $inifile_mdwiki;
// ******************
$teste = file_get_contents($inifile_mdwiki);
if ( $teste != '' ) { 
    $inifile = $inifile_mdwiki;
} else {
    $inifile = $inifile_local;
};
// ******************
$ini = parse_ini_file( $inifile );
$sqlpass = $ini['sqlpass'];
//--------------------
$pass = $_REQUEST['pass'];
$qua  = $_REQUEST['code'];
$raw  = $_REQUEST['raw'];
$test = $_REQUEST['test'];
//--------------------
if ( $raw == '' ) {
    require('header.php');
    //--------------------
    $quu = "SELECT SUBSTRING_INDEX(CURRENT_USER(), '@', 1); ";
    //--------------------
    $quaa = $qua ? $qua : $quu ;
    //--------------------    
    // echo '<link rel="stylesheet" href="https://quarry.wmflabs.org/static/vendor/yeti.bootstrap.min.css">';
    echo '

    <style>
    #code {
        font-family: Monaco, Consolas, "Ubuntu Mono", monospace;
        width: 100%;
        height: auto;
        min-height: 144px;
    }
    </style>
    ';
    //--------------------
    echo "
    <ul>
    <li><a href='sql.php?code=show tables;'>show tables</a></li>
    <li><a href='sql.php?code=describe views_by_month;'>describe views_by_month;</a></li>
    <li><a href='sql.php?code=describe words;'>describe words;</a></li>
    <li><a href='sql.php?code=select * from words;'>select * from words;</a></li>
    <li><a href='sql.php?code=describe pages;'>describe pages;</a></li>
    <li><a href='sql.php?code=select * from pages;'>select * from pages;</a></li>
    <li><a href='sql.php?code=SELECT%20A.lang%20as%20lang%2CA.title%20as%20title%2C%20%0AA.user%20AS%20u1%2C%20A.target%20as%20T1%2C%20A.date%20as%20d1%2C%0AB.user%20AS%20u2%2C%20B.target%20as%20T2%2C%20B.date%20as%20d2%0A%0AFROM%20pages%20A%2C%20pages%20B%0AWHERE%20A.user%20%3C%3E%20B.user%0AAND%20A.title%20%3D%20B.title%0AAND%20A.lang%20%3D%20B.lang%0AORDER%20BY%20A.title%3B'>Find duplicte pages.</a></li>
    </ul>
    <form action='sql.php' method='POST'>
    <textarea cols='100' rows='10' name='code'>$quaa</textarea>
    <input type='text' name='pass' value= '$pass'>
    <br>
    <input type='checkbox' id='test' name='test' value='m'> <label for=test> test</label><br>
    <input type='checkbox' id='raw' name='raw' value='m'> <label for=test> raw</label><br>
    <input class='btn btn-lg' type='submit' name='start' value='Start' />
    </form>
    ";
};
//--------------------
function sqlquary_localhost($quae) {
    //--------------------
    $host = '127.0.0.1:3306';
    $dbname = "mdwiki";
    //--------------------
    try {
        // start
        $db = new PDO(
                "mysql:host=$host;dbname=$dbname", 
                'root', 
                'root11'
                );
        //--------------------
        $q = $db->prepare($quae);
        $q->execute();
        $result = $q->fetchAll();
        //--------------------
        return $result;
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
    }
    //--------------------
    // end 
    $db = null;
    //--------------------
};
//--------------------
function sqlquary($quae) {
    //--------------------
    $ts_pw = posix_getpwuid(posix_getuid());
    // replica.my.cnf
    $ts_mycnf = parse_ini_file($ts_pw['dir'] . "/replica.my.cnf");
    //--------------------
    $host = 'tools.db.svc.wikimedia.cloud';
    $dbname = $ts_mycnf['user'] . "__mdwiki";
    //--------------------
    try {
        // start
        $db = new PDO(
                "mysql:host=$host;dbname=$dbname", 
                $ts_mycnf['user'], 
                $ts_mycnf['password']
                );
        //--------------------
        unset($ts_mycnf, $ts_pw);
        $q = $db->prepare($quae);
        $q->execute();
        $result = $q->fetchAll();
        //--------------------
        return $result;
    } 
    catch(PDOException $e) {
        echo $quae . "<br>" . $e->getMessage();
    }
    //--------------------
    // end 
    $db = null;
    //--------------------
};
//--------------------
if ( $qua != '' and ($pass == $sqlpass or $_SERVER['SERVER_NAME'] == 'localhost') ) {
    //==========================
    if ($_SERVER['SERVER_NAME'] == 'mdwiki.toolforge.org') {
        $uu = sqlquary($qua);
    } else {
        $uu = sqlquary_localhost($qua);
    };
    //==========================
    $start = '<table class="sortable table table-striped alignleft"><tr>';
    $text = '';
    //==========================
    $number = 0;
    //==========================
    foreach ( $uu AS $id => $row ) {
        $number = $number + 1;
        $tr = '<tr>';
        //--------------------
        //--------------------
        foreach ( $row AS $nas => $value ) {
            // if ($nas != '') {
            if (preg_match( '/^\d+$/', $nas, $m ) ) {
                $ii = '';
            } else {
                $tr .= "<td>$value</th>";
                if ($number == 1) { 
                    $start .= "<th onclick='sortTable(0)' class='text-nowrap'>$nas</th>";
                };
            };
        };
        //--------------------
        $tr .= '</tr>';
        //--------------------
        if ($tr != '<tr></tr>' ) { $text .= $tr; };
        //--------------------
    };
    //==========================
    $start .= '</tr>';
    //==========================
    if ( $raw == '' ) {
        PRINT($start . $text . '</table>');
        //==========================
        // if ($test != '') { print_r($uu);};
        if ($test != '') { print(var_export($uu)) ;};
        //==========================
        if ($text == '') {
            if ($test != '') {
                print_r($uu);
            } else {
                print(var_dump($uu));
            };
        };
    } else {
        //--------------------
        $sql_result = array();
        //--------------------
        $n = 0;
        //--------------------
        foreach ( $uu AS $id => $row ) {
            $ff = array();
            $n = $n + 1 ;
            // ------------------------
            foreach ( $row AS $nas => $value ) {
                if (preg_match( '/^\d+$/', $nas, $m ) ) {
                    $ii = '';
                } else {
                    $ff[$nas] = $value;
                };
            };
            // ------------------------
            
            $sql_result[$n] = $ff;
        };
        print(json_encode($sql_result));
        
    };
    //==========================
};
//==========================
//==========================

/*
$sql = <<<____SQL
     CREATE TABLE IF NOT EXISTS `ticket_hist` (
       `tid` int(11) NOT NULL,
       `trqform` varchar(40) NOT NULL,
       `trsform` varchar(40) NOT NULL,
       `tgen` datetime NOT NULL,
       `tterm` datetime,
       `tstatus` tinyint(1) NOT NULL
     ) ENGINE=ARCHIVE COMMENT='ticket archive';
____SQL;
$result = $this->db->getConnection()->exec($sql);

*/
//==========================
//--------------------
if ( $raw == '' ) {
    print "</div>";
    //--------------------
    require('foter.php');
    //--------------------
};
//==========================
?>