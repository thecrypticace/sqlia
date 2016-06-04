<?php

namespace Sqlia\Disks;

use Sqlia\Cli\Cli;

abstract class AbstractDisk implements Disk
{
    protected $cli;
    private $root;

    public function __construct(Cli $cli, $root = null)
    {
        $this->cli = $cli;
        $this->root = $root ?: "/tmp/sqlia";
    }

    public function root() : string
    {
        return $this->root;
    }
}
