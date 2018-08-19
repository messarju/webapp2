<?php
try {
    $CROND = "cron";

    foreach (new DirectoryIterator($CROND) as $fi) {
        if ($fi->isDot()) {
            continue;
        } else if ($fi->isFile()) {
        } else if ($fi->isDir()) {

        }
    }

} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
