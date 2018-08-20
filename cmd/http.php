<?php
try {

	$url = null;
	$include = null;
	$use_md5 = null;
	$use_status_code = null;
	$use_content_type = null;
	$hide_status = null;
	$headers = null;
	$opts = array(
		'http' => array(
			'method' => "GET",
			'header' => [],
		),
	);

	foreach (array(&$_GET, &$_POST) as &$REQ) {
		//  var_dump($REQ);
		foreach ($REQ as $key => $value) {
			// echo "{$key} => {$value}\n";
			switch ($key) {
				case 'url':
					$url =  $value;
					break;
				case 'method':
					$opts['http']['method'] =  strtoupper($value);
					break;
				case 'include':
					$include =  (bool)(int)$value;
					break;
				case 'md5':
					$use_md5 =  "MD5";
					break;
				case 'hide_status':
					$hide_status =  (bool)(int)$value;
					break;
				case 'use_status_code':
					$use_status_code =  (int)$value;
					break;
				case 'use_content_type':
					$use_content_type = $value;
					break;
				case 'allow_redirects':
					$opts['http']['follow_location'] = (int)(bool)(int)$value;
					break;
				case 'timeout':
					$opts['http']['timeout'] = (int)$value;
					break;
				case 'content':
				case 'data':
					$opts['http']['content'] = $value;
					break;
				case 'user_agent':
					if($value === '.'){
						$opts['http']['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
					}elseif ($value === '-') {
						unset( $opts['http']['user_agent']);
					}elseif ($value === '-') {
						$opts['http']['user_agent'] = $value;
					}
				default:
					if( substr( $key, strlen( $key ) - 1 ) === ':'){
						array_push($opts['http']['header'], "$key $value");
					}
			}
		}
	}

	foreach ($_FILES as $key => $value) {
		if($key === 'content' || $key === 'data'){
			$opts['http']['content'] = file_get_contents($value['tmp_name']);
		}
	}

	$context = stream_context_create($opts);

	if (!$fp = fopen($url, 'r', false, $context)) {
		trigger_error("Unable to open URL ($url)", E_USER_ERROR);
	}

	if ($include) {
		$meta = stream_get_meta_data($fp);
		foreach ($meta['wrapper_data'] as $value) {
			echo ($value);
			echo ("\r\n");
		}
		if($use_content_type){
			echo("Content-Type: $use_content_type\r\n");
		}
		if($use_md5){

		}else{

		}
		echo ("\r\n");
		echo stream_get_contents($fp);
		fclose($fp);
	}else{
		echo stream_get_contents($fp);
		fclose($fp);
	}

} catch (Exception $e) {
	header("HTTP/1.0 500 Internal Server Error");
	echo $e->getMessage();
}
