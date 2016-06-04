<?php

namespace Sqlia\Environments;

use Closure;
use Sqlia\Disks;

interface Environment
{
    public function disk() : Disks\Disk;
    public function supported() : bool;
    public function measure(Closure $callback);
    public function measureAndReport(Closure $callback);
    public function scriptMemoryUsage() : int;
    public function serverMemoryUsage() : int;
}
