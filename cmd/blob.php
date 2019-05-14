<?php
try {

	$what = null;

	foreach (array(&$_GET, &$_POST) as &$REQ) {
		//  var_dump($REQ);
		foreach ($REQ as $key => $value) {
			// echo "{$key} => {$value}\n";
			switch ($key) {
				case 'what':
					$what =  $value;
					break;
			}
		}
	}
	switch ($what) {
		case 'add':

			$what =  $value;
			break;
	}


function add($db_dir, $sha1, $content)
{


}


} catch (Exception $e) {
	header("HTTP/1.0 500 Internal Server Error");
	echo $e->getMessage();
}

/*
mee --cd /mnt/KABAN/fav/asa-meshi-mae -- php -S localhost:8700
*/
