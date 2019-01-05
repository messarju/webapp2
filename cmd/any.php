<?php
try {

	$use_md5 = null;
	$use_status_code = null;
	$use_content_type = null;
	$hide_status = null;
	$headers = null;
	$len = 0;

	foreach (array(&$_GET, &$_POST) as &$REQ) {
		//  var_dump($REQ);
		foreach ($REQ as $key => $value) {
			// echo "{$key} => {$value}\n";
			switch ($key) {
				case 'md5':
					$use_md5 =  "MD5";
					break;
				case 'hide_status':
					$hide_status =  (bool)(int)$value;
					break;
				case 'use_status_code':
					$use_status_code =  (int)$value;
					break;
				case 'len':
					$len =  (int)$value;
					break;
				case 'use_content_type':
					$use_content_type = $value;
					break;
				case 'timeout':
					$opts['http']['timeout'] = (int)$value;
					break;
			}
		}
	}
	if($use_content_type){
		header("Content-Type: $use_content_type");
	}
	header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
	header('Last-Modified: {now} GMT');
	header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
	if(!$len){
		$len=128*1024;
	}
	$x = ceil(($len/4.0)*3);
	$data=random_bytes ( $x );
	echo substr ( base64_encode($data), 0, $len);

} catch (Exception $e) {
	header("HTTP/1.0 500 Internal Server Error");
	echo $e->getMessage();
}

/*
mee --cd /mnt/KABAN/fav/asa-meshi-mae -- php -S localhost:8700
*/
