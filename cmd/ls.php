<?php
try {
    include_once 'item.php';
    include_once 'lister.php';
    $dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '.';
    $ls  = new Ls();
    $ls->start(new DirectoryItem($dir), $_REQUEST);
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
