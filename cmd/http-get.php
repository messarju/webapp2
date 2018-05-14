<?php
try {
    if ($url = $_REQUEST['url']) {

        if ($_REQUEST['curl']) {
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
            $opts = array(
                'http' => array(
                    'method'     => "GET",
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                ),
            );

            $context = stream_context_create($opts);

            if (!$fp = fopen($url, 'r', false, $context)) {
                trigger_error("Unable to open URL ($url)", E_USER_ERROR);
            }

            $meta = stream_get_meta_data($fp);
            foreach ($meta['wrapper_data'] as $value) {
                echo ($value);
                echo ("\r\n");
            }
            echo ("\r\n");
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
