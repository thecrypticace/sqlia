<?php

namespace Sqlia\Drivers;

use Sqlia\Cli\Cli;
use Sqlia\Disks\Disk;
use Sqlia\Environments\Environment;

abstract class AbstractDriver implements Driver
{
    protected $cli;
    protected $env;
    protected $disk;

    public function __construct(Cli $cli, Disk $disk, Environment $env)
    {
        $this->cli = $cli;
        $this->env = $env;
        $this->disk = $disk;
    }

    protected function path()
    {
        return "{$this->disk->root()}/" . strtolower($this->name());
    }
}
