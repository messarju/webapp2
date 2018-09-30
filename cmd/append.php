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

    if (!($h = fopen($path, "a"))) {
        throw new RuntimeException("Failed to open `$path'");
    } else {
        if (isset($md5)) {
        	$md5_cur = md5($data)
            if ($md5_cur != $md5) {
                throw new RuntimeException("Expected md5 '$md5' not '$md5_cur'");
            }
        }
        if (isset($offset)) {
        	$size = filesize($path)
        	if($offset > $size){

        	}else if($offset < $size){

        	}
        }

		$data_size = strlen($data);
		$offset = 0;
		$block_size = 128*1024;

        fwrite($data);
        fclose($h);
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
