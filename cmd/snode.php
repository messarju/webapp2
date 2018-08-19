<?php
class SNode implements IteratorAggregate
{
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
        return new SubNodeIterator($this);
    }
}

class SubNodeIterator implements Iterator
{
    public function __construct($node)
    {
        $this->first = $node->first;
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
        $this->current = $this->first;
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

class SPath
{
    public function __construct($path)
    {
        $this->path = $path;
    }
    public function __get($name)
    {
        if ($name === 'name') {
            return $this->$name = basename($this->path);
        } else if ($name === 'prior' || $name === 'first') {
            return $this->$name = null;
        } else if ($name === 'checksum') {
            return $this->$name = $this->checksumHook();
        } else if ($name === 'size' || $name === 'mtime' || $name === 'uid' || $name === 'gid' || $name === 'nlink' || $name === 'perms' || $name === 'type') {
            $s           = stat($this->path);
            $m           = $s[2];
            $this->type  = 0xf000 & $m;
            $this->perms = 0x0fff & $m;
            $this->mode  = $m;
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

class SDir extends SPath
{
    public function __get($name)
    {
        if ($name === 'first') {
            $first = null;
            $cur   = null;
            foreach (new DirectoryIterator($this->path) as $fi) {
                if ($fi->isDot()) {
                    continue;
                } else if ($fi->isDir()) {
                    $sub = new SDir($fi->getPathname());
                } else {
                    $sub = new SFile($fi->getPathname());
                }
                $sub->parent = $this;
                if ($cur === null) {
                    $cur   = $sub;
                    $first = $sub;
                } else {
                    $cur->next = $sub;
                    $cur       = $sub;
                }
            }
            $this->first = $first;
            return $first;
        }
        return parent::__get($name);
    }
}
