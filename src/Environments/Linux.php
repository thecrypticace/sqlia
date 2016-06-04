<?php

namespace Sqlia\Environments;

use Sqlia\Disks;

class Linux extends AbstractEnvironment
{
    public function disk() : Disks\Disk
    {
        return $this->container->make(Disks\TmpFs::class);
    }

    public function supported() : bool
    {
        return stripos($this->os(), "Linux") !== false;
    }

    public function serverMemoryUsage() : int
    {
        return $this->serverMemoryTotal() - $this->serverMemoryFree();
    }

    private function serverMemoryTotal()
    {
        $amount = $this->cli->run("cat /proc/meminfo | grep \"MemTotal\" | awk '{print $2}'");

        return $amount * 1000;
    }

    private function serverMemoryFree()
    {
        $amount = $this->cli->run("cat /proc/meminfo | grep \"MemFree\" | awk '{print $2}'");

        return $amount * 1000;
    }
}
