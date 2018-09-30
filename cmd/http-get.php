<?php
try {
    if (isset($_REQUEST['url']) && ($url = $_REQUEST['url'])) {

        if (isset($_REQUEST['curl'])) {
            // create a new cURL resource
            $ch = curl_init();

            // set URL and other appropriate options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);

            // grab URL and pass it to the browser
            curl_exec($ch);

            // close cURL resource, and free up system resources
            curl_close($ch);
        } else {

            $useragent = isset($_REQUEST['useragent']) ? $_REQUEST['useragent'] : $_SERVER['HTTP_USER_AGENT'];
            $opts = array(
                'http' => array(
                    'method'     => "GET",
                ),
            );
            if (isset($_POST['header']) && !empty($_POST['header'])) {
                $opts['http']['headers'] = $_POST['header'];
            }

            if (isset($_REQUEST['useragent'])) {
                $useragent = $_REQUEST['useragent'];
                if ('.' == $useragent) {
                    $opts['http']['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                } else if ('-' != $useragent) {
                    $opts['http']['user_agent'] = $useragent;
                }
            } else {
                $opts['http']['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            }

            $context = stream_context_create($opts);

            if (!$fp = fopen($url, 'r', false, $context)) {
                trigger_error("Unable to open URL ($url)", E_USER_ERROR);
            }

            if (isset($_REQUEST['include']) && !empty($_REQUEST['include'])) {
                $meta = stream_get_meta_data($fp);
                foreach ($meta['wrapper_data'] as $value) {
                    echo ($value);
                    echo ("\r\n");
                }
                echo ("\r\n");
            }
            echo stream_get_contents($fp);
            fclose($fp);
        }
    } else {
        throw new RuntimeException('No URL');
    }
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
