<?php

namespace Sqlia\Environments;

use Sqlia\Disks;

class MacOS extends AbstractEnvironment
{
    public function disk() : Disks\Disk
    {
        return $this->container->make(Disks\HfsPlus::class);
    }

    public function supported() : bool
    {
        return stripos($this->os(), "Darwin") !== false;
    }

    public function serverMemoryUsage() : int
    {
        $amounts = $this->cli->run("ps -caxm -orss");
        $amounts = explode("\n", $amounts);
        $amounts = array_slice($amounts, 1); // Remove first "RSS" line
        $usage = array_sum($amounts);

        // NOTE: RSS (resident set size) is reported in 1KiB units
        return $usage * 1024;
    }
}
