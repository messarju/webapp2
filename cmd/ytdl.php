<?php

$INFO="";
$URL="";
$action=$_REQUEST['action'];
if ($action && isset($action)) {
    $action = strtolower($action);
    $URL = $_REQUEST['url'];
    if ($URL != null) {
        $INFO=$URL;
    }
}
// $arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);
// echo json_encode($arr, JSON_PRETTY_PRINT);

if($URL){
    $YT_FILE="youtube-dl";
    $YT_LATEST="https://yt-dl.org/downloads/latest/youtube-dl";
    // $YT_LATEST="file:///mnt/META/wrx/www/biojet1.github.io/local/notes.html";
    if(!file_exists($YT_FILE)){
        exec( 'curl -L ' . escapeshellarg($YT_LATEST) . ' -o ' . escapeshellarg($YT_FILE) );
    }
    if(file_exists($YT_FILE)){
        $cmd = 'python ' . escapeshellarg($YT_FILE);
        if($action=='json'){
            $cmd = $cmd . ' -J';
        }else{
            $cmd = $cmd . ' -F';
        }
        $cmd = $cmd . ' ' . escapeshellarg($URL);
        $out = system($cmd);
        $json = json_decode($out);
        $INFO = json_encode($json, JSON_PRETTY_PRINT);
    }
}
// print_r($action);


$FORM_URL=htmlentities($_SERVER['PHP_SELF']);
$URL=htmlentities($URL);
$INFO=htmlentities($INFO);

$xml = <<<EOD
<html>
    <head>
        <title>Youtube-dl query</title>
    </head>
    <body>
        <form id="yt-url" action="$FORM_URL" method="POST">
            <input name="url" class="search" size="128" placeholder="Video URL" autocomplete="off" type="text" value="$URL"/>
            <div>
                <input name="action" value="Json" type="submit"/>
                <input name="action" value="Formats" type="submit"/>
            </div>
       </form>
        <pre>$INFO</pre>
        <style type="text/css">            body {
                font-size: 1rem;
                background: lightslategray;
            }
        </style>
        <small>2019-01-11 14:44:37+0800</small>
    </body>
</html>
EOD;
// $doc = new DOMDocument();
// $doc->loadXML($xml);
// echo $doc->saveXML();
/*
        <meta content="text/html; charset=UTF-8" http-equiv="content-type"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

*/


echo($xml);
