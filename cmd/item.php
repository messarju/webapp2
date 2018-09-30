<?php
class Item implements IteratorAggregate
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        if ($name === 'first') {
            $first = null;
            if ($this->data instanceof IteratorAggregate) {
                $cur = null;
                foreach ($this->data as $sub) {
                    //   echo ("[" . $sub->data->path . "]\n");
                    assert($sub->parent === null);
                    $sub->parent = $this;
                    if ($cur === null) {
                        $cur   = $sub;
                        $first = $sub;
                    } else {
                        $cur->next = $sub;
                        $cur       = $sub;
                    }
                }
            }
            $this->first = $first;
            return $first;
        } else if ($name === 'next') {
            return $this->$name = null;
        } else if ($name === 'parent') {
            return $this->$name = null;
        }
        throw new InvalidArgumentException($name);
    }
    public function getIterator()
    {
        return new SubItemIterator($this);
    }
}

class SubItemIterator implements Iterator
{
    public function __construct($item)
    {
        $this->item = $item;
        $this->rewind();
    }
    public function current()
    {
        // echo ("(" . $this->current->data->path . ")\n");
        assert($this->item === $this->current->parent);
        return $this->current;
    }
    public function key()
    {
        return null;
    }
    public function next()
    {
        $this->current = $this->current->next;
    }
    public function rewind()
    {
        $this->current = $this->item->first;
    }
    public function valid()
    {
        return $this->current != null;
    }
}

class ItemDirectoryIterator extends DirectoryIterator
{
    public function __construct($path)
    {
        parent::__construct($path);
    }
    public function current()
    {
        $cur = parent::current();
        if ($cur->isDir()) {
            return new DirectoryItem($cur->getPathname());
        } else {
            return new FileItem($cur->getPathname());
        }
    }
    public function valid()
    {
        while (parent::valid()) {
            $cur = parent::current();
            // echo ("{" . $cur->getPathname() . ' ' . $cur->isDot() . "}\n");
            if ($cur->isDot()) {
                parent::next();
            } else {
                return true;
            }
        }
        return false;
    }
}

class PathData
{
    public function __construct($path)
    {
        $this->path = $path;
    }
    public function __get($name)
    {
        if ($name === 'name') {
            return $this->$name = basename($this->path);
        } else if ($name === 'prior') {
            return $this->$name = null;
        } else if ($name === 'md5sum') {
            return $this->$name = md5_file($this->path);
        } else if ($name === 'sha1sum') {
            return $this->$name = sha1_file($this->path);
        } else if ($name === 'size' || $name === 'mtime' || $name === 'uid' || $name === 'gid' || $name === 'nlink' || $name === 'perms' || $name === 'type') {
            $s           = stat($this->path);
            $m           = $s[2];
            $this->type  = 0xf000 & $m;
            $this->perms = 0x0fff & $m;
            $this->mode = $m;
            $this->nlink = $s[3];
            $this->uid   = $s[4];
            $this->gid   = $s[5];
            $this->mtime = $s[9];
            if ($this->type == 0x4000) {
                if ($name == 'size') {
                    $this->$name = null;
                }
            } else {
                $this->size = $s[7];
            }
            return $this->$name;
        }
        throw new InvalidArgumentException($name);
    }
}

class DirectoryData extends PathData implements IteratorAggregate
{
    public function getIterator()
    {
        return new ItemDirectoryIterator($this->path);
    }
    public function __get($name)
    {
        if ($name === 'md5sum') {
            return $this->$name = null;
        } else if ($name === 'sha1sum') {
            return $this->$name = null;
        } else if ($name === 'size') {
            return $this->$name = null;
        } else if ($name === 'githash') {

        }
        return parent::__get($name);
    }
}

class FileData extends PathData
{
    public function __get($name)
    {
        if ($name === 'md5sum') {
            return $this->$name = md5_file($this->path);
        } else if ($name === 'sha1sum') {
            return $this->$name = sha1_file($this->path);
        } else if ($name === 'githash') {
            return $this->$name = sha1('blob ' . $this->size . "\0" . file_get_contents($this->path));
        }
        return parent::__get($name);
    }
}

class PathItem extends Item
{

}

class FileItem extends PathItem
{
    public function __construct($path)
    {
        $this->data = new FileData($path);
    }
}

class DirectoryItem extends PathItem
{
    public function __construct($path)
    {
        $this->data       = new DirectoryData($path);
        $this->data->size = null;
    }
}
