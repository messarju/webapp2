<?php
try {
    include_once 'item.php';
    include_once 'lister.php';

    class GitTreeIterator extends ArrayIterator
    {
        public function __construct($url, $token)
        {
            // echo $url, $token;
            $opts = array(
                'http' => array(
                    'method'     => "GET",
                    'user_agent' => 'curl/7.37.0',
                    'headers'    => array("Authorization: token " . $token),
                ),
            );
            $context = stream_context_create($opts);
            if (!$fp = fopen($url, 'r', false, $context)) {
                trigger_error("Unable to open URL ($url)", E_USER_ERROR);
            }
            $d = stream_get_contents($fp);
            fclose($fp);
            // echo ($d);
            $d = json_decode($d);
            parent::__construct($d->tree);
        }
        public function current()
        {
            $cur = parent::current();
            // echo json_encode($cur);
            return new Item(new GitHubData($cur));
        }
    }

    class GitHubData implements IteratorAggregate
    {
        public static $token = "?";
        public function __construct($obj)
        {
            if (is_string($obj)) {
                $this->url = $obj;
            } else {
                $m           = intval($obj->mode, 8);
                $this->url   = $obj->url;
                $this->name  = $obj->path;
                $this->mode  = $m;
                $this->type  = 0xf000 & $m;
                $this->perms = 0x0fff & $m;
                $this->kind  = $obj->type;
                if ($this->kind == 'tree') {
                    $this->size = null;
                } else {
                    $this->size = intval($obj->size);
                }
                $this->githash = $obj->sha;
            }
        }
        public function getIterator()
        {
            // echo 'getIterator', __LINE__, "\n";
            return new GitTreeIterator($this->url, $this::$token);
        }
    }

    GitHubData::$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
    $tree           = new Item(new GitHubData(isset($_REQUEST['url']) ? $_REQUEST['url'] : ''));
    // echo GitHubData::$token, __LINE__, "\n";
    // foreach ($tree as $sub) {
    //     // echo GitHubData::$token, __LINE__, "\n";
    //     echo $sub->data->name, "\n";
    // }
    $ls = new Ls();
    $req = array('mtime' => false, 'uid' => false, 'gid' => false, 'size' => true, 'perms' => true, 'hash' => 'githash', 'pad' => true);
    $ls->start($tree, $req);
} catch (Exception $e) {
    header("HTTP/1.0 500 Internal Server Error");
    echo $e->getMessage();
}
