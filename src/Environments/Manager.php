<?php

namespace Sqlia\Environments;

use RuntimeException;
use Illuminate\Container\Container;

class Manager
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function all()
    {
        return array_map([$this->container, "make"], [
            Linux::class,
            MacOS::class,
        ]);
    }

    public function detect()
    {
        foreach ($this->all() as $env) {
            if ($env->supported()) {
                return $env;
            }
        }

        throw new RuntimeException("Your environment is not yet supported.");
    }
}
