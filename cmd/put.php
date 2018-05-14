<?php
try {
    header('Content-type: image/png');
    $path   = $_REQUEST['path'];
    $decode = $_REQUEST['decode'];
    function check_path()
    {
        global $path;
        $mkdir   = $_REQUEST['mkdir'];
        $clobber = $_REQUEST['clobber'];
        if (file_exists($path)) {
            if ($clobber == '1') {
                unlink($path);
            } else {
                throw new RuntimeException('file exists');
            }
        } else if (!file_exists($d = dirname($path))) {
            if ($mkdir == '1') {
                mkdir($d, 0777, true);
            } else {
                throw new RuntimeException("dir not found '$d'");
            }
        }
    }

    if (!$path) {
        throw new RuntimeException('No path');
    } else if ($data = $_FILES['data']) {
        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (!isset($data['error']) || is_array($data['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }
        // Check $data['error'] value.
        switch ($data['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }
        check_path();
        move_uploaded_file($data['tmp_name'], $path);
        echo (realpath($path));
    } else if ($data = $_REQUEST['data']) {
        check_path();
        if ($decode == 'base64') {
            $data = base64_decode($data);
        }
        if ($h = fopen($path, 'wb')) {
            fwrite($h, $data);
            fclose($h);
            echo (realpath($path));
        } else {
            throw new RuntimeException("fopen failed '$path'");
        }
    } else {
        throw new RuntimeException('No data');
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo ($e->getMessage());
}
