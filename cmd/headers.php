<?php
try {
    header('Content-type: image/png');
    $url = $_REQUEST['url'];
    $headers = get_headers($url, 0);
    echo (implode("\n", $headers));
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}

