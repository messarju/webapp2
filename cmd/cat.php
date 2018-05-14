<?php
try {
    if(!($path = $_REQUEST['path'])){
        throw new RuntimeException("No path");
    }else if (!($fp = fopen($path, 'rb'))) {
        throw new RuntimeException("Failed to open `$path'");
    }else{
        header("Content-Length: " . filesize($path));
        fpassthru($fp);
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
