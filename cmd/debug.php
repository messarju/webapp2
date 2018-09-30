<?php
try {
    function print_r2($name, $val)
    {
        echo '<div>';
        echo htmlentities($name);
        echo '<pre>';
        print_r($val);
        echo '</pre>';
        echo "</div>\n";
    }
    print_r2('$_POST', isset($_POST) ? $_POST : '-');
    print_r2('$_GET', isset($_GET) ? $_GET : '-');
    print_r2('$_COOKIE', isset($_COOKIE) ? $_COOKIE : '-');
    print_r2('$_SESSION', isset($_SESSION) ? $_SESSION : '-');
    print_r2('$_REQUEST', isset($_REQUEST) ? $_REQUEST : '-');
    print_r2('$_SERVER', isset($_SERVER) ? $_SERVER : '-');
    print_r2('$_ENV', isset($_ENV) ? $_ENV : '-');
    print_r2('$_FILES', isset($_FILES) ? $_FILES : '-');
    print_r2('$GLOBALS', isset($GLOBALS) ? $GLOBALS : '-');
    print_r2('php://input', file_get_contents('php://input'));
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
