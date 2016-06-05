<?php

namespace Sqlia\Drivers;

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
            MySQL::class,
            Postgres::class,
        ]);
    }

    public function named($name)
    {
        foreach ($this->all() as $driver) {
            if (strtolower($driver->name()) === strtolower($name)) {
                return $driver;
            }
        }

        throw new RuntimeException("The driver “{$name}” does not exist");
    }
}
