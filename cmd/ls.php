<?php
try {
    include_once 'item.php';
    include_once 'lister.php';
    $dir = $_REQUEST['dir'];
    $dir = ($dir ? $dir : '.');
    $ls  = new Ls();
    $ls->start($dir, $_REQUEST);
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
