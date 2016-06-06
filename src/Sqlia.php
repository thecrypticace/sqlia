<?php

namespace Sqlia;

use Silly\Application;
use Illuminate\Container\Container;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Sqlia
{
    const VERSION = "0.0.0";

    private $app;
    private $container;

    public function __construct(Application $app, Container $container)
    {
        $this->app = $app;
        $this->container = $container;
    }

    public static function setup()
    {
        // Setup Silly
        $app = new Application("Sqlia", static::VERSION);

        // Setup the DI container
        $container = new Container;
        $container->instance(Application::class, $app);
        $container->instance(Container::class, $container);

        Container::setInstance($container);

        return $container->make(static::class)->boot();
    }

    protected function boot()
    {
        $this->container->singleton(Drivers\Manager::class);
        $this->container->singleton(Environments\Manager::class);
        $this->container->bind(OutputInterface::class, ConsoleOutput::class);

        $this->container->bind(Environments\Environment::class, function ($app) {
            return $app->make(Environments\Manager::class)->detect();
        });

        $this->container->bind(Disks\Disk::class, function ($app) {
            return $app->make(Environments\Environment::class)->disk();
        });

        return $this->register([
            Console\Stop::class,
            Console\Start::class,
            Console\Status::class,
            Console\Refresh::class,
            Console\Supported::class,
        ]);
    }

    protected function register($classes)
    {
        foreach ((array) $classes as $class) {
            $command = $this->container->make($class);

            $cli = $this->app->command($command->signature, [$command, "handle"]);
            $cli->descriptions($command->description, $command->descriptions());
            $cli->defaults($command->defaults());
        }

        return $this;
    }

    public function run()
    {
        $this->app->run();

        return $this;
    }
}
