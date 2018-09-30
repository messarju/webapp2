<?php
try {
    header('Content-type: image/png');
    $url   = $_REQUEST['url'];
    $bytes = $_REQUEST['bytes'];
    $opts  = array(
        'http' => array(
            'method' => "GET",
            'header' => array("Range: bytes=$bytes"),
        ),
    );
    $context = stream_context_create($opts);
    echo(file_get_contents($url, false, $context));
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
