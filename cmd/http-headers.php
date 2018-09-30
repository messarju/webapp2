<?php
try {
    $url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
    if (!$url) {
        throw new RuntimeException("No URL");
    }
    $headers = get_headers($url, 0);
    if (!$headers) {
        throw new RuntimeException("get_headers empty");
    }
    echo (implode("\n", $headers));
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
