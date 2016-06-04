<?php

namespace Sqlia\Cli;

class Cli
{
    public function run($command)
    {
        return $this->command()->run($command);
    }

    public function __call($name, $args)
    {
        return $this->command()->$name(...$args);
    }

    private function command()
    {
        return new Command();
    }
}
