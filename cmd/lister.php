<?php
define('S_IFMT', 0170000); // mask for all types
define('S_IFSOCK', 0140000); // type: socket
define('S_IFLNK', 0120000); // type: symbolic link
define('S_IFREG', 0100000); // type: regular file
define('S_IFBLK', 0060000); // type: block device
define('S_IFDIR', 0040000); // type: directory
define('S_IFCHR', 0020000); // type: character device
define('S_IFIFO', 0010000); // type: fifo
define('S_ISUID', 0004000); // set-uid bit
define('S_ISGID', 0002000); // set-gid bit
define('S_ISVTX', 0001000); // sticky bit
define('S_IRWXU', 00700); // mask for owner permissions
define('S_IRUSR', 00400); // owner: read permission
define('S_IWUSR', 00200); // owner: write permission
define('S_IXUSR', 00100); // owner: execute permission
define('S_IRWXG', 00070); // mask for group permissions
define('S_IRGRP', 00040); // group: read permission
define('S_IWGRP', 00020); // group: write permission
define('S_IXGRP', 00010); // group: execute permission
define('S_IRWXO', 00007); // mask for others permissions
define('S_IROTH', 00004); // others: read permission
define('S_IWOTH', 00002); // others: write permission
define('S_IXOTH', 00001); // others: execute permission

function typeCode($m)
{
    switch ($m) {
        case S_IFREG:return 'f';
        case S_IFDIR:return 'd';
        case S_IFLNK:return 'l';
        case S_IFBLK:return 'b';
        case S_IFCHR:return 'c';
        case S_IFIFO:return 'p';
        case S_IFSOCK:return 's';
        default:return '?';
    }
}
function permsCode($m)
{
    $s = array();
    // set user permissions
    $s[0] = ($m & S_IRUSR) ? 'r' : '-';
    $s[1] = ($m & S_IWUSR) ? 'w' : '-';
    $s[2] = ($m & S_IXUSR) ? (($m & S_ISUID) ? 's' : 'x') : (($m & S_ISUID) ? 'S' : '-');
    // set group permissions
    $s[3] = ($m & S_IRGRP) ? 'r' : '-';
    $s[4] = ($m & S_IWGRP) ? 'w' : '-';
    $s[5] = ($m & S_IXGRP) ? (($m & S_ISGID) ? 's' : 'x') : (($m & S_ISGID) ? 'S' : '-');
    // set other permissions
    $s[6] = ($m & S_IROTH) ? 'r' : '-';
    $s[7] = ($m & S_IWOTH) ? 'w' : '-';
    $s[8] = ($m & S_IXOTH) ? (($m & S_ISVTX) ? 't' : 'x') : (($m & S_ISVTX) ? 'T' : '-');
    return join('', $s);
}

function mode_string($m)
{
    $s = array();

    // set type letter
    if ($m == S_IFBLK) {
        $m = 'b';
    } elseif ($m == S_IFCHR) {
        $m = 'c';
    } elseif ($m == S_IFDIR) {
        $m = 'd';
    } elseif ($m == S_IFREG) {
        $m = '-';
    } elseif ($m == S_IFIFO) {
        $m = 'p';
    } elseif ($m == S_IFLNK) {
        $m = 'l';
    } elseif ($m == S_IFSOCK) {
        $m = 's';
    }

    // set user permissions
    $s[1] = ($m & S_IRUSR) ? 'r' : '-';
    $s[2] = ($m & S_IWUSR) ? 'w' : '-';
    $s[3] = ($m & S_IXUSR) ? 'x' : '-';

    // set group permissions
    $s[4] = ($m & S_IRGRP) ? 'r' : '-';
    $s[5] = ($m & S_IWGRP) ? 'w' : '-';
    $s[6] = ($m & S_IXGRP) ? 'x' : '-';

    // set other permissions
    $s[7] = ($m & S_IROTH) ? 'r' : '-';
    $s[8] = ($m & S_IWOTH) ? 'w' : '-';
    $s[9] = ($m & S_IXOTH) ? 'x' : '-';

    // adjust execute letters for set-uid, set-gid, and sticky
    if ($m & S_ISUID) {
        if ($s[3] != 'x') {
            // set-uid but not executable by owner
            $s[3] = 'S';
        } else {
            $s[3] = 's';
        }
    }

    if ($m & S_ISGID) {
        if ($s[6] != 'x') {
            // set-gid but not executable by group
            $s[6] = 'S';
        } else {
            $s[6] = 's';
        }
    }

    if ($m & S_ISVTX) {
        if ($s[9] != 'x') {
            // sticky but not executable by others
            $s[9] = 'T';
        } else {
            $s[9] = 't';
        }
    }
    // return formatted string
    return join('', $s);
}

