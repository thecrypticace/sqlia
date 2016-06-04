<?php

namespace Sqlia\Disks;

interface Disk
{
    public function root() : string;
    public function mount(/* array | \ArrayAccess */ $options = []) : bool;
    public function mounted(/* array | \ArrayAccess */ $options = []) : bool;
    public function unmount(/* array | \ArrayAccess */ $options = []) : bool;
}
