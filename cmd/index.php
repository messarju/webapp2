<?php
try {
    error_reporting(E_ALL);
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    // header('Content-type: image/png');
   // $mt = $_REQUEST['use_content_type'];
   // if($mt){
   //     header("Content-type: $mt");
   // }else{
        header('Content-type: application/x-font-opentype');
   // }
    $cmd = $_REQUEST['_'];
    if(!isset($cmd) || empty($cmd)){
        throw new RuntimeException("No cmd");
    }
    if (!include_once ("$cmd.php")) {
        throw new RuntimeException("Command `$cmd' not found");
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    header('Content-type: text/html');
    echo "\n======\n";
    echo $e->getMessage();
    echo "\n======\n";
}
