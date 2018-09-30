<?php
try {
    extract($_REQUEST);
    if (isset($mode)) {
        $mode = int($mode);
    } else {
        $mode = 0777;
    }
    if (isset($recursive)) {
        $recursive = $recursive == '1';
    } else {
        $recursive = 0777;
    }
    if (!isset($path)) {
        throw new RuntimeException("No path");
    }
    if (mkdir($path, $mode, $recursive == '1')) {
        echo ("mkdir: created directory '$path'");
    } else {
        throw new RuntimeException("mkdir: failed created directory `$path'");
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
