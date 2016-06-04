<?php

namespace Sqlia\Console;

use Sqlia\Cli\Cli;
use Sqlia\Drivers\Manager;
use Illuminate\Container\Container;
use Sqlia\Environments\Environment;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractCommand implements Command
{
    protected $cli;
    protected $env;
    protected $container;

    public function __construct(Cli $cli, Environment $env, Manager $drivers, Container $container)
    {
        $this->cli = $cli;
        $this->env = $env;
        $this->drivers = $drivers;
        $this->container = $container;
    }

    protected function disableOutput()
    {
        $this->container->bind(OutputInterface::class, NullOutput::class);
    }

    public function descriptions()
    {
        return [];
    }

    public function defaults()
    {
        return [];
    }
}
