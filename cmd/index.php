<?php
try {
    error_reporting(E_ALL);
    header('Content-type: image/png');
    $cmd = $_REQUEST['_'];
    if (!include_once ("$cmd.php")) {
        throw new RuntimeException("Command `$cmd' not found");
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    header('Content-type: image/png');
    echo $e->getMessage();
}
