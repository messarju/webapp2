<?php
try {
    $url   = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
    $bytes = isset($_REQUEST['bytes']) ? $_REQUEST['bytes'] : '';
    if (!$url) {
        throw new RuntimeException("No URL");
    } else if (!$bytes) {
        throw new RuntimeException("No bytes");
    }
    $opts = array(
        'http' => array(
            'method' => "GET",
            'header' => array("Range: bytes=$bytes"),
        ),
    );
    $context = stream_context_create($opts);
    echo (file_get_contents($url, false, $context));
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