function size_max($dir)
{
    $n = 0;
    foreach ($dir as $sub) {
        $n = max($sub->data->size, $n);
        if ($sub instanceof DirectoryItem) {
            $n = max(size_max($sub), $n);
        }
    }
    return $n;
}
class Lister
{
    public function pathOf($x)
    {
        $names = array();
        while ($x->parent) {
            array_push($names, $x->data->name);
            $x = $x->parent;
        }
        return implode("/", array_reverse($names));
    }
}

class Ls extends Lister
{

    public function line($sub)
    {
        $D    = $sub->data;
        $type = typeCode($D->type);
        if ($this->usePerms) {
            $perms = $D->perms;
            $perms = ($perms === null) ? '' : permsCode($perms);
        } else {
            $perms = '';
        }
        if ($this->useUId) {
            $uid = $D->uid;
        } else {
            $uid = null;
        }
        if ($this->useGId) {
            $gid = $D->gid;
        } else {
            $gid = null;
        }
        if ($uid !== null) {
            if ($gid !== null) {
                $ogid = $uid . ':' . $gid;
            } else {
                $ogid = $uid . ':';
            }
        } else if ($gid !== null) {
            $ogid = ':' . $gid;
        } else {
            $ogid = '.';
        }

        if ($this->useSize) {
            $size = $D->size;
            $size = ($size === null) ? '.' : $size;
        } else {
            $size = '.';
        }
        if ($this->useMTime) {
            $mtime = $D->mtime;
            $mtime = ($mtime === null) ? '.' : date(DATE_ATOM, $mtime);
        } else {
            $mtime = '.';
        }
        if ($this->useHash == 'md5') {
            $hash = $D->md5sum;
            if ($hash === null) {
                $hash = '.';
            }
        } else if ($this->useHash == 'sha1') {
            $hash = $D->sha1sum;
            if ($hash === null) {
                $hash = '.';
            }
        } else {
            $hash = '.';
        }
        $path = $this->pathOf($sub);
        $out  = $this->out;
        $out($this, $path, $type, $perms, $ogid, $size, $mtime, $hash);
    }

    public function walk($dir)
    {
        foreach ($dir as $sub) {
            $this->line($sub);
            if ($sub instanceof DirectoryItem) {
                $this->walk($sub);
            }
        }
    }
    public function start($path, $req)
    {
        $this->useMTime = isset($req['mtime']) ? boolval($req['mtime']) : true;
        $this->useUId   = isset($req['uid']) ? boolval($req['uid']) : true;
        $this->useGId   = isset($req['gid']) ? boolval($req['gid']) : true;
        $this->useSize  = isset($req['size']) ? boolval($req['size']) : true;
        $this->usePerms = isset($req['perms']) ? boolval($req['perms']) : true;
        $this->useHash  = isset($req['hash']) ? $req['hash'] : false;
        $this->dirHash  = isset($req['hashd']) ? $req['hashd'] : false;
        $pad            = isset($req['pad']) ? boolval($req['pad']) : true;
        $i              = new DirectoryItem($path);
        if ($pad) {
            $this->padSize = 1;
            $this->padOGId = 1;
            $this->padHash = 1;
            $this->padMode = 0;
            $this->out     = function ($that, $path, $type, $perms, $ogid, $size, $mtime, $hash) {
                if (($n = strlen($size)) > $that->padSize) {
                    $that->padSize = $n;
                }
                if (($n = strlen($hash)) > $that->padHash) {
                    $that->padHash = $n;
                }
                if (($n = strlen($ogid)) > $that->padOGId) {
                    $that->padOGId = $n;
                }
                if ($perms) {
                    if (($n = strlen($perms)) > $that->padMode) {
                        $that->padMode = $n;
                    }
                }

            };
            $this->walk($i);
            $this->out = function ($that, $path, $type, $perms, $ogid, $size, $mtime, $hash) {
                echo $type, $perms ? ($that->padMode ? str_pad($perms, $that->padMode, " ") : $perms) : '', ' '
                , str_pad($ogid, $that->padOGId, " ", STR_PAD_LEFT), ' '
                , str_pad($size, $that->padSize, " ", STR_PAD_LEFT), ' '
                , $mtime, ' '
                , str_pad($hash, $that->padHash, " ", STR_PAD_LEFT), ' '
                , $path, "\n";
            };
        } else {
            $this->out = function ($that, $path, $type, $perms, $ogid, $size, $mtime, $hash) {
                echo $type, $perms, ' ', $ogid, ' ', $size, ' ', $mtime, ' ', $hash, ' ', $path, "\n";
            };
        }
        $this->walk($i);

    }
}
